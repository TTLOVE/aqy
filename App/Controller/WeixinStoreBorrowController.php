<?php

namespace Controller;

use Model\KoalaPay;
use Model\Borrow;
use Model\ReturnMoney;

/**
 * Class WeixinStoreBorrowController 商家贷款控制器
 * @author xiaozhu
 */
class WeixinStoreBorrowController extends WeixinBaseController
{
    /**
     * ajax申请贷款
     * 
     * @author zengxiong
     * @since  2017年9月26日
     */
    public function applyMoney()
    {
        $storeId    = intval($this->storeId);//店铺id
        $id         = isset($_POST['id']) ? $_POST['id'] : '';//产品id
        $loanAmount = isset($_POST['loanAmount']) ? intval($_POST['loanAmount']) / MONEY_RATIO : 0;//申请贷款金额
        $loanTime   = isset($_POST['loanTime']) ? intval($_POST['loanTime']) : 0;//贷款期限

        //模拟数据
        if (empty($storeId) || empty($id) || empty($loanAmount) || empty($loanTime)){
            $this->echoJson(false,'参数错误',[]);
            return false;
        }
        
        $koalaPayModel = new KoalaPay();
        
        // 去钱包获取产品列表数据
        $productData = $koalaPayModel->getUserProductList($storeId);
        $productList =  isset($productData['status']) && $productData['status'] == 1 ?  $productData['list']['productList'] : [];
        $product = [];
        foreach ($productList as $row){
            if ($row['productNo'] == $id){
                $product = $row;
                break;
            }
        }
        if (empty($product)){
            $this->echoJson(false,'获取产品失败',[]);
            return false;
        }
        $product['loanTypes'] = $product['loanTypes'][0];
        
        //贷款期限类型
        $productType = [
            '2' => '日',//按日计息，随借随还
            '3' => '月',//等额本息，按月分期
            '5' => '日',//等额本息，按日分期
        ];
        $loanTimeType = $productType[intval($product['loanTypes']['confReMethod'])];
        
        if($loanAmount < $product['prodMinAmt']){
            $this->echoJson(false,'该产品最小贷款额度为:'.$product['prodMinAmt'] * MONEY_RATIO .'元',[]);
            return false;
        }elseif ($loanAmount > $product['prodMaxAmt']){
            $this->echoJson(false,'该产品最大贷款额度为:'.$product['prodMaxAmt'] * MONEY_RATIO .'元',[]);
            return false;
        }elseif ($loanTime < $product['loanTypes']['confMinPeriod']){
            $this->echoJson(false,'该产品最小贷款期限为:'.$product['loanTypes']['confMinPeriod'].$loanTimeType,[]);
            return false;
        }elseif ($loanTime > $product['loanTypes']['confMaxPeriod']){
            $this->echoJson(false,'该产品最大贷款期限为:'.$product['loanTypes']['confMaxPeriod'].$loanTimeType,[]);
            return false;
        }
        
        //校验通过,开始贷款
        $typeNum = intval($product['loanTypes']['confReMethod']);
        $notifyUrl = HOST . "/notify/borrowFY?loanAmount=" . $loanAmount . "&loanTime=" . $loanTime . "&confRate=" . $product['loanTypes']['confRate'] . 
            "&confMngRate=" . $product['loanTypes']['confMngRate'] . "&loanType=" . $typeNum;
        $param = [
            'pay_userid'        => $storeId,//店铺ID
            'page_notify_url'   => $notifyUrl,//回调地址
            'product_no'        => $id,//产品id
            'apply_amt'         => $loanAmount,//借款金额,单位分
            'loan_type'         => $product['loanTypes']['confReMethod'],//借款类型 03：等额本息，按月分期 05：等额本息，按日分期
            'col_type'          => '01',//到账类型 01:U融汇账户； 02:银行卡；
            'loan_rate'         => $product['loanTypes']['confRate'],//借款利率
            'mng_rate'          => $product['loanTypes']['confMngRate'],//资金管理费率
            'loan_period'       => $loanTime,//借款期限 : 期数
            'remark'            => '备注',//备注
        ];
        $res = $koalaPayModel->doBorrowMoney($param,1);
        if (isset($res['status']) && $res['status'] == 1){
            $this->echoJson(true, '', $res['result_obj']);
        }else{
            $this->echoJson(false, '发起请求失败', []);
        }
    }

