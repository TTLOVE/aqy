<?php

namespace Controller;
use Leaf\Loger\LogDriver;
use Model\ReturnMoney;
use Model\Borrow;
use Model\Product;
use Model\KoalaPay;
use Model\KlApi;
use Model\Application;
use Model\Message;

/**
 * Class WeixinRepaymentController 商家还款管理控制器
 * @author xiaozhu
 */
class WeixinRepaymentController  extends WeixinBaseController
{
    /**
     * 还款方式：余额还款
     */
    CONST REPAYMENT_TYPE_BALANCE = '01';
    /**
     * 还款方式：银行卡
     */
    CONST REPAYMENT_TYPE_BANK = '02';
    /**
     * 还款标志：还当期（逾期+当期）
     */
    CONST REPAYMENT_TAG_NOW = '01';
    /**
     * 还款标志：还清所有（逾期+当期+提前还款）
     */
    CONST REPAYMENT_TAG_ALL = '02';

    /**
        * 还款管理页面
     */
    public function repaymentHome()
    {
        // 获取待还金额
        $repaymentModel = new ReturnMoney();
        $repaymentMoney = $repaymentModel->getStoreRepaymentMoney($this->storeId);

        // 获取对应月份待还列表
        $theMonth = isset($_GET['month']) ? intval($_GET['month']) : date('m');
        $theYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $monthlyTime = $repaymentModel->getTheMonthTime($theYear, $theMonth);
        $repaymentList = $repaymentModel->getStoreRepaymentList($this->storeId, $monthlyTime['begin_time'], $monthlyTime['end_time'], [ReturnMoney::RETURN_STATUS_UNPAY, ReturnMoney::RETURN_STATUS_OVERDUE_UNPAY]);
        $usuryIdArr = [];
        // 设置剩余天数
        if ( !empty($repaymentList) ) {
            foreach ($repaymentList as $key => $repayment) {
                $repaymentList[ $key ] ['days'] = $repaymentModel->dealWithOverdueDays($repayment['repayment_time']);
                if ( $repayment['repayment_status']==3 ) {
                    $repaymentList[ $key ] ['days'] += 1;
                }
                $repaymentList[ $key ]['money'] = number_format($repayment['repayment_capital']/100, 2, ".", "");
                $repaymentList[ $key ]['return_fee'] = number_format(($repayment['repeyment_interest']+$repayment['repayment_penalty']+$repayment['repayment_ahead_fee']+$repayment['repayment_mng_fee'])/100, 2, ".", "");
            }
            // 读取借款信息
            $borrowInfo = (new Borrow())->getBorrowDetail($repaymentList[0]['borrow_money_log_id']);
        } else {
            $borrowInfo = [];
        }

        // 去钱包获取用户金额
        $userMoneyData = (new KoalaPay())->getUserMoney($this->storeId);
        if ( isset($userMoneyData['status']) && $userMoneyData['status']==1 ) {
            $balance = number_format(($userMoneyData['data']['book_balance'] - $userMoneyData['data']['withdraw']), 2, ".", "");
        } else {
            $balance = 0.00;
        }

        $dateInfo = [
            'year' => $theYear,
            'month' => $theMonth
        ];
        $this->view = $this->myView->make('repayment.repaymentManage')
            ->with('repaymentMoney', $repaymentMoney)
            ->with('repaymentList', $repaymentList)
            ->with('balance', $balance)
            ->with('borrowInfo', $borrowInfo)
            ->with('dateInfo', $dateInfo);
    }

