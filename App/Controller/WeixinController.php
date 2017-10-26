<?php

namespace Controller;
use Leaf\Loger\LogDriver;

/**
 * Class WeixinController 微信页面控制器
 */
class WeixinController extends WeixinBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 个人主页
     */
    public function home()
    {
        exit("首页");
    }

    /**
     * 借款攻略页面
     */
    public function guidance()
    {
        $this->view = $this->myView->make('account.guidance');
    }
}
