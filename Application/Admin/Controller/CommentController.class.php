<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class CommentController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 评论展示
     */

    public function comment_list() {
        $model_comment = M('comment');        
        //获取总数
        $comment_count = $model_comment->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($comment_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $comment_list = $model_comment->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="comment_del";
        $comment_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="comment_set";
        $comment_set = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="search";
        $search = $this->checkAuth($actionName4);
        $this->assign('search',$search);
        $this->assign('comment_del',$comment_del);
        $this->assign('comment_set',$comment_set);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('comment_list',$comment_list);
      	$this->display();
    }
    /**
     * 删除
     * @return [type] [description]
     */
    public function comment_del(){
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
    public function comment_set(){
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
    /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $com_phone = $_POST["com_phone"];
        $com_content = $_POST["com_content"];
        $room_name = $_POST["room_name"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['com_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }  
        if($com_phone !=""){
            $map["com_phone"] = $com_phone;
            $this->assign('com_phone',$com_phone);
        }
        if($room_name !=""){
            $map["room_name"] = $room_name;
            $this->assign('room_name',$room_name);
        }
        if($com_content != ""){
            $map["com_content"] = array('like','%'.$com_content.'%');
            $this->assign('com_content',$com_content);
        }
        $model_comment = M("comment");
        //获取总数
        $comment_count = $model_comment->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($comment_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $comment_list = $model_comment->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="comment_del";
        $comment_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="comment_set";
        $comment_set = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="search";
        $search = $this->checkAuth($actionName4);
        $this->assign('search',$search);
        $this->assign('comment_del',$comment_del);
        $this->assign('comment_set',$comment_set);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('comment_list',$comment_list);
        $this->display("Comment/comment_list");
    }

}