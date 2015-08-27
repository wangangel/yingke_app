<?php if (!defined('THINK_PATH')) exit();?>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->
 <link rel="stylesheet" href="/base/Public/assets/css/amazeui.min.css"/>
  <link rel="stylesheet" href="/base/Public/assets/css/admin.css">
    <link rel="stylesheet" href="/base/Public/assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="/base/Public/assets/css/y-css.css">
  <!-- sidebar start -->
 <div class="am-cf admin-main">
  <!-- sidebar end -->
  <!-- content start -->
  <div class="admin-content">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">数据表列表</strong> / <small>Data List</small></div>
    </div>
    <div class="am-g">
      <div class="am-u-sm-12">
        <form class="am-form" id="myForm" action=<?php echo U("admin/backup/backup_all");?>>
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                
                <th><input type="checkbox" id="chkMsgId" name="chkMsgId" onclick="doCheck(this)"/>全选/全不选</th>
                <th class="table-id">序号</th>
                <th class="table-title">表名称</th>
                <th class="table-set">操作</th>
              </tr>
          </thead>
          <tbody>
          <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
              <td><input type="checkbox" name="items" value="<?php echo ($vo["tables_in_anjuyi"]); ?>"/></td>
              <td><?php echo ($key+1); ?></td>
              <td><?php echo ($vo["tables_in_anjuyi"]); ?></td>
              <td>
                <div class="am-btn-toolbar">
                  <div class="am-btn-group am-btn-group-xs">
                    <?php if(in_array(($backup_table), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('admin/backup/backup_table',array('table'=>$vo['tables_in_anjuyi']));?>><span class="am-icon-pencil-square-o"></span> 备份  </a><?php endif; ?>
                  </div>
                </div>
              </td>
            </tr><?php endforeach; endif; ?>
            
          </tbody>
        </table>

          <input type="hidden" id="selectAll" name="selectAll" value="">
           <?php if(in_array(($backup_all), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><input type="button" value="备份"  onclick ="backup()"/><?php endif; ?>

          <hr />
          <p  style="text-align:center"><a href="">蜂鸟科技,诚意为您</a></p>
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
<script type="text/javascript">
   /**  
        * 操作全选复选框事件  
        **/  
        function doCheck(obj){  
            var inputs=document.getElementsByTagName("input");  

            for(var i=0;i<inputs.length;i++){  
                if(inputs[i].type=="checkbox" && inputs[i].id!="chkMsgId") //刷选出所有复选框  
                {  
                    inputs[i].checked=obj.checked;   
                }  
            }  
        } 
        
         /**  
        * 获取所有复选框  
        **/  
        function getCheckBox()  {  
            var inputs= document.getElementsByTagName("input");  
            var chkInputs=new Array();  
            var j=0;  
            for(var i=0;i<inputs.length;i++) {  
                if(inputs[i].type=="checkbox" && inputs[i].id!="chkMsgId"&& inputs[i].checked){  
                  
                   chkInputs[j]=inputs[i].value;  
                    j++;
                }  
            }  
            return chkInputs;  
        }     
        /**
         * 复选删除
         */
        function backup(){
          var chkInputs = getCheckBox();

          var select = document.getElementById("selectAll");
          if (chkInputs != "" && chkInputs.length != 0) {
              select.value = chkInputs;
              document.getElementById("myForm").submit();
          }else{
              alert("请选择要备份项！");
              return false;
          }
         

        }






</script>