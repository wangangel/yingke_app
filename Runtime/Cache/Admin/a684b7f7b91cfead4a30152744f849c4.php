<?php if (!defined('THINK_PATH')) exit();?>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->
<style type="text/css">
  .server_search{
    float: right;
  }
</style>
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
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">权限列表</strong> / <small>Auth List</small></div>
    </div>

    <div class="am-g">
      <div class="am-u-md-6 am-cf">
        <div class="am-fl am-cf">
          <div class="am-btn-toolbar am-fl">
            <div class="am-btn-group am-btn-group-xs">

            <?php if(in_array(($auth_add_show), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U("admin/auth/auth_add_show");?>><button type="button" class="am-btn am-btn-default"><span class="am-icon-plus"></span> 新增</button></a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="server_search">
      <?php if(in_array(($auth_search), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><form class="am-form-inline" role="form" id="searchForm" action=<?php echo U("admin/auth/auth_search");?> method="post" >
          <div class="am-form-group">
                <input type="text" name="auth_name" placeholder="请输入权限全称！"/>
          </div>
          <input type="submit" class="am-btn am-btn-default" value="筛选"/>
        </form><?php endif; ?>
    </div>
    <div class="am-g">
      <div class="am-u-sm-12">
        <form class="am-form">
          <table class="am-table am-table-striped am-table-hover table-main">
            <thead>
              <tr>
                <th class="table-id">序号</th>
                <th class="table-title">权限名称</th>
                <th class="table-author">模块</th>
                <th class="table-date">操作方法</th>
                <th class="table-set">是否菜单</th>
                <th class="table-date">路径</th>
                <th class="table-set">级别</th>
                
                <th class="table-set">操作</th>
              </tr>
          </thead>
          <tbody>
          <?php if(is_array($info)): foreach($info as $k=>$vo): ?><!-- <?php if($vo["auth_level"] == '0'): endif; ?> -->
              <tr>
                <td><?php echo ($k+1); ?></td>
                <td><?php echo ($vo["auth_name"]); ?></td>
                <td><?php echo ($vo["auth_c"]); ?></td>
                <td><?php echo ($vo["auth_a"]); ?></td>
                <td><?php if($vo["is_menu"] == 0): ?>否<?php else: ?>是<?php endif; ?></td>
                <td><?php echo ($vo["auth_path"]); ?></td>
                <td><?php echo ($vo["auth_level"]); ?></td>
                
                <td>
                  <div class="am-btn-toolbar">
                    <div class="am-btn-group am-btn-group-xs">
                    <?php if(in_array(($auth_edit_show), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('admin/auth/auth_edit_show',array('id'=>$vo['auth_id']));?>><span class="am-icon-pencil-square-o"></span> 编辑  </a>|&nbsp;<?php endif; ?>
                    <?php if(in_array(($auth_del), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><a href=<?php echo U('admin/auth/auth_del',array('id'=>$vo['auth_id']));?>><span class="am-icon-trash-o"></span> 删除</a><?php endif; ?>
                    </div>
                  </div>
                </td>
              </tr><?php endforeach; endif; ?>
            
          </tbody>
        </table>
     
    <?php echo ($page); ?>
    注：级别0为顶级权限，依次递减！
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