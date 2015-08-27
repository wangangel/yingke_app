<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/base/Public/admin/css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="/base/Public/admin/js/jquery.js"></script>

<script type="text/javascript">
$(function(){	
	//导航切换
	$(".menuson li").click(function(){
		$(".menuson li.active").removeClass("active")
		$(this).addClass("active");
	});
	
	$('.title').click(function(){
		var $ul = $(this).next('ul');
		$('dd').find('ul').slideUp();
		if($ul.is(':visible')){
			$(this).next('ul').slideUp();
		}else{
			$(this).next('ul').slideDown();
		}
	});
})	
</script>


</head>

<body style="background:#f0f9fd;">

    
    <dl class="leftmenu">
        


        <?php if(is_array($auth_infoA)): foreach($auth_infoA as $k=>$vo): ?><dd>
                <div class="title">
                <span><img src="/base/Public/admin/images/leftico0<?php echo ($k+1); ?>.png" /></span><?php echo ($vo["auth_name"]); ?>
                </div>
                <ul class="menuson" style="display: none">
                    <?php if(is_array($auth_infoB)): foreach($auth_infoB as $k=>$vv): if($vv['auth_pid'] == $vo['auth_id']): ?><li><cite></cite>
                                <a href="../<?php echo ($vv["auth_c"]); ?>/<?php echo ($vv["auth_a"]); ?>" target="rightFrame"><?php echo ($vv["auth_name"]); ?></a>
                                <i></i>
                            </li><?php endif; endforeach; endif; ?>
                </ul>    
            </dd><?php endforeach; endif; ?>

   <!--  <dd>
    <div class="title">
    <span><img src="/base/Public/admin/images/leftico01.png" /></span>系统管理
    </div>
    	<ul class="menuson" style="display: none">
        <li><cite></cite><a href=<?php echo U("admin/system/update");?> target="rightFrame">系统设置</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/data/data_list");?> target="rightFrame">数据字典</a><i></i></li>    
        <li><cite></cite><a href=<?php echo U("admin/dictionary/dictionary_list");?> target="rightFrame">字典类型</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/advice/advice_list");?> target="rightFrame">用户反馈</a><i></i></li>
		<li><cite></cite><a href=<?php echo U("admin/backup/backup_list");?> target="rightFrame">数据备份</a><i></i></li>
		<li><cite></cite><a href=<?php echo U("admin/log/log_list");?> target="rightFrame">日志管理</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/message/message_list");?> target="rightFrame">消息管理</a><i></i></li> 
        </ul>    
    </dd>
        
    <dd>
    <div class="title">
    <span><img src="/base/Public/admin/images/leftico02.png" /></span>用户管理
    </div>
    <ul class="menuson">
        <li><cite></cite><a href=<?php echo U("admin/member/admin_list");?> target="rightFrame">管理员用户</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/member/businessman_list");?> target="rightFrame">商家用户</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/member/member_list");?> target="rightFrame">普通用户</a><i></i></li>
        </ul>     
    </dd> 
     <dd>
    <div class="title">
    <span><img src="/base/Public/admin/images/leftico02.png" /></span>权限管理
    </div>
        <ul class="menuson">
            <li><cite></cite><a href=<?php echo U("admin/role/role_list");?> target="rightFrame">角色列表</a><i></i></li>
            <li><cite></cite><a href=<?php echo U("admin/auth/auth_list");?> target="rightFrame">权限列表</a><i></i></li>
        </ul>     
    </dd> 
    <dd><div class="title"><span><img src="/base/Public/admin/images/leftico03.png" /></span>商品服务管理</div>
    <ul class="menuson">
        <li><cite></cite><a href="<?php echo U("admin/category/category_list");?>" target="rightFrame">服务(商品)分类</a><i></i></li>
        <li><cite></cite><a href="<?php echo U("admin/servers/servers_list");?>" target="rightFrame">服务（商品）列表</a><i></i></li>
		  <li><cite></cite><a href="<?php echo U("admin/coupon/coupon_list");?>" target="rightFrame">优惠券</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/comment/comment_list");?> target="rightFrame">评价列表</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/area/area_list");?> target="rightFrame">库房列表</a><i></i></li>
    </ul>    
    </dd>  
    
    <dd><div class="title"><span><img src="/base/Public/admin/images/leftico04.png" /></span>订单管理</div>
    <ul class="menuson">
        <li><cite></cite><a href=<?php echo U("admin/order/order_list");?> target="rightFrame">订单列表</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/order/order_appolist");?> target="rightFrame">预约列表</a><i></i></li>
    </ul>
    
    </dd>  


    <dd><div class="title"><span><img src="/base/Public/admin/images/leftico04.png" /></span>内容管理</div>
    <ul class="menuson">
        <li><cite></cite><a href=<?php echo U("admin/channel/channel_list");?> target="rightFrame">栏目列表</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/content/content_list");?> target="rightFrame">内容列表</a><i></i></li>
        <li><cite></cite><a href=<?php echo U("admin/Banner/banner_list");?> target="rightFrame">幻灯管理</a><i></i></li>
    </ul>
    
    </dd>    
 -->
    </dl>

</body>
</html>