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
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">反馈列表</strong> / <small>Advice List</small></div>
    </div>

    <div class="am-g">
      <div class="am-u-sm-12">
        <form class="am-form">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                <!-- <th class="table-id">ID</th>
                <th class="table-title">用户ID</th> -->
                <th class="table-title">意见类型</th>
                <th class="table-type">意见内容</th>
                <th class="table-author">联系方式</th>
                <th class="table-set">操作</th>
              </tr>
          </thead>
          <tbody>
          <?php if(is_array($advice_list)): foreach($advice_list as $k=>$vo): ?><tr>
              <!-- <td><?php echo ($vo["id"]); ?></td>
              <td><?php echo ($vo["userid"]); ?></td> -->
              <?php if($vo["type"] == '0'): ?><td>功能意见</td>
                <?php elseif($vo["type"] == '1'): ?>
                <td>安装服务</td>
                <?php elseif($vo["type"] == '2'): ?>
                <td>配送服务</td>
                <?php elseif($vo["type"] == '3'): ?>
                <td>售后服务</td><?php endif; ?>
              <td><?php echo ($vo["advicecontent"]); ?></td>
              <td><?php echo ($vo["phone"]); ?></td>
              <td>
                <div class="am-btn-toolbar">
                  <div class="am-btn-group am-btn-group-xs">
                    <!-- <a href=<?php echo U('admin/member/admin_edit_show',array('id'=>$vo['id']));?>><span class="am-icon-pencil-square-o"></span> 查看  </a>|&nbsp; -->
                    <?php if(in_array(($advice_del), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('admin/advice/advice_del',array('id'=>$vo['id']));?>><span class="am-icon-trash-o"></span> 删除</a><?php endif; ?>
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
<script data-cfasync="false" src="/base/Public/assets/js/jquery.min.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/amazeui.min.js"></script>
<!--<![endif]-->
<script data-cfasync="false" src="/base/Public/assets/js/app.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/bootstrap.min.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/jquery.superslide.2.1.1.js"></script>
<script data-cfasync="false" src="/base/Public/assets/js/jquery-1.11.2.min.js"></script>
</body>
</html>