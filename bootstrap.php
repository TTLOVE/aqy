<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Autoload 自动载入
require BASE_PATH.'/vendor/autoload.php';

// 根据配置显示错误信息
if ( IS_ERROR==true ) {
    // whoops 错误提示
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}
