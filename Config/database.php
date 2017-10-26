<?php

if(getenv('RUNTIME_ENVIROMENT') == "DEV"){
    $config = array(
        'finance' => array(
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'finance',
            'username'  => 'finance',
            'password'  => 'SZR2K^WeU%DLBtjJjo0C',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
        'userCenter' => array(
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'lifeq_user',
            'username'  => 'finance',
            'password'  => 'SZR2K^WeU%DLBtjJjo0C',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
        'shenghuoquan' => array(
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'lifeq',
            'username'  => 'finance',
            'password'  => 'SZR2K^WeU%DLBtjJjo0C',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
    );
}elseif(getenv('RUNTIME_ENVIROMENT') == "DOCKER"){
    $config = array(
        'finance' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.0.161',
            'database'  => 'finance',
            'username'  => 'root',
            'password'  => 'koalac',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
        'userCenter' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.0.160',
            'database'  => 'lifeq_user',
            'username'  => 'root',
            'password'  => 'koalac123',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
        'shenghuoquan' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.0.160',
            'database'  => 'lifeq',
            'username'  => 'root',
            'password'  => 'koalac123',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
    );
}else{
    $config = array(
        'finance' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.0.161',
            'database'  => 'finance',
            'username'  => 'root',
            'password'  => 'koalac',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
        'userCenter' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.0.160',
            'database'  => 'lifeq_user',
            'username'  => 'root',
            'password'  => 'koalac123',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
        'shenghuoquan' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.0.160',
            'database'  => 'lifeq',
            'username'  => 'root',
            'password'  => 'koalac123',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'port'      => 3306,
            'prefix'    => ''
        ),
    );
}

return $config;
