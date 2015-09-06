<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class WithdrawController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 评论展示
     */
    public function withdraw_list() {
        $model_withdraw = M('withdrawals');        
        //获取总数
        $withdraw_count = $model_withdraw->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($withdraw_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $withdraw_list = $model_withdraw->limit($page_class->firstRow.','.$page_class->listRows)->select();

        //为权限加上
        $actionName1["auth_a"]="withdraw_set";
        $withdraw_set = $this->checkAuth($actionName1);
        $this->assign('withdraw_set',$withdraw_set);
        $this->assign('page',$page);
        $this->assign('withdraw_list',$withdraw_list);
      	$this->display();
    }
    
    //确认转账
    public function withdraw_set(){
        $data['id'] = $_POST["id"];
        $data["status"] = "yes";
        $data["yes_time"] = time();
        $res = M("withdrawals")->save($data);
        $this->ajaxReturn($res,"JSON");
    }
}