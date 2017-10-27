<?php

use \NoahBuscher\Macaw\Macaw;

// weixin页面
Macaw::get('home', 'Controller\WeixinController@home');
Macaw::get('picShow', 'Controller\WeixinController@picShow');
Macaw::get('savePic', 'Controller\WeixinController@savePic');
Macaw::post('savePic', 'Controller\WeixinController@savePic');


// 外部回调接口
Macaw::post('notify/borrow/success', 'Controller\NotifyController@notifyForMoneyBorrow');
Macaw::post('notify/repayment/success', 'Controller\NotifyController@notifyForMoneyRepayment');
Macaw::post('notify/repayFY', 'Controller\NotifyController@notifyFromFYForRepay'); // 还款后返回页面
Macaw::post('notify/borrowFY', 'Controller\NotifyController@notifyFromFYForBorrow'); // 借款后返回页面
Macaw::post('notify/newAccount', 'Controller\NotifyController@notifyFromFYAccount'); // 申请用户后返回页面

Macaw::$error_callback = function() {
      throw new Exception("路由无匹配项 404 Not Found");
};

Macaw::dispatch();
