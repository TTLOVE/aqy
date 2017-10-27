<?php

namespace Controller;
use Leaf\Loger\LogDriver;
use Model\Picture;
use Model\User;
use Model\UserPoster;
use Model\PosterLog;

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
        $uid = 10;
        $templateId = 1;
        $userPosterId = (new UserPoster())->addUserPoster($uid);
        if ( $userPosterId>0 ) {
            $insertData = [];
            $wordsArray = [
                '最怕空气突然?',
                '我的最怕空气突然安静',
                '帮助开发者更方便地进行开发和调试',
                '可在PC或Mac上模拟访问微信内网页，帮助开发者更方便地进行开发和',
            ];
            for ($i = 0; $i < 4; $i++) {
                $words = $wordsArray[ $i ];
                $picUrl = (new Picture())->createPic($userPosterId, $templateId, $words);
                $insertData[] = [
                    $userPosterId,
                    $templateId,
                    $words,
                    $picUrl
                ];
            } 
            $insertCount = (new PosterLog())->addPosterLog($insertData);
            if ( $insertCount>0 ) {
                $redirectUrl = HOST . '/picShow?poster_id=' . $userPosterId;
                $this->redirect($redirectUrl);
            }
        }
    }

    /**
     * 根据id显示一整列图片信息
     *
     */
    public function picShow()
    {
        $posterId = isset($_GET['poster_id']) ? intval($_GET['poster_id']) : 0;
        if ( empty($posterId) ) {
            exit('生成图片失败');
        }
        $picList = (new PosterLog())->getPosterLogListByPosterId($posterId);
        $this->view = $this->myView->make('picShow')->with('picList', $picList);
    }

    /**
     * 借款攻略页面
     */
    public function guidance()
    {
        $this->view = $this->myView->make('account.guidance');
    }
}
