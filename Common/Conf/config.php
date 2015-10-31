<?php
//定义回调URL通用的URL
define('URL_CALLBACK', 'http://api.bihuo123.com/index.php?m=Api&c=SSO&a=callback&type=');
define('WEB_HOST', 'http://api.bihuo123.com');
return array(
    'VAR_MODULE'            =>  'm',     // 默认模块获取变量
    'VAR_CONTROLLER'        =>  'c',    // 默认控制器获取变量
    'VAR_ACTION'            =>  'a',    // 默认操作获取变量
    //'配置项'=>'配置值'
    'URL_MODEL' => 1, // 如果你的环境不支持PATHINFO 请设置为3
    //'MODULE_ALLOW_LIST' => array('Api', 'Admin','Home'),
    // 设置禁止访问的模块列表
    'DB_TYPE'               =>  'mysql',                // 数据库类型
    'DB_HOST'               =>  '42.123.87.205',        // 服务器地址
    'DB_NAME'               =>  'yingke',                   // 数据库名
    'DB_USER'               =>  'root',                 // 用户名
    'DB_PWD'                =>  'ZHANGZHAO608',                 // 密码
    'DB_PORT'               =>  '3306',                 // 端口
    'DB_PREFIX'             =>  'yk_',                  // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,                  // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,                   // 启用字段缓存
    'DEFAULT_MODULE'        =>  'Admin',
    'DEFAULT_CONTROLLER' => 'Login', 
    'DB_CHARSET'            =>  'utf8',                 // 数据库编码默认采用utf8
    'WEB_URL'      => 'http://127.0.0.1/yingke/',

    'TMPL_ACTION_SUCCESS'=>'Public:jump',
    'TMPL_ACTION_ERROR'=>'Public:jump',
     //微信登录
    'THINK_SDK_WEIXIN' => array(
        'APP_KEY'    => 'wxb8e3693e0481e640', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '73b424971cea37515ea6354fb924742b', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'weixin'
    ),
    'THINK_SDK_SINA' => array(
        'APP_KEY'    => '599678130', //应用注册成功后分配的 APP ID
        'APP_SECRET' => 'f17b02720629fb39a56fba35e677a0bb', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'sina'
    ),
      /*微信支付配置*/
    'WxPayConf_pub'=>array(
        'APPID' => 'wxb8e3693e0481e640',
        'MCHID' => '1264809601',
        'KEY' => 'BZruR8wE9xHNSnXVTh0MDXOhdhGX85HH',
        'APPSECRET' => '73b424971cea37515ea6354fb924742b',
        'JS_API_CALL_URL' => WEB_HOST.'/index.php/Api/WxJsAPI/jsApiCall',
        'SSLCERT_PATH' => WEB_HOST.'/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_cert.pem',
        'SSLKEY_PATH' => WEB_HOST.'/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_key.pem',
        'NOTIFY_URL' =>  WEB_HOST.'/index.php/Api/WxJsAPI/notify',
        'CURL_TIMEOUT' => 30
    ),
    //支付宝配置参数
'alipay_config'=>array(
       'partner' =>'2088021170084496',   //这里是你在成功申请支付宝接口后获取到的PID；
    'key'=>'txa6xj0p2wepunravfzryo230pan2npu',//这里是你在成功申请支付宝接口后获取到的Key
    'sign_type'=>strtoupper('MD5'),
    'input_charset'=> strtolower('utf-8'),
    'cacert'=> getcwd().'\\cacert.pem',
    'transport'=> 'http',
      ),
     //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；
    
'alipay'   =>array(
 //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
'seller_email'=>'dev@skyeyeslive.com',

//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
'notify_url'=>'http://127.0.0.1/Api/SSO/notifyurl', 

//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
'return_url'=>'http://127.0.0.1/Api/SSO/returnurl',

//支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
'successpage'=>'User/myorder?ordtype=payed',   

//支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
'errorpage'=>'User/myorder?ordtype=unpay', 
),
);
