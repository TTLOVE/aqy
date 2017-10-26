<?php

require '../../vendor/autoload.php';

$logDriver = new \Leaf\Loger\Example\LogDriver();
$logDriver->setLogFile('./log.log');

//设置日志等级
//$logDriver->getLoger()->setLogLevel(\Leaf\Loger\LogerClass\LogLevel::WARNING);

//设置日志类别
$logDriver->getLoger()->setLogCategory('profile');

sleep(5);
$logDriver->info('info', [], 'profile');
sleep(5);
$logDriver->warning('warning', [], 'profile');
sleep(5);
$logDriver->error('error', [], 'profile');
