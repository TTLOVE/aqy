<?php

namespace Controller;
use Leaf\Loger\LogDriver;
use Model\Picture;

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
     * 首页
     */
    public function home()
    {
        exit("首页");
    }

    /**
     * 生成图片
     *
     * @return 
     */
    public function savePic()
    {
        $words = isset($_GET['words']) ? strval($_GET['words']) : '最怕空气突然?';
        $picUrl = (new Picture())->generateImg (1, 1, $words);
        $this->redirect($picUrl);
    }

    /**
     * 借款攻略页面
     */
    public function guidance()
    {
        $this->view = $this->myView->make('account.guidance');
    }
}
