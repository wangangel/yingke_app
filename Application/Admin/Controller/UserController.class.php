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
        $actionName1["auth_a"]="comment_del";
        $comment_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="comment_set";
        $comment_set = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName3);
        $this->assign('comment_del',$comment_del);
        $this->assign('comment_set',$comment_set);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('user_list',$user_list);
      	$this->display();
    }
    /**
     * 删除
     * @return [type] [description]
     */
    public function user_del(){
        $id = $_GET['id'];
        $result = M("comment")->delete($id);
        if($result){
            $this->success('操作成功！',U("admin/comment/comment_list"));
        }else{
            $this->error('操作失败',U("admin/comment/comment_list"));
        }

    }
    /**
     * 设置是否显示或者不显示
     */
    public function user_set(){
        $data['id'] = $_POST["id"];
        $status = $_POST['status'];
        if($status == '0'){
            $data['is_display'] = 'yes';
        }else{
            $data['is_display'] = 'no';
        }
        $result = M("comment")->save($data);
        $this->ajaxReturn($result,'JSON');
    }

}