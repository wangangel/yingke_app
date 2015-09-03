<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/yingke/Public/admin/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/yingke/Public/admin/js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $(".click").click(function(){
  $(".tip").fadeIn(200);
  });
  
  $(".tiptop a").click(function(){
  $(".tip").fadeOut(200);
});

  $(".sure").click(function(){
  $(".tip").fadeOut(100);
});

  $(".cancel").click(function(){
  $(".tip").fadeOut(100);
});

});
</script>

</head>


<body>

	<div class="place">
    <span>位置：</span>
    <ul class="placeul">
    <li><a href="#">首页</a></li>
    </ul>
    </div>
    
    <div class="mainindex">
    
    
    <div class="welinfo">
    <span><img src="/yingke/Public/admin/images/sun.png" alt="天气" /></span>
    <b><?php echo ($_SESSION['admin_name']); ?>&nbsp;您好，欢迎使用安居易信息管理系统</b>
    </div>
    
    <div class="welinfo">
    <span><img src="/yingke/Public/admin/images/time.png" alt="时间" /></span>
    <i>您上次登录的时间：<?php echo ($_SESSION['login_time']); ?></i>
    </div>

</body>
</html>