<?php
//定义回调URL通用的URL
define('URL_CALLBACK', 'http://www.edeco.cc/index.php?m=Home&c=Login&a=callback&type=');
return array(
    'VAR_MODULE'            =>  'm',     // 默认模块获取变量
    'VAR_CONTROLLER'        =>  'c',    // 默认控制器获取变量
    'VAR_ACTION'            =>  'a',    // 默认操作获取变量
    //'配置项'=>'配置值'
    'URL_MODEL' => 1, // 如果你的环境不支持PATHINFO 请设置为3
    //'MODULE_ALLOW_LIST' => array('Api', 'Admin','Home'),
    // 设置禁止访问的模块列表
    'DB_TYPE'               =>  'mysql',                // 数据库类型
    'DB_HOST'               =>  '127.0.0.1',        // 服务器地址
    'DB_NAME'               =>  'yingke',                   // 数据库名
    'DB_USER'               =>  'root',                 // 用户名
    'DB_PWD'                =>  'ZHANGZHAO608',                 // 密码
    'DB_PORT'               =>  '3306',                 // 端口
    'DB_PREFIX'             =>  'yk_',                  // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,                  // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,                   // 启用字段缓存

    'DB_CHARSET'            =>  'utf8',                 // 数据库编码默认采用utf8


    'TMPL_ACTION_SUCCESS'=>'Public:jump',
    'TMPL_ACTION_ERROR'=>'Public:jump',
    //'SESSION_AUTO_START' => true,    //是否开启SESSION


    //短信验证平台配置文件
    'sms_config'  => array(
            'username' => 'ajywangluokeji',
            'password' => '200005',

        ),
    //腾讯QQ登录配置
    'THINK_SDK_QQ' => array(
        'APP_KEY'    => '101223533', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '9d27da905c6829d4bdb9c994d96701ce', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'qq',
    ),
    //新浪微博配置
    'THINK_SDK_SINA' => array(
        'APP_KEY'    => '2478765671', //应用注册成功后分配的 APP ID
        'APP_SECRET' => 'e48c6e3ecaeb85e053d237b7c8cc1e42', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'sina',
    ),
    'THINK_SDK_WEIXIN' => array(
        'APP_KEY'    => 'wxbe2bebc18aa83c01', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '455110f705ef53668fc15dda46d068d8', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'weixin',
    ),
    //'配置项'=>>'配置值'
    'alipay_config'=>array(
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字//
        'partner'       => '2088911759821856',

        //安全检验码，以数字和字母组成的32位字符//
        'key'           => 'i1quguih3shm1g9a7mru9p3koelxa051',


        //签名方式 不需修改
        'sign_type'    => strtoupper('MD5'),

        //字符编码格式 目前支持 gbk 或 utf-8
        'input_charset'=> strtolower('utf-8'),

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        'cacert'    => getcwd().'\\cacert.pem',
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'transport'    => 'http',
    ),
    /**************************请求参数配置**************************/
    'alipay'=>array(
        //支付类型
        'payment_type' => 1,
        //必填，不能修改
        //服务器异步通知页面路径
        'notify_url' => 'http://www.edeco.cc/Home/Pay/notifyurl',
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        'return_url' => 'http://www.edeco.cc/Home/Pay/returnurl',
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //卖家支付宝帐户
        'seller_email' => 'bjedeco@163.com',
        //必填
        /************************************************************/
    )
);
