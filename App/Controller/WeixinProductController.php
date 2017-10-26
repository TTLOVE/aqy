<?php

namespace Controller;
use Model\Product;
use Model\StoreExtraInfo;
use Model\ReturnMoney;
use Model\KoalaPay;
use Model\StoreInfo;
use Leaf\Loger\LogDriver;

/**
 * Class WeixinProductController 借款产品控制器
 * @author yao
 */
class WeixinProductController extends WeixinBaseController
{
    //贷款期限类型
    public $productType = [
        '2' => '日',//按日计息，随借随还
        '3' => '月',//等额本息，按月分期
        '5' => '日',//等额本息，按日分期
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 申请富友用户页面
     */
    public function goToFuyouAddAccount()
    {
        // 获取商家渠道id
        $storeInfo = (new StoreInfo())->getStoreInfoById($this->storeId);
        $returnUrl = HOST . '/notify/newAccount';

        // 获取请求富友信息
        $requestData = (new KoalaPay())->goToFuyouAddAccountRequest($this->storeId, $storeInfo['channel_id'], $returnUrl);
        if ( isset($requestData['status']) && $requestData['status']==1 ) {
            $this->view = $this->myView->make('wxProduct.goToFuyou')->with('requestData', $requestData['info']);
        } else {
            $this->redirect( HOST . "/weixin/errorShow?msg=获取富友请求信息失败");
        }
    }

    /*
     * 获取借款产品列表
     */
    public function getProductList()
    {
        // 查询商家信息
        $storeInfo = (new StoreInfo())->getStoreInfoById($this->storeId);
        $koalaPayModel = new KoalaPay();
        // 如果进件到富友
        if ( $storeInfo['bank_sl_unified']==3 ) {
            // 查看用户是否开通富友用户
            $fuyouAccountInfo = $koalaPayModel->getFuyouAccountInfo($this->storeId, $storeInfo['channel_id']);
            if ( isset($fuyouAccountInfo['status']) && $fuyouAccountInfo['status']==1 ) {
                if ( $fuyouAccountInfo['info']['bl_user_status']=="" ) {
                    // 跳转富友申请用户页面
                    $this->redirect( HOST . "/weixin/goToFuyou");
                }
            } else {
                // 跳转富友申请用户页面
                $this->redirect( HOST . "/weixin/goToFuyou");
            } 
        }

        // 去钱包获取产品列表数据
        $productData = $koalaPayModel->getUserProductList($this->storeId);
        if ( isset($productData['status']) && $productData['status']==1 ) {
            $productList = $productData['list'];
        } else {
            $productList = [
                'lmtAble' => 0.00,
                'lmtTotal' => 0.00,
            ];
        }

        // 处理插入产品列表
        if ( !empty($productList['productList']) ) {
            $this->addProductList($productList['productList']);
        }

        // 查看是否过期
        $repaymentModel = new ReturnMoney();
        $isOverdue = intval($repaymentModel->isOverdue($this->storeId));
        $repaymentMoney = $repaymentModel->getStoreRepaymentMoney($this->storeId);

        $this->view = $this->myView->make('wxProduct.loan')
                           ->with('isOverdue', $isOverdue)
                           ->with('repaymentMoney', $repaymentMoney)
                           ->with('list',$productList);
    }

    /**
     * 根据钱包返回的产品列表记录到本地
     *
     * @param $productList 产品列表
     *
     * @return boolean
     */
    private function addProductList($productList)
    {
        if ( empty($productList) ) {
            return false;
        }
        // 设置数组的key为对应产品id
        $productData =array_column($productList, NULL, 'productNo');
        // 得到列表所有的产品id
        $productIdArr = array_keys($productData);
        // 获取对应产品列表
        $productModel = new Product();
        $exitProductList = $productModel->getProductListByIdArr($productIdArr);

        // 对比拿到未保存的数据
        $exitIdArr = array_column($exitProductList, 'product_no');
        $diffIdArr = array_values(array_diff($productIdArr, $exitIdArr));
        // 如果有不一致的产品id
        $insertData = [];
        if ( !empty($diffIdArr) ) {
            foreach ($diffIdArr as $id) {
                if ( isset($productData[ $id ]) ) {
                    $product = [
                        $id, 
                        $productData[ $id ]['prodName'],
                        $productData[ $id ]['prodMinAmt'],
                        $productData[ $id ]['prodMaxAmt'],
                        $productData[ $id ]['loanTypes'][0]['confReMethod'],
                        $productData[ $id ]['loanTypes'][0]['confMinPeriod'],
                        $productData[ $id ]['loanTypes'][0]['confMaxPeriod'],
                        $productData[ $id ]['loanTypes'][0]['confRate'],
                        $productData[ $id ]['loanTypes'][0]['confLateRate'],
                        $productData[ $id ]['loanTypes'][0]['confMngRate'],
                        $productData[ $id ]['loanTypes'][0]['confPreRate'],
                    ];
                    array_push($insertData, $product);
                }
            }
        }

        // 插入数据
        if ( !empty($insertData) ) {
            $insertStatus = $productModel->addProduct($insertData);
            if ( empty($inserStatus) ) {
                (new LogDriver())->error('product', "批量插入产品失败,data:" . json_encode($insertData));
                return false;
            }
        }
        return true;
    }

    /*
     * 获取借款产品详情
     */
    public function getInfo()
    {
        $koalaPayModel = new KoalaPay();
        //产品id
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        //借款金额
        $borrowMoney = isset($_GET['borrow_money']) ? $_GET['borrow_money'] : '';
        //借款期限
        $borrowTime = isset($_GET['borrow_time']) ? intval($_GET['borrow_time']) : '';
        
        // 去钱包获取产品列表数据
        $productData = $koalaPayModel->getUserProductList($this->storeId);
        $productList =  isset($productData['status']) && $productData['status'] == 1 ?  $productData['list']['productList'] : [];
        $product = [];
        foreach ($productList as $row){
            if ($row['productNo'] == $id){
                $product = $row;
                break;
            }
        }
        if (empty($product)){
            $redirectUrl = HOST . "/weixin/errorShow?msg=获取产品信息失败";
            $this->redirect($redirectUrl);
        }
        $product['loanTypes'] = $product['loanTypes'][0];
        //把额度的分转换成元
        $product['prodMinAmt'] = $product['prodMinAmt'] * MONEY_RATIO;
        $product['prodMaxAmt'] = $product['prodMaxAmt'] * MONEY_RATIO;

        //获取商家认证信息
        $isAuth = (new StoreExtraInfo)->getStoreExtraInfoByStoreId($this->storeId);
        
        //借款类型:日,月
        $loanType = $this->productType[intval($product['loanTypes']['confReMethod'])];
        //月利率
        $confRate = $loanType == '日' ? $product['loanTypes']['confRate'] * 30 : $product['loanTypes']['confRate'];
        //获取模拟借款计划
        $param = [
            'pay_userid'        => $this->storeId,//店铺ID
            'page_notify_url'   => HOST . '/weixin/product/notice',
            'product_no'        => $id,//产品id
            'apply_amt'         => !empty($borrowMoney) ? $borrowMoney : $product['prodMinAmt'],//借款金额,单位分
            'loan_type'         => $product['loanTypes']['confReMethod'],//借款类型 03：等额本息，按月分期 05：等额本息，按日分期
            'col_type'          => '02',//到账类型 01:U融汇账户； 02:银行卡；
            'loan_rate'         => $product['loanTypes']['confRate'],//借款利率
            'mng_rate'          => $product['loanTypes']['confMngRate'],//资金管理费率
            'loan_period'       => !empty($borrowTime) ? $borrowTime : $product['loanTypes']['confMinPeriod'],//借款期限 : 期数
            'remark'            => '备注',//备注
        ];
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
            ['value' => 1, 'name' => '实际到账'.$param['apply_amt'].'元'],
            ['value' => 1, 'name' => '到期还款'.$param['apply_amt'].'元'],
            ['value' => 1, 'name' => '总利息'.$repaymentInterest.'元'],
            ['value' => 1, 'name' => $loanType .'利率'.$product['loanTypes']['confRate'].'%']
        ];

        $this->view = $this->myView->make('wxProduct.detail')
            ->with('detail',$product)->with('confRate',$confRate)
            ->with('store_id',$this->storeId)->with('loanType',$loanType)
            ->with('repaymentInterest',$repaymentInterest)
            ->with('returnData',json_encode($returnData,JSON_UNESCAPED_UNICODE))
            ->with('isAuth',$isAuth)->with('id',$id)->with('plan',$param);
    }
    
}