    /**
     * 借款成功页面
     * 
     * @author zengxiong
     * @since  2017年9月26日
     */
    public function borrowSuccess()
    {
        if ( isset($_GET['fromNotify']) && $_GET['fromNotify']==1 ) {
            $borrowInfo = [
                'apply_amt' => $_GET['loanAmount'],
                'loan_period' => $_GET['loanTime'],
                'loan_type' => $_GET['loanType'],
                'loan_rate' => $_GET['confRate'],
                'mng_rate' => $_GET['confMngRate'],
            ];
        } else {
            $loanId = isset($_GET['loanId']) ? strval($_GET['loanId']) : '';
            if ( empty($loanId) ) {
                $redirectUrl = HOST . "/weixin/errorShow?msg=没有对应借款记录";
                $this->redirect($redirectUrl);
            }

            $borrowInfo = (new Borrow())->getBorrowDetailByLoanId($loanId);
            if ( empty($borrowInfo) || $borrowInfo['store_id']!=$this->storeId ) {
                $redirectUrl = HOST . "/weixin/errorShow?msg=没有对应借款信息";
                $this->redirect($redirectUrl);
            }
        }

        // 套入成功页面
        $this->view = $this->myView->make('loan.loanSuccess')
            ->with('borrowInfo', $borrowInfo);
    }

    /**
     * 借款记录列表页面
     */
    public function loanLog()
    {
        // 根据商家id和分页信息读取借款列表
        $borrowModel = new Borrow();
        $borrowList = $borrowModel->getBorrowList($this->storeId);

        // 获取对应的主键id
        $borrowIdArray = array_keys(array_column($borrowList, NULL, 'log_id'));

        // 读取对应还款计划信息
        if ( !empty($borrowIdArray) ) {
            $repaymentList = (new ReturnMoney())->getRepaymentInfoByIdArray($borrowIdArray);
            $repaymentList = array_column($repaymentList, NULL, 'borrow_money_log_id');
        } else {
            $repaymentList = [];
        }

        // 根据商家id查看是否有逾期还款
        // 融资的状态: 00-待提交 ,01-已提交待经办,02-经办通过待复核,03复核通过待放款,04-审批不通过,05-正常还款中,06-逾期,07-已结清借款,08-已退货,09已终止,10已发起融资,11已回购
        $isOverdue = $borrowModel->checkIsOverdue($this->storeId);

        $this->view = $this->myView->make('loan.mineRecord')
            ->with('isOverdue', $isOverdue)
            ->with('repaymentList', $repaymentList)
            ->with('borrowList', $borrowList);
    }

    /**
     * 审核中-详情
     */
    public function reviewOn()
    {
        $logId = isset($_GET['log_id']) ? intval($_GET['log_id']) : 0;
        if ( empty($logId) ) {
            echo "<script>alert('请求信息不可为空');history.go(-1);</script>";
            exit();
        }

        // 读取借款信息
        $borrowModel = new Borrow();
        $logData = $borrowModel->getReturnDetail($logId);
        if ( empty($logData) ) {
            echo "<script>alert('没有对应的借款信息');history.go(-1);</script>";
            exit();
        }

        // 设置返回还款类型信息
        $repaymentTypeData = [
            '2' => '随借随还，按日分期',
            '3' => '等额本息，按月分期',
            '5' => '等额本息，按日分期'
        ];

        // 获取产品罚息信息
        $productData = (new KoalaPay())->getUserProductList($this->storeId);
        if ( isset($productData['status']) && $productData['status']==1 ) {
            $productArray = array_column($productData['list']['productList'], NULL, 'productNo');
        } else {
            $productArray = [];
        }

        // 如果有对应产品信息
        $product = isset($productArray[ $logData['product_no'] ]) ? $productArray[ $logData['product_no'] ] : [];
        if ( empty($product) ) {
            echo "<script>alert('没有对应的产品信息');history.go(-1);</script>";
            exit();
        }

        // 如果是审核中
        if (in_array($logData['status'], [0,1,2,3,4])){
            $this->view = $this->myView->make('loan.reviewOn')
                ->with('repaymentTypeData', $repaymentTypeData)
                ->with('product', $product)
                ->with('logData', $logData);
        }else{
            // 获取还款列表信息
            $repaymentModel = new ReturnMoney();
            $repaymentList  = $repaymentModel->getRepaymentList($logId);
            // 设置剩余天数
            foreach ($repaymentList as $key => $repayment) {
                $repaymentList[ $key ] ['days'] = $repaymentModel->dealWithOverdueDays($repayment['repayment_time']);
                if ( $repayment['repayment_status']==3 ) {
                    $repaymentList[ $key ] ['days'] += 1;
                }
                $repaymentList[ $key ]['money'] = number_format($repayment['repayment_capital']/100, 2, ".", "");
                $repaymentList[ $key ]['return_fee'] = number_format(($repayment['repeyment_interest']+$repayment['repayment_penalty']+$repayment['repayment_ahead_fee']+$repayment['repayment_mng_fee'])/100, 2, ".", "");
            }
            //审核通过详情
            $this->view = $this->myView->make('loan.reviewComplete')
                ->with('logData', $logData)
                ->with('product', $product)
                ->with('repaymentTypeData', $repaymentTypeData)
                ->with('returnList', $repaymentList);
        }
    }

