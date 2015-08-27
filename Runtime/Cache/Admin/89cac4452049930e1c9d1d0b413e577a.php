<?php if (!defined('THINK_PATH')) exit();?>

<div class="am-cf admin-main">
  <!-- sidebar start -->
 <link rel="stylesheet" href="/base/Public/assets/css/amazeui.min.css"/>
  <link rel="stylesheet" href="/base/Public/assets/css/admin.css">
    <link rel="stylesheet" href="/base/Public/assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="/base/Public/assets/css/y-css.css">
  <!-- sidebar end -->
  <style type="text/css">
  .admin-content{
    height: 3000px;
  }
ul{overflow:hidden;width:100%;}
ul li{width:33.33%;float:left;}
                 </style>
  <!-- content start -->
  <div class="admin-content">
    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">分配权限</strong> / <small>Distribute</small></div>
    </div>

    <hr/>

    <div class="am-g">

      <div class="am-u-sm-12 am-u-md-4 am-u-md-push-8">
       

      </div>

       <div class="am-u-sm-12 am-u-md-8 am-u-md-pull-4">
        <form id="myForm" class="am-form am-form-horizontal" action=<?php echo U('admin/role/distribute_role');?> method='post'>
          <div style="font-size: 13px; margin: 10px 5px;">
            <h4>角色&nbsp;[&nbsp;<?php echo ($role_info["role_name"]); ?>&nbsp;]</h4>
          
              
              <?php if(is_array($auth_infoA)): foreach($auth_infoA as $k=>$vo): ?><ul  class="am-list am-list-static">
                    <?php if(in_array(($vo["auth_id"]), is_array($have_auth_arr)?$have_auth_arr:explode(',',$have_auth_arr))): ?><li> <input type='checkbox' name='ids' onclick="checkAll(this,<?php echo ($vo["auth_id"]); ?>)" value='<?php echo ($vo["auth_id"]); ?>'checked='checked'> <b><?php echo ($vo["auth_name"]); ?> </b></li> 
                      <?php else: ?>
                        <li><input type='checkbox' id="check<?php echo ($vo["auth_id"]); ?>" name='ids' onclick="checkAll(this,<?php echo ($vo["auth_id"]); ?>)" value='<?php echo ($vo["auth_id"]); ?>'> <b><?php echo ($vo["auth_name"]); ?> </b></li><?php endif; ?>
               </ul>
               
                    <ul id='li_style' class="am-list am-list-static am-list-border am-list-striped">
                      <?php if(is_array($auth_infoB)): foreach($auth_infoB as $k=>$vv): if($vv['auth_pid'] == $vo['auth_id']): if(in_array(($vv["auth_id"]), is_array($have_auth_arr)?$have_auth_arr:explode(',',$have_auth_arr))): ?><li><input type='checkbox' id="childid<?php echo ($vv["auth_pid"]); ?>" name='ids' value='<?php echo ($vv["auth_id"]); ?>'checked='checked'><?php echo ($vv["auth_name"]); ?></li>
                                    <?php else: ?>
                                    <li>
                                      <input type='checkbox'id="childid<?php echo ($vv["auth_pid"]); ?>" name='ids' value='<?php echo ($vv["auth_id"]); ?>'>
                                    <?php echo ($vv["auth_name"]); ?>
                                  </li><?php endif; endif; endforeach; endif; ?>
                    </ul><?php endforeach; endif; ?>
          
        </div>
          <input type="hidden"  name="role_id" value="<?php echo ($role_info["role_id"]); ?>">
          <input type="hidden" id="authids" name="authids" value=""/>
          <div class="am-form-group">
            <div class="am-u-sm-9 am-u-sm-push-3">
              <button type="button" onclick="getAll()" class="am-btn am-btn-primary">分配权限</button>
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
  function checkAll(obj,index){
    if(obj.checked){
      $("input[id='childid"+index+"']").prop("checked", true);
    }else{
      $("input[id='childid"+index+"']").prop("checked", false);
    }

  }

  //获取被选择的ids
  function getAll(){
    if($("input[name='ids']:checked").val() == null){
        alert("请对该角色授权！");
        return false;
    }else{
        var checked = [];
        $('input[name="ids"]:checked').each(function(){
            checked.push($(this).val());
        });
        var checkStr = checked.join(",");
        $("#authids").val(checkStr);
        $("#myForm").submit();
        
    }

  }
  
</script>