<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/yingke/Public/admin/css/style.css" rel="stylesheet" type="text/css" />
<link href="/yingke/Public/admin/css/select.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/yingke/Public/admin/js/jquery.js"></script>
<script type="text/javascript" src="/yingke/Public/admin/js/jquery.idTabs.min.js"></script>
<script type="text/javascript" src="/yingke/Public/admin/js/select-ui.min.js"></script>
<script type="text/javascript" src="/yingke/Public/admin/editor/kindeditor.js"></script>
<link href="/yingke/Public/admin/css/lyz.calendar.css" rel="stylesheet" type="text/css" />
<script src="/yingke/Public/admin/js/lyz.calendar.min.js" type="text/javascript"></script>
</head>

<body>

	<div class="place">
    <span>位置：</span>
    <ul class="placeul">
    <li>关于我们</li>

    </ul>
    </div>
     <form  action=<?php echo U("admin/system/saveorupdate");?> method='post'>
    <div class="formbody">
    
    <div class="formtitle"><span>关于我们</span></div>
    
    <ul class="forminfo">
	
    <li><label>QQ交流群</label><input type="text" class="dfinput" id="category-name"  name='qqgroup' value="<?php echo ($system_config["qqgroup"]); ?>"></li>
    <li><label>电话</label>  <input type="text" id="tel" class='dfinput' name='tel' value="<?php echo ($system_config["tel"]); ?>"></li>
	<li><label>邮箱</label>  <input type="text" id="email" class='dfinput' name='email' value="<?php echo ($system_config["email"]); ?>"></li> 
	<li><label>logo</label>  <input type="text" id="logopath" class='dfinput' name='logopath' value="<?php echo ($system_config["logopath"]); ?>"></li> 
    <li><label>&nbsp;</label><input name="" type="submit" class="btn" value="确认保存"/></li>
    </ul>
    
    
    </div>
</form>

</body>

</html>