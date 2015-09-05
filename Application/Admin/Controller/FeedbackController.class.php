<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class FeedbackController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 反馈展示
     */

    public function feedback_list() {
        $model_advice = M('feedback');        
        //获取总数
        $advice_count = $model_advice->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($advice_count,8);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $feedback_list = $model_advice->limit($page_class->firstRow.','.$page_class->listRows)->select();

         //为权限加上
        $actionName1["auth_a"]="feedback_del";
        $feedback_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName2);
        $this->assign('feedback_del',$feedback_del);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('feedback_list',$feedback_list);
      	$this->display();
    }
    /**
     * 删除
     * @return [type] [description]
     */
    public function feedback_del(){
        $id = $_GET['id'];
        $result = M("feedback")->delete($id);
        if($result){
            $this->success('操作成功！',U("admin/feedback/feedback_list"));
        }else{
            $this->error('操作失败',U("admin/feedback/feedback_list"));
        }

    }

}