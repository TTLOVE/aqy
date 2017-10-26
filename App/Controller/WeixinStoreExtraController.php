<?php

namespace Controller;
use Model\StoreExtraInfo;
use Model\CarrierOperatorArea;
use Model\StoreInfo;
use Model\userCenter;
use Model\ReturnMoney;
use Model\KoalaPay;

/**
 * Class WeixinStoreExtraController 商家额外信息控制器
 * @author xiaozhu
 */
class WeixinStoreExtraController extends WeixinBaseController
{
    /**
     * 视图
     * @var unknown
     */
    public  $myView;
    
    /**
        * 获取商家配置的基本信息
        *
        * @return mix
     */
    public function getStoreExtraInfo()
    {
        // 获取商家设置的额外信息
        $storeExtraInfo = (new StoreExtraInfo())->getStoreExtraInfoByStoreId($this->storeId);
        // 获取商家的基本信息
        $storeInfo = (new StoreInfo())->getStoreInfoById($this->storeId);
        // 获取有用商家信息
        $usefulStoreInfo = [
            'store_id' => $storeInfo['store_id'],
            'store_realname' => $storeInfo['store_realname'],
            'idcard_sn' => $storeInfo['idcard_sn'],
        ];
        // 合并两组数据
        $storeInfo = array_merge($storeExtraInfo, $usefulStoreInfo);
        echo "\n\n";
        var_export($storeInfo);
        echo "\n\n";
        exit;
    }

    /**
        * 保存商家额外信息
        *
        * @return array
     */
    public function saveStoreExtraInfo()
    {
        //最高月还款额度
        $returnMoneyMax = isset($_POST['return_money_max']) ? intval($_POST['return_money_max']) : 0;
        //教育程度
        $educationalStatus = isset($_POST['educational_status']) ? trim($_POST['educational_status']) : '';
        //现单位是否缴纳社保
        $socialSecurity = isset($_POST['social_security']) ? trim($_POST['social_security']) : '';
        //车辆情况
        $carInfomation = isset($_POST['car_infomation']) ? trim($_POST['car_infomation']) : '';
        //经营年限
        $operatingLife = isset($_POST['operating_life']) ? trim($_POST['operating_life']) : '';
        //经营流水
        $operatingStream = isset($_POST['operating_stream']) ? intval($_POST['operating_stream']) : 0;

        if ( empty($educationalStatus) || empty($socialSecurity) || empty($carInfomation) || empty($operatingLife) ) {
            $this->echojson(1501663387, '传输的数据有空');
        }

        // 保存信息
        $saveStoreExtraInfoStatus = (new StoreExtraInfo())->saveStoreExtraInfo($this->storeId, $returnMoneyMax, $educationalStatus, $socialSecurity, 
            $carInfomation, $operatingLife, $operatingStream);
        if ( $saveStoreExtraInfoStatus ) {
            $this->echojson(1, '保存数据成功');
        } else {
            $this->echojson(1501666296, '保存数据失败');
        }
    }
    
    /**
     * 额度管理
     * 
     * @author zengxiong
     * @since  2017年8月7日
     */
    public function quotaManage()
    {
        // 去钱包获取产品列表数据(返回产品列表和额度统计)
        $productData = (new KoalaPay())->getUserProductList($this->storeId);
        if ( isset($productData['status']) && $productData['status']==1 ) {
            $productList = $productData['list'];
        } else {
            $productList = [
                'lmtAble' => 0.00,
                'lmtTotal' => 0.00,
            ];
        }

        if ( isset($productList['productList']) ) {
            foreach ($productList['productList'] as $key => $product) {
                if ( isset($product['loanTypes']['loan']) ) {
                    $loanTypes = $product['loanTypes']['loan'];
                    unset($productList['productList'][ $key ]['loanTypes']);
                    $productList['productList'][ $key ]['loanTypes'][] = $loanTypes;
                }
            }
        }

        $returnModel = new ReturnMoney();
        
        // 获取商家其他信息
        $StoreExtraInfoModel = new StoreExtraInfo();
        $storeExtraInfo = $StoreExtraInfoModel->getStoreExtraInfoByStoreId($this->storeId);
        //银行卡链接
        $bankConfigUrl = KOALACPAY_DRAWAL_URL . 'index.php?controller=withdraw&action=bankcard_list';
        //是否有逾期
        $isOverdue = $returnModel->isOverdue($this->storeId);
        
        $this->view = $this->myView->make('loan.quotaManage')
            ->with('storeExtraInfo',$storeExtraInfo)
            ->with('isOverdue',$isOverdue)
            ->with('list',$productList)
            ->with('storeId',$this->storeId)
            ->with('bankConfigUrl',$bankConfigUrl);
    }
    
    /**
     * 开放交易流水
     * 
     * @author zengxiong
     * @since  2017年8月7日
     */
    public function updateOpenStream()
    {
        $storeId = $this->storeId;
        $status  = isset($_POST['setStatus']) ? $_POST['setStatus'] : 0;
        
        $StoreExtraInfoModel = new StoreExtraInfo();
        $res = $StoreExtraInfoModel->updateOpenStream($storeId, $status);
        $this->echoJson(boolval($res));
    }
    
    /**
     * 个人身份认证
     * 
     * @author zengxiong
     * @since  2017年8月7日
     */
    public function storeProve()
    {
        $StoreExtraInfoModel = new StoreExtraInfo();
        
        //store_extra_info表信息
        $storeExtraInfo = $StoreExtraInfoModel->getStoreExtraInfoByStoreId($this->storeId);
        $storeModel     = new StoreInfo();
        $storeInfo      = $storeModel->getStoreInfoById($this->storeId);
        $usefulStoreInfo = [
            'store_id' => $storeInfo['store_id'],
            'store_realname' => $storeInfo['store_realname'],
            'idcard_sn' => $storeInfo['idcard_sn'],
        ];
        $this->view = $this->myView->make('loan.store_prove')->with('info',$storeExtraInfo)->with('base',$usefulStoreInfo);
    }
    
    /**
     * 运营商手机号码认证
     * 
     * @author zengxiong
     * @since  2017年8月8日
     */
    public function certification()
    {
        $userCenterModel = new userCenter();
        $res = $userCenterModel->getUserInfoById($this->storeId);
        
        //修改手机号码url
        $modifyUrl = B_URL . 'index.php/Mall/Store/modifyPhone/store_id/'.$this->storeId;
        $this->view = $this->myView->make('loan.certification')->with('info',$res)->with('modifyUrl',$modifyUrl);
    }
}
