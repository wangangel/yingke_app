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
        $actionName2["auth_a"]="search";
        $search = $this->checkAuth($actionName2);
        $this->assign('search',$search);
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

    /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $apply_phone = $_POST["apply_phone"];
        $status = $_POST["status"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['apply_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }  
        if($apply_phone !=""){
            $map["apply_phone"] = $apply_phone;
            $this->assign('apply_phone',$apply_phone);
        }
        if($status != ""){
            $map["status"] = $status;
            $this->assign('status',$status);
        }
        $model_withdraw = M("withdrawals");
        //获取总数
        $withdraw_count = $model_withdraw->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($withdraw_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $withdraw_list = $model_withdraw->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="withdraw_set";
        $withdraw_set = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="search";
        $search = $this->checkAuth($actionName2);
        $this->assign('search',$search);
        $this->assign('withdraw_set',$withdraw_set);
        $this->assign('page',$page);
        $this->assign('withdraw_list',$withdraw_list);
        $this->display("Withdraw/withdraw_list");
    }
}