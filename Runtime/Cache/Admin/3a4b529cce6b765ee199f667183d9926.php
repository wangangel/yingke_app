<?php if (!defined('THINK_PATH')) exit();?>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->

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
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">系统配置</strong> / <small>System Set</small></div>
    </div>

    <hr/>

    <div class="am-g">

      <div class="am-u-sm-12 am-u-md-4 am-u-md-push-8">
       

      </div>

       <div class="am-u-sm-12 am-u-md-8 am-u-md-pull-4">
        <form class="am-form am-form-horizontal" action=<?php echo U("admin/system/saveorupdate");?> method='post'>

          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">站点名称 / State Name</label>
            <div class="am-u-sm-9">
              <input type="text" id="category-name" placeholder="站点名称 / State Name" name='statename' value="<?php echo ($system_config["statename"]); ?>">
            </div>
          </div>

          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">站点简称 / Simple Name</label>
            <div class="am-u-sm-9">
              <input type="text" id="category-name" placeholder="站点简称 / Simple Name" name='simplename' value="<?php echo ($system_config["simplename"]); ?>">
            </div>
          </div>
          
          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">描述 / Description</label>
            <div class="am-u-sm-9">
              <input type="text" id="category-name" placeholder="描述 / Description" name='des'
              value="<?php echo ($system_config["des"]); ?>">
            </div>
          </div>
          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">官网电话 / Tel</label>
            <div class="am-u-sm-9">
              <input type="text" id="tel" placeholder="官网电话 / Tel" name='tel' value="<?php echo ($system_config["tel"]); ?>">
            </div>
          </div>
          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">官网邮箱 / Email</label>
            <div class="am-u-sm-9">
              <input type="text" id="email" placeholder="官网邮箱 / Email" name='email' value="<?php echo ($system_config["email"]); ?>">
            </div>
          </div>
          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">域名 / Domain</label>
            <div class="am-u-sm-9">
              <input type="text" id="category-name" placeholder="域名 / Domain" name='domains'
              value="<?php echo ($system_config["domains"]); ?>">
            </div>
          </div>
           <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">备案 / Record</label>
            <div class="am-u-sm-9">
              <input type="text" id="record" placeholder="备案 / Record" name='record'
              value="<?php echo ($system_config["record"]); ?>">
            </div>
          </div>
          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">路径 / Rescouse Path</label>
            <div class="am-u-sm-9">
              <input type="text" id="category-name" placeholder="路径 / Rescouse Path" name='repath' value="<?php echo ($system_config["repath"]); ?>">站点目录名称
            </div>
          </div>
          <div class="am-form-group">
            <label for="user-category" class="am-u-sm-3 am-form-label">公司地址 / Address</label>
            <div class="am-u-sm-9">
              <input type="text" id="address" placeholder="公司地址 / Address" name='address' value="<?php echo ($system_config["address"]); ?>">
            </div>
          </div>

         <!--  <div class="am-form-group">
            <label for="user-name" class="am-u-sm-3 am-form-label">访问协议 Access</label>
            <div class="am-u-sm-9">
              <select name="accesspr" id="statusid" >
                 <?php if($system_config["accesspr"] == '0'): ?><option value="0" selected="selected">http://</option>
                    <option value="1" >https://</option>
                    <?php elseif($system_config["accesspr"] == '1'): ?>
                      <option value="0" >http://</option>
                      <option value="1" selected="selected">https://</option>
                    <?php else: ?>
                      <option value="0" >http://</option>
                      <option value="1" >https://</option><?php endif; ?>
              </select>
                
            </div>
          </div> -->
          <input type="hidden" name="id" value="<?php echo ($system_config["id"]); ?>"/>
          <div class="am-form-group">
            <?php if(in_array(($saveorupdate), is_array($hava_authids)?$hava_authids:explode(',',$hava_authids))): ?><div class="am-u-sm-9 am-u-sm-push-3">
              <button type="submit" class="am-btn am-btn-primary">更新</button>
            </div><?php endif; ?>

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