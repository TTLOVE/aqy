<?php

//开启session
session_start();

// 定义 BASE_PATH
define('BASE_PATH', __DIR__ . "/../");

//自定义常量
require BASE_PATH . 'Config/define.php';

// 启动器
require BASE_PATH .'bootstrap.php';

// 路由配置、开始处理
require BASE_PATH . 'Config/routes.php';