    /**
        * ajax模拟申请借款
        *
        * @return json
     */
    public function ajaxForPretendBorrowMoney()
    {
        //产品id
        $id = strval($_POST['id']);
        //借款金额
        $borrowMoney = floatval($_POST['borrow_money']);
        //借款期限
        $borrowTime = intval($_POST['borrow_time']);
        //借款类型
        $loanType = strval($_POST['loan_type']);;
        //借款利率
        $loanRate = strval($_POST['loan_rate']);;
        //管理利率
        $mngRate = strval($_POST['mng_rate']);;
        // 有空数据
        if ( empty($id) || empty($borrowMoney) || empty($borrowTime) || empty($loanType) || empty($loanRate) ) {
            $this->echoJson(1507536865, '请求信息不能为空');
        }
        
        //获取模拟借款计划
        $param = [
            'pay_userid'        => $this->storeId,//店铺ID
            'page_notify_url'   => HOST . '/weixin/product/notice',
            'product_no'        => $id,//产品id
            'apply_amt'         => $borrowMoney,//借款金额,单位分
            'loan_type'         => $loanType,//借款类型 03：等额本息，按月分期 05：等额本息，按日分期
            'col_type'          => '02',//到账类型 01:U融汇账户； 02:银行卡；
            'loan_rate'         => stripos($loanRate, '.')>0 ? $loanRate . '00' : $loanRate . '.0000',//借款利率
            'mng_rate'          => stripos($mngRate, '.')>0 ? $mngRate . '00' : $mngRate . '.0000',//借款利率
            'loan_period'       => $borrowTime,//借款期限 : 期数
            'remark'            => '备注',//备注
        ];
        $koalaPayModel = new KoalaPay();
        $plan = $koalaPayModel->doBorrowMoney($param, 0);
        if ( $plan['status']==1 ) {
            if (isset($plan['result_obj']['plan'])) {
                $planList[] = $plan['result_obj']['plan'];
            } else {
                $planList = $plan['result_obj'];
            }
        } else {
            $planList = [];
        }

        if ( empty($planList) || count($planList)==0 ) {
            $this->echoJson(1507537267, '模拟借款失败');
        }

        //还款计划
        $repaymentInterest = 0.00;
        if ( !empty($planList) && count($planList)>0 ) {
            $interestArray = array_column($planList, 'repaymentInterest');
            $interestMoney = array_sum($interestArray);
            $feeArray = array_column($planList, 'repaymentMngFee');
            $feeMoney = array_sum($feeArray)*MONEY_RATIO;
            $repaymentInterest = ($interestMoney+$feeMoney);
        }
        $returnData = [
            'borrow_money' => $borrowMoney,
            'borrow_time' => $borrowTime,
            'interestMoney' => $repaymentInterest,
        ];

        //　模拟借款成功
        $this->echoJson(0, '模拟借款成功', $returnData);
    }
}
