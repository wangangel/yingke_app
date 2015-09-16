<?php
namespace Home\Controller;
use Admin\Common\AdminController;
use Think\Controller;
class IndexController extends AdminController {
    public function index(){
    	if($_SESSION['admin_id'] == null){
    		$this->success('正在进入...',U('admin/login/login'));
    	}
    }
}