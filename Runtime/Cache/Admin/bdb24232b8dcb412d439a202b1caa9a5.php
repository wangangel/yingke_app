<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>

<link href="/base/Public/admin/css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" data-cfasync="false" src="/base/Public/admin/js/jquery.js"></script>
<script type="text/javascript">
$(function(){	
	//顶部导航切换
	$(".nav li a").click(function(){
		$(".nav li a.selected").removeClass("selected")
		$(this).addClass("selected");
	})	
})	
</script>


</head>

<body style="background:url(/base/Public/admin/images/topbg.gif) repeat-x;">

    <div class="topleft">
    <a href="" target=""><img src="/base/Public/admin/images/logo.png" title="系统首页" /></a>
    </div>
        
    <ul class="nav">
    <li><a href="#"  class="selected"><img src="/base/Public/admin/images/icon01.png" title="工作台" /><h2>首页</h2></a></li>
   
        <li><a href="<?php echo U("admin/servers/servers_list");?>" target="rightFrame"><img src="/base/Public/admin/images/icon02.png" title="商品发布" /><h2>商品发布</h2></a></li>
    
    <li><a href=<?php echo U("admin/order/order_list");?> target="rightFrame"><img src="/base/Public/admin/images/icon03.png" title="订单管理" /><h2>订单管理</h2></a></li>
    
    <li><a href=<?php echo U("admin/content/content_list");?> target="rightFrame"><img src="/base/Public/admin/images/icon04.png" title="资讯中心" /><h2>资讯中心</h2></a></li>
    
    <li><a href=<?php echo U("admin/system/update");?>  target="rightFrame"><img src="/base/Public/admin/images/icon06.png" title="系统设置" /><h2>系统设置</h2></a></li>
    </ul>
            
    <div class="topright">    
    <ul>
    <!-- <li><span><img src="/base/Public/admin/images/help.png" title="帮助"  class="helpimg"/></span><a href="#">帮助</a></li> -->
	    <li><span><img class="helpimg"/></span><a href=<?php echo U('Home/Index/index');?> target="_blank">网站首页</a></li>
   <!--  <li><a href="#">关于</a></li> -->
    <li><a href=<?php echo U("admin/login/logout");?> target="_parent">退出</a></li>
    </ul>
     
    <div class="user">
    <span><?php echo ($_SESSION['admin_name']); ?></span>
    
    </div>    
    
    </div>
</body>
</html>