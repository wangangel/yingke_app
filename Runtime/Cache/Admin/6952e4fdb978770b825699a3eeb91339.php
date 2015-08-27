<?php if (!defined('THINK_PATH')) exit();?>

<div class="am-cf admin-main">
  <!-- sidebar start -->
 <link rel="stylesheet" href="/base/Public/assets/css/amazeui.min.css"/>
  <link rel="stylesheet" href="/base/Public/assets/css/admin.css">
    <link rel="stylesheet" href="/base/Public/assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="/base/Public/assets/css/y-css.css">
  <!-- sidebar end -->

  <!-- content start -->
  <div class="admin-content">
    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">角色资料</strong> / <small>Personal information</small></div>
    </div>

    <hr/>

    <div class="am-g">

      <div class="am-u-sm-12 am-u-md-4 am-u-md-push-8">
       

      </div>

       <div class="am-u-sm-12 am-u-md-8 am-u-md-pull-4">
        <form id="myForm" class="am-form am-form-horizontal" action=<?php echo U('admin/role/role_add');?> method='post'>
          <div class="am-form-group">
            <label for="user-name" class="am-u-sm-3 am-form-label">角色名称 / rolename</label>
            <div class="am-u-sm-9">
              <input type="text" id="role-name" placeholder="角色名称 / RoleName" name='role_name'>
              
            </div>
          </div>
          <div class="am-form-group">
            <label for="user-email" class="am-u-sm-3 am-form-label">是否启用 / role_status</label>
            <div class="am-u-sm-9">
              <select  name="role_status" >
                <option value="0">否</option>
                <option value="1" selected="true">是</option>
              </select>
              <!-- <input type="hidden" id="auth_pid" placeholder="输入密码 / Password" name='auth_pid'> -->
              
            </div>
          </div>

          <div class="am-form-group">
            <div class="am-u-sm-9 am-u-sm-push-3">
              <button type="button" onclick="check()" class="am-btn am-btn-primary">保存</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- content end -->

</div>


<!--<footer>
  <hr>
  <p class="am-padding-left">© 2015 AllMobilize, Inc. Licensed under MIT license. <a href="http://www.mycodes.net/" target="_blank">宿迁蜂鸟网络科技有限公司</a></p>
</footer>
-->
<!--[if lt IE 9]>
<script src="assets/js/jquery1.11.1.min.js"></script>
<script src="assets/js/modernizr.js"></script>
<script src="assets/js/polyfill/rem.min.js"></script>
<script src="assets/js/polyfill/respond.min.js"></script>
<script src="assets/js/amazeui.legacy.js"></script>
<![endif]-->

<!--[if (gte IE 9)|!(IE)]><!-->
<script data-cfasync="false" src="/base/Public/assets/js/jquery.min.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/amazeui.min.js"></script>
<!--<![endif]-->
<script data-cfasync="false" src="/base/Public/assets/js/app.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/bootstrap.min.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/jquery.superslide.2.1.1.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/jquery-1.11.2.min.js"></script>
</body>
</html>
<script data-cfasync="false" type="text/javascript">
  function check(){
    var rolename = $("#role-name").val();
    if(trim(rolename).length == 0 ){
        alert("请填写角色名称！");
        return false;   
    }else{
        $("#myForm").submit();
    }
   
  }
  //删除左右两端的空格
  function trim(str){ 
　　  return str.replace(/(^\s*)|(\s*$)/g, "");
　　}

</script>