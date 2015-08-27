<?php if (!defined('THINK_PATH')) exit();?>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->




  <!-- sidebar start -->
 <div class="am-cf admin-main">
 <link rel="stylesheet" href="/base/Public/assets/css/amazeui.min.css"/>
  <link rel="stylesheet" href="/base/Public/assets/css/admin.css">
    <link rel="stylesheet" href="/base/Public/assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="/base/Public/assets/css/y-css.css">
  <!-- sidebar end -->

  <!-- content start -->
  <div class="admin-content">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">商户列表</strong> / <small>Business List</small></div>
    </div>

    <div class="am-g">
      <div class="am-u-md-6 am-cf">
        <div class="am-fl am-cf">
          <div class="am-btn-toolbar am-fl">
            
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
                <th class="table-id">ID</th>
                <th class="table-title">企业名</th>
                <th class="table-title">企业电话</th>
                <th class="table-author">企业邮箱</th>
                <th class="table-author">审核状态</th>
                <th class="table-date">登录日期</th>
                <th class="table-set">操作</th>
              </tr>
          </thead>
          <tbody>
          <?php if(is_array($businessman_list)): foreach($businessman_list as $k=>$vo): ?><tr>
              <td><?php echo ($vo["id"]); ?></td>
              <td><a href="#"><?php echo ($vo["username"]); ?></a></td>
              <td><?php echo ($vo["phone"]); ?></td>
              <td><?php echo ($vo["email"]); ?></td>
              <td>
              <?php if($vo['status'] == '0'): ?>待审核
                <?php else: ?>已通过<?php endif; ?>
              </td>
              <td><?php echo (date("Y-m-d H:m",$vo["logintime"])); ?></td>   
              <td>
                <div class="am-btn-toolbar">
                  <div class="am-btn-group am-btn-group-xs">
                  <?php if(in_array(($businessman_verify_show), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href= <?php echo U('admin/member/businessman_verify_show',array('id'=>$vo['id']));?>><span class="am-icon-check"></span> 审核</a> |&nbsp;<?php endif; ?>
                <?php if(in_array(($businessman_edit_show), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('admin/member/businessman_edit_show',array('id'=>$vo['id']));?>><span class="am-icon-pencil-square-o"></span> 编辑</a> |&nbsp;<?php endif; ?>
                  <?php if(in_array(($businessman_del), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('admin/member/businessman_del',array('id'=>$vo['id']));?>>
                      <span class="am-icon-trash-o"></span> 删除</a><?php endif; ?>
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