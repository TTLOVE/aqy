<?php

namespace Controller;
use Service\View;
use Model\StoreInfo;

/**
 * Class WeixinBaseController 微信页面的父类
 * @author xiaozhu
 */
class WeixinBaseController extends BaseController
{
    protected $myView;

    public function __construct()
    {
        // TODO 验证微信登录
        $userId = isset($_COOKIE['wx_userid']) ? intval($_COOKIE['wx_userid']) : 0;
        if ( empty($userId) ) {
            // 跳去微信授权
        }
        $this->myView = new View();
    }
}
