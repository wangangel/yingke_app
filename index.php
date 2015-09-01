<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用入口文件
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('require PHP > 5.3.0 !');
    
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define('RUNTIME_PATH', './Runtime/');
/*
 * 定义当前网站的url
 */
define('URL', 'http://' . $_SERVER['HTTP_HOST']);

/*
 * 定义当前网站的公共目录
 */

define('URL_PUB', URL . '/Public/');
//定义公共模块的目录，放到应用目录外
define('COMMON_PATH', './Common/');
//关闭目录安全文件的生成
define('BUILD_DIR_SECURE', false);
// 定义应用目录
$_GET['m'] = 'Admin'; // 绑定Home模块到当前入口文件
$_GET['c'] = 'Login'; // 绑定Index控制器到当前入口文件
define('APP_PATH', './Application/');
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单