<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class GiftController extends AdminController{

	public function user_gift_list(){
        $data['gift_sign'] = 'user';
	$gift_model = M('gift');        
        //获取总数
        $gift_count = $gift_model->where($data)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($gift_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();

        //为权限加上
        $actionName1["auth_a"]="user_gift_list";
        $user_gift_list = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="gift_status_set";
        $gift_status_set = $this->checkAuth($actionName2);
	$gift_info = $gift_model -> where($data) ->select();
        $this->assign('user_gift_list',$user_gift_list);
	$this->assign('gift_status_set',$gift_status_set);
	$this->assign('page',$page);
	$this->assign('gift_info',$gift_info);
	$this->display("Gift/user_gift_list");
	}


        public function gift_status_set(){
                $data['id'] = $_REQUEST['id'];
                $data['status'] = $_REQUEST['status'];
                $model = M('gift');
                $info = $model ->save($data);
                $this->ajaxReturn($info,'JSON');
        }

}