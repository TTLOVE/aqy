<?php

//公共常量
define('IS_POST','POST' === $_SERVER['REQUEST_METHOD']);
define('IS_GET','GET' === $_SERVER['REQUEST_METHOD']);
define('IS_AJAX',isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
//产品分页
define('PRODUCT_PAGE_SIZE', 40);

if(getenv('RUNTIME_ENVIROMENT') == "DEV"){
    // 是否显示报错
    define("IS_ERROR", true);
    // 提现页面域名
    define("KOALACPAY_DRAWAL_URL", "https://appweb.kaolapay.cn/");
    // b站域名
    define("B_URL", "https://tb.lifeq.com.cn/");
    // 钱包域名
    define("KL_PAY", 
        [
            'url' => "https://pay.kaolapay.cn/",
            'mall_client_id' => '11',
            'clerk_client_id' => '4',
            'app_id' => 'abac',
            'app_key' => 'adjfua125js',
        ]
    );
    define("KL_API", 
        [
            'url' => "https://tklapi.lifeq.com.cn/",
            'client_id' => 'script',
            'client_secret' => '141eb82b0b2c6cc8a97ba3f129db4815',
        ]
    );
    // 本站域名
    define('HOST', 'https://tfinance.lifeq.com.cn');
    //define('HOST', 'http://finance.com');
    //借贷金额单位 分
    define('MONEY_RATIO', 0.01);
    // 我要借款服务商id
    define('BORROW_APP_ID', 232);
}elseif(getenv('RUNTIME_ENVIROMENT') == "DOCKER"){
    // 是否显示报错
    define("IS_ERROR", true);
    // 提现页面域名
    define("KOALACPAY_DRAWAL_URL", "https://appweb.kaolapay.cn/");
    // b站域名
    define("B_URL", "https://tb.lifeq.com.cn/");
    // 钱包域名
    define("KL_PAY", 
        [
            'url' => "https://pay.kaolapay.cn/",
            'mall_client_id' => '11',
            'clerk_client_id' => '4',
            'app_id' => 'abac',
            'app_key' => 'adjfua125js',
        ]
    );
    define("KL_API", 
        [
            'url' => "https://tklapi.lifeq.com.cn/",
            'client_id' => 'script',
            'client_secret' => '141eb82b0b2c6cc8a97ba3f129db4815',
        ]
    );
    // 本站域名
    define('HOST', 'https://tfinance.lifeq.com.cn');
    //借贷金额单位 分
    define('MONEY_RATIO', 0.01);
    // 我要借款服务商id
    define('BORROW_APP_ID', 232);
}else{
    // 是否显示报错
    define("IS_ERROR", true);
    // 提现页面域名
    define("KOALACPAY_DRAWAL_URL", "https://appweb.koalacpay.com/");
    // b站域名
    define("B_URL", "https://b.lifeq.com.cn/");
    // 钱包配置
    define("KL_PAY", 
        [
            'url' => "https://pay.koalacpay.com/",
            'mall_client_id' => '1',
            'clerk_client_id' => '4',
            'app_id' => 'abac',
            'app_key' => 'adjfua125js',
        ]
    );
    define("KL_API", 
        [
            'url' => "https://klapi.lifeq.com.cn/",
            'client_id' => 'script',
            'client_secret' => '141eb82b0b2c6cc8a97ba3f129db4815',
        ]
    );
    // 本站域名
    define('HOST', 'https://finance.lifeq.com.cn');
    //借贷金额单位 分
    define('MONEY_RATIO', 0.01);
    // 我要借款服务商id
    define('BORROW_APP_ID', 223);
}
