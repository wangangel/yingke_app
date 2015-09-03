<?php if (!defined('THINK_PATH')) exit();?><!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->
<link href="/yingke/Public/admin/css/style.css" rel="stylesheet" type="text/css" />
<link href="/yingke/Public/admin/css/select.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/yingke/Public/admin/js/jquery.js"></script>
<script type="text/javascript" src="/yingke/Public/admin/js/jquery.idTabs.min.js"></script>
<script type="text/javascript" src="/yingke/Public/admin/js/select-ui.min.js"></script>
<script type="text/javascript" src="/yingke/Public/admin/editor/kindeditor.js"></script>
<link href="/yingke/Public/admin/css/lyz.calendar.css" rel="stylesheet" type="text/css" />
<script src="/yingke/Public/admin/js/lyz.calendar.min.js" type="text/javascript"></script>
  <!-- sidebar start -->
 <div class="am-cf admin-main">
  <!-- sidebar end -->

  <!-- content start -->
  <div class="admin-content">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">消息列表</strong> / <small>Message List</small></div>
    </div>

    <div class="am-g">
      <div class="am-u-md-6 am-cf">
        <div class="am-fl am-cf">
          <div class="am-btn-toolbar am-fl">
            <div class="am-btn-group am-btn-group-xs">
              <input type="button" value="批量删除" onclick="getAll()"/>
            <?php if(in_array(($add_show), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U("admin/message/add_show");?>><button type="button" class="am-btn am-btn-default"><span class="am-icon-plus"></span> 发送信息</button></a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="am-g">
      <div class="am-u-sm-12">
        <form class="am-form">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                <th><input type="checkbox" name="all" id="all" onclick="checkall(this)"/>全选</th>
                <th class="table-title">序号</th>
                <th class="table-title">内容</th>
                <th class="table-author">发送目标</th>
                <th class="table-author">发送时间</th>
                <th class="table-author">发送人</th>
                <th class="table-set">操作</th>
              </tr>
          </thead>
          <tbody>
          <?php if(is_array($message_list)): foreach($message_list as $k=>$vo): ?><tr>
              <td><input type="checkbox"  name="ids" value="<?php echo ($vo["id"]); ?>"/></td>
              <td><?php echo ($k+1); ?></td>
              <td><?php echo (msubstr($vo["m_content"],0,12,'utf-8',true)); ?>
              </td>
              <td><?php if($vo["m_target"] == 'all'): ?>全体用户<?php else: ?>多个用户<?php endif; ?></td>
              <td><?php echo (date('Y-m-d',$vo["m_date"])); ?></td>
              <td><?php echo ($vo["m_user"]); ?></td>
              <td>
                <div class="am-btn-toolbar">
                  <div class="am-btn-group am-btn-group-xs">
                  <?php if(in_array(($look_show), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><!-- <a href=<?php echo U('Admin/message/look_show',array('id'=>$vo['id']));?>><span class="am-icon-pencil-square-o"></span> 查看  </a>|&nbsp; --><?php endif; ?>
                  <?php if(in_array(($message_del), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('Admin/message/message_del',array('id'=>$vo['id']));?>><span class="am-icon-trash-o"></span> 删除</a><?php endif; ?>
                  </div>
                </div>
              </td>
            </tr><?php endforeach; endif; ?>
            
          </tbody>
        </table>

    <?php echo ($page); ?>

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
<script data-cfasync="false" src="/yingke/Public/assets/js/jquery.min.js"></script>
<script data-cfasync="false" src="/yingke/Public/assets/js/amazeui.min.js"></script>
<!--<![endif]-->
<script data-cfasync="false" src="/yingke/Public/assets/js/app.js"></script>
<script data-cfasync="false" src="/yingke/Public/assets/js/bootstrap.min.js"></script>
<script data-cfasync="false" src="/yingke/Public/assets/js/jquery.superslide.2.1.1.js"></script>
<script data-cfasync="false" src="/yingke/Public/assets/js/jquery-1.11.2.min.js"></script>
</body>
</html>
<script type="text/javascript">
  /*
     *全选按钮，prop方法，是给ipnut框添加属性的
    */
    function checkall(obj){
      if(obj.checked){
        $("input[name='ids']").prop("checked", true);
      }else{
        $("input[name='ids']").prop("checked", false);
      }
    }

    function getAll(){
      if($("input[name='ids']:checked").val() == null){
        alert("请选择要删除的列表！");
        return false;
      }else{
        var checked = [];
        $('input[name="ids"]:checked').each(function(){
              checked.push($(this).val());
          });
        var checkStr = checked.join(",");
        var url = "<?php echo U('admin/message/del_all');?>?ids="+checkStr;
        window.location.href=url;
      }
    }

</script>