<?php

namespace Controller;
use Model\ClientSession;
use Model\StoreMember;
use Model\AppUser;
use Model\StoreInfo;
use Service\View;
use Service\Mail;

/**
 * Class BaseController
 * @author xiaozhu
 */
class BaseController
{
    protected $view;
    protected $mail;

    public function __construct()
    {

    }

    public function __destruct()
    {

        // 导入页面
        $view = $this->view;

        if ( $view instanceof View ) {
            if (is_array($view->data)) {
                extract($view->data);
                echo $view->view->make($view->viewName, $view->data)->render();

            } else {
                echo $view->view->make($view->viewName)->render();
            }
        }
    }

    public function redirect($url)
    {
        header("location: ".$url);
        exit;
    }

    /**
     * 输出json数据
     * @param bool $status
     * @param string $msg
     * @param array $data
     */
    public function echoJson($status = true, $msg = '', $data = [])
    {
        $res = json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
        exit($res);
    }

    /**
        * 通过请求的header获取商家id
        *
        * @return int
     */
    public function checkStore()
    {
        $appUserInfo = [];
        $isUsertoken = (strpos( strtolower(json_encode($_SERVER) ), 'http_usertoken')) === false ? 0 : 1;

        // 如果有设置http_usertoke
        if($isUsertoken)
        {
            // 如果不是app打开的页面，则获取商家信息失败
            if(stripos($_SERVER['HTTP_USER_AGENT'],'AppName/KoalacSq') === false)
            {
                return $appUserInfo;
            }

            $mAuth = trim($_SERVER['HTTP_USERTOKEN']);

            //根据mAuth查找用户id
            $sessionModel = new ClientSession();
            $uid = $sessionModel->getUidByMauth($mAuth);

            if ( $uid ) {
                // 根据用户id获取app用户信息
                $appUserInfo = (new AppUser())->getUserInfoById($uid);
            }
        }
        return $appUserInfo;
    }
}
