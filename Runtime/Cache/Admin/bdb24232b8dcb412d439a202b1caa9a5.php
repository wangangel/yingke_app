<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

<script type="text/javascript">

function reset(){
   alert('ssss');
   $('.tip').css('display':'block')
}
 $(function () {
        $("#txtBeginDate").calendar({
            controlId: "divDate",                                 // 弹出的日期控件ID，默认: $(this).attr("id") + "Calendar"
            speed: 200,                                           // 三种预定速度之一的字符串("slow", "normal", or "fast")或表示动画时长的毫秒数值(如：1000),默认：200
            complement: true,                                     // 是否显示日期或年空白处的前后月的补充,默认：true
            readonly: true,                                       // 目标对象是否设为只读，默认：true
            upperLimit: new Date(),                               // 日期上限，默认：NaN(不限制)
            lowerLimit: new Date("2011/01/01"),                   // 日期下限，默认：NaN(不限制)
            callback: function () {                               // 点击选择日期后的回调函数
                alert("您选择的日期是：" + $("#txtBeginDate").val());
            }
        });
        $("#txtEndDate").calendar();
    });
</script>
<script type="text/javascript">

</script>

</head>



<body style="background:url(/yingke/Public/admin/images/topbg.gif) repeat-x;">

    <div class="topleft">
    <a href="" target=""><img src="/yingke/Public/admin/images/logo.png" title="系统首页" /></a>
    </div>
            
    <div class="topright">    
  <ul>
    <!-- <li><span><img src="/yingke/Public/admin/images/help.png" title="帮助"  class="helpimg"/></span><a href="#">帮助</a></li> -->
        <li><span><img class="helpimg"/></span><a href="" onclick="reset()">修改密码</a></li>
   <!--  <li><a href="#">关于</a></li> -->
    <li><a href=<?php echo U("admin/login/logout");?> target="_parent">退出</a></li>
    </ul>
     
   <div class="user">
    <span><?php echo ($_SESSION['admin_name']); ?></span>
    
    </div>    
    
    </div>
<div class="tip" >
        <div class="tiptop"><span>提示信息</span><a></a></div>
        
      <div class="tipinfo">
        <span><img src="/yingke/Public/admin/images/ticon.png" /></span>
        <div class="tipright">
        <p>是否确认对信息的修改 ？</p>
        <cite>如果是请点击确定按钮 ，否则请点取消。</cite>
        </div>
        </div>
        
        <div class="tipbtn">
        <input name="" type="button"  class="sure" value="确定" />&nbsp;
        <input name="" type="button"  class="cancel" value="取消" />
        </div>
    
    </div>
</body>

</html>