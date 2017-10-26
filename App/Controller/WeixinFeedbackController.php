<?php

namespace Controller;
use Model\Feedback;
use Leaf\Loger\LogDriver;

/**
 * Class WeixinFeedbackController 留言控制器
 * @author yao
 */
class WeixinFeedbackController extends WeixinBaseController
{
    private $feedbackModel;

    public function __construct()
    {
        parent::__construct();
        $this->feedbackModel = new feedback();

    }

    public function add()
    {
        if (IS_POST) {

            $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
            $result = $this->feedbackModel->add($comment,$this->storeId);
            if ($result) {
                $this->redirect('/weixin/account/info');
            } else{
                $this->redirect('/weixin/feedback/add');
            }
        } else {

            $this->view = $this->myView->make('feedback.feedback')->with('store_id',$this->storeId);
        }
    }

}
