<?php

namespace Controller;
use Service\View;
use Model\StoreInfo;

/**
 * Class WeixinGuideController 微信借款引导页面
 * @author xiaozhu
 */
class WeixinGuideController extends BaseController
{
    public function guide()
    {
        $appStoreInfo = $this->checkStore();
        if ( empty($appStoreInfo) ) {
            exit("获取用户信息失败,请重新打开～");
        }
        // 读取商家信息0待审核，1审核通过，2已关闭，3注册成功但未提交店铺资料，4审核不通过
        $storeId = isset($appStoreInfo['userid']) ? $appStoreInfo['userid'] : 0;
        $storeInfo = (new StoreInfo())->getStoreInfoById($storeId, 'store_id,state');
        // 如果商家信息读取不到，则跳去开店页面
        if ( empty($storeInfo) ) {
            $storeInfo = [
                'store_id' => $storeId,
                'state' => -100
            ];
        }

        $goOpenStoreUrl = B_URL . 'Mall/Wechat/dredgewx/invite_type/qrcode?from=groupmessage&isappinstalled=0';

        $this->view = (new View())->make('loan.guide')
            ->with('goOpenStoreUrl', $goOpenStoreUrl)
            ->with('appStoreInfo', $appStoreInfo)
            ->with('storeInfo', $storeInfo)
            ->with('storeId', $storeId);
    }

    /**
        * 错误显示页面
        *
     */
    public function errorShow()
    {
        $msg = strval($_GET['msg']);
        $this->view = (new View())->make('loan.loanError')->with('ErrorMsg', $msg);
    }
}
