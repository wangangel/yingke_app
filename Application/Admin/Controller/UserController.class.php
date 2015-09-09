<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class UserController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 用户列表
     */
    public function user_list() {
        $model_user = M('user');        
        //获取总数
        $user_count = $model_user->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($user_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $user_list = $model_user->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="user_detail";
        $user_detail = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="user_set";
        $user_set = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="user_set";
        $user_set = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="user_add_show";
        $user_add_show = $this->checkAuth($actionName4);
        $this->assign('user_add_show',$user_add_show);
        $this->assign('user_detail',$user_detail);
        $this->assign('user_set',$user_set);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('user_list',$user_list);
      	$this->display();
    }
    /**
     * 添加用户
     */
    /*public function user_add_show(){

    }*/
    /**
     * 用户详情
     * @return [type] [description]
     */
    public function user_detail(){
        $id = $_GET['id'];
        $result = M("comment")->delete($id);
        if($result){
            $this->success('操作成功！',U("admin/comment/comment_list"));
        }else{
            $this->error('操作失败',U("admin/comment/comment_list"));
        }

    }
    /**
     * 设置是否启用
     */
    public function user_set(){
        $data['id'] = $_POST["id"];
        $status = $_POST['status'];
        if($status == '0'){
            $data['status'] = 'stop';
        }else{
            $data['status'] = 'start';
        }
        $result = M("user")->save($data);
        $this->ajaxReturn($result,'JSON');
    }
    /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $phone_num = $_POST["phone_num"];
        $ni_name = $_POST["ni_name"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['reg_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }
        if($phone_num !=""){
            $map["phone_num"] = $phone_num;
            $this->assign('phone_num',$_POST["phone_num"]);
        }
        if($ni_name != ""){
            $map["ni_name"] = $ni_name;
            $this->assign('ni_name',$_POST["ni_name"]);
        }
        
        $model_user = M("user");
        //获取总数
        $user_count = $model_user->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($user_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $user_list = $model_user->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="user_detail";
        $user_detail = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="user_set";
        $user_set = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="user_set";
        $user_set = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="user_add_show";
        $user_add_show = $this->checkAuth($actionName4);
        $this->assign('user_add_show',$user_add_show);
        $this->assign('user_detail',$user_detail);
        $this->assign('user_set',$user_set);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('user_list',$user_list);
        $this->display("User/user_list");
    }


}