    /**
     * 选择还款方式进行还款
     */
    public function repaymentWay()
    {
        $repaymentId = isset($_GET['repayment_id']) ? intval($_GET['repayment_id']) : 0;
        $repayTag = isset($_GET['repay_tag']) ? strval($_GET['repay_tag']) : '01';
        if ( empty($repaymentId) ) {
            echo "<script>alert('没有对应还款记录');history.go(-1);</script>";
            exit();
        }

        // 读取还款信息
        $repaymentInfo = (new ReturnMoney())->getRepaymentInfo($repaymentId);
        if ( empty($repaymentInfo) || $repaymentInfo['store_id']!=$this->storeId ) {
            echo "<script>alert('没有查到对应还款记录');history.go(-1);</script>";
            exit();
        }

        // 读取还款信息带上产品信息
        $borrowModel = new Borrow();
        $returnDetail = $borrowModel->getReturnDetail($repaymentInfo['borrow_money_log_id']);
        if ( empty($returnDetail) ) {
            echo "<script>alert('没有对应借款记录');history.go(-1);</script>";
            exit();
        }

        // 请求钱包获取模拟还款信息
        $returnMoney = $repaymentInfo['repayment_capital']+$repaymentInfo['repeyment_interest']+$repaymentInfo['repayment_penalty']+$repaymentInfo['repayment_ahead_fee']+$repaymentInfo['repayment_mng_fee'];
        $notifyUrl = HOST . "/notify/repayFY";

        $returnData = (new KoalaPay())->tryToReturnMoney($this->storeId, $notifyUrl, $returnMoney, self::REPAYMENT_TYPE_BALANCE, 
            $returnDetail['loan_id'], $repayTag);
        if ( isset($returnData['status']) && $returnData['status']==1 ) {
            $repayment = (isset($returnData['result_obj']['rspCode']) && $returnData['result_obj']['rspCode']=='0000') ? $returnData['result_obj'] : [];
        } else {
            $repayment = [];
        }
        if ( empty($repayment) ) {
            (new LogDriver())->error('repay', "请求模拟还款接口,loadId:" . $returnDetail['loan_id'] . ",商家id:" . $this->storeId . ",返回数据:" . json_encode($returnData));
            echo "<script>alert('还款信息不存在');history.go(-1);</script>";
            exit();
        }

        $this->view = $this->myView->make('repayment.repaymentWay')
            ->with('returnDetail', $returnDetail)
            ->with('repayment', $repayment)
            ->with('storeId', $this->storeId)
            ->with('repayTag', $repayTag)
            ->with('repaymentInfo', $repaymentInfo);
    }

    /**
     * 请求进行还款
     */
    public function ajaxReturnMoney()
    {
        $returnMoney = floatval($_POST['money']);
        $repayType = strval($_POST['repay_type']);
        $loanId = strval($_POST['loan_id']);
        $repayTag = strval($_POST['repay_tag']);
        $borrowId = intval($_POST['borrowId']);
        $period = intval($_POST['period']);
        if ( empty($returnMoney) || empty($loanId) || empty($repayTag) || empty($repayType) || empty($borrowId) || empty($period) ) {
            $this->echoJson(1506478575, '请求信息不能为空');
        }

        $notifyUrl = HOST . "/notify/repayFY?borrowId=" . $borrowId . '&period=' . $period;
        $returnData = (new KoalaPay())->returnMoney($this->storeId, $notifyUrl, $returnMoney, $repayType, 
            $loanId, $repayTag);
        if ( isset($returnData['status']) && $returnData['status']==1 ) {
            $repayment = isset($returnData['result_obj']) ? $returnData['result_obj'] : [];
        } else {
            $repayment = [];
        }
        if ( empty($repayment) ) {
            $this->echoJson(1506496336, '请求还款失败');
        } else {
            $this->echoJson(0, '请求还款成功', $repayment);
        }
        
    }

    /**
        * 还款成功页面
     */
    public function repaymentSuccess()
    {
        $borrowId = isset($_GET['borrowId']) ? intval($_GET['borrowId']) : 0;
        $period = isset($_GET['period']) ? intval($_GET['period']) : 0;
        $this->view = $this->myView->make('repayment.repaymentSuccess');
        // 发送还款消息
        if ( $borrowId>0 ) {
            // 读取还款信息
            $repaymentInfo = (new ReturnMoney())->getRepaymentInfoByBorrowIdAndPeriod($borrowId, $period);
            if ( $repaymentInfo['realpay_capital']>0 ) {
                // 如果已经回调更新还款信息
                $allMoney = $repaymentInfo['realpay_capital']+$repaymentInfo['realpay_interest']+$repaymentInfo['realpay_penalty']+$repaymentInfo['realpay_ahead_fee']+$repaymentInfo['realpay_mng_fee'];
            } else {
                // 如果未更新还款信息
                $allMoney = $repaymentInfo['repayment_capital']+$repaymentInfo['repeyment_interest']+$repaymentInfo['repayment_penalty']+$repaymentInfo['repayment_ahead_fee']+$repaymentInfo['repayment_mng_fee'];
            }
            $allMoney *= MONEY_RATIO;
            $tplContentArray = [
                [
                    "type" => "text",
                    "m" => "还款成功",
                    "s" => "3",
                    "c" => "#333333",
                    "p" => "left"
                ],
                [
                    "type" => "text",
                    "m" => date("Y-m-d H:i:s"),
                    "s" => "2",
                    "c" => "#333333",
                    "p" => "left"
                ],
                [ 
                    "type" => "fields",
                    "f1_m" => "总还款额:    ",
                    "f2_m" => '' . $allMoney . '',
                    "f1_s" => "2",
                    "f1_c" => "#999999",
                    "f2_s" => "2",
                    "f2_c" => "#999999",
                ], 
                [ 
                    "type" => "fields",
                    "f1_m" => "产品名称:    ",
                    "f2_m" =>$repaymentInfo['product_name'] . '（' . $repaymentInfo['period'] .  '期）',
                    "f1_s" => "2",
                    "f1_c" => "#999999",
                    "f2_s" => "2",
                    "f2_c" => "#999999",
                ], 
                [ 
                    "type" => "fields",
                    "f1_m" => "还款类型:    ",
                    "f2_m" => $repaymentInfo['repayment_time']>time() ? '正常还款' : '逾期还款',
                    "f1_s" => "2",
                    "f1_c" => "#999999",
                    "f2_s" => "2",
                    "f2_c" => "#999999",
                ], 
                [ 
                    "type" => "fields",
                    "f1_m" => "还款方式:    ",
                    "f2_m" => '账户余额划扣',
                    "f1_s" => "2",
                    "f1_c" => "#999999",
                    "f2_s" => "2",
                    "f2_c" => "#999999",
                ], 
            ];
            $toUserArray = ['lq_' . $repaymentInfo['store_id']];
            $goUrl = HOST . '/weixin/repayment/history';
            $sendMsgData = (new Message())->sendMsgToApplication(BORROW_APP_ID, '还款成功', $toUserArray, $goUrl, $tplContentArray);
            if ( !isset($sendMsgData['my_status']) || $sendMsgData['my_status']!=1 ) {
                (new LogDriver())->error('repay', '还款成功发送消息失败!data : ' . json_encode($repaymentInfo) . ';result:'.json_encode($sendMsgData));
            }
        }
    }

    /**
        * 还款历史页面
     */
    public function repaymentHistory()
    {
        // 获取对应月份待还列表
        $theMonth = isset($_GET['month']) ? intval($_GET['month']) : date('m');
        $theYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $dateInfo = [
            'year' => $theYear,
            'month' => $theMonth
        ];

        $repaymentModel = new ReturnMoney();
        $monthlyTime = $repaymentModel->getTheMonthTime($theYear, $theMonth);
        $repaymentList = $repaymentModel->getStoreRepaymentList($this->storeId, $monthlyTime['begin_time'], $monthlyTime['end_time'], [ReturnMoney::RETURN_STATUS_PAY]);

        $configStatus = [
            2 => '正常还款',
        ];
        $configMethod = [
            2 => '按日计息，随借随还',
            3 => '等额本息，按月还款',
            5 => '等额本息，按日还款',
        ];
        $this->view = $this->myView->make('repayment.repaymentHistory')
            ->with('repaymentList', $repaymentList)
            ->with('configStatus', $configStatus)
            ->with('configMethod', $configMethod)
            ->with('dateInfo', $dateInfo);
    }

}
