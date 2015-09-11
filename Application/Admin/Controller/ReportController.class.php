<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class ReportController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 反馈展示
     */

    public function report_list() {
        $model_report = M('report');        
        //获取总数
        $report_count = $model_report->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($report_count,8);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $report_list = $model_report->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="search";
        $search = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="handle";
        $handle = $this->checkAuth($actionName2);
        $this->assign('handle',$handle);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('report_list',$report_list);
      	$this->display();
    }
    /**
     * 举报人处理
     * @return [type] [description]
     */
    public function handle(){
      /*  $id = $_GET['id'];
        $result = M("feedback")->delete($id);
        if($result){
            $this->success('操作成功！',U("admin/feedback/feedback_list"));
        }else{
            $this->error('操作失败',U("admin/feedback/feedback_list"));
        }*/

    }
    /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $phone_num = $_POST["f_phone"];
        $f_content = $_POST["f_content"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['f_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }  
        if($phone_num !=""){
            $map["f_phone"] = $phone_num;
            $this->assign('f_phone',$phone_num);
        }
        if($f_content != ""){
            $map["f_content"] = array('like','%'.$f_content.'%');
            $this->assign('f_content',$f_content);
        }
        $model_feedback = M("feedback");
        //获取总数
        $feedback_count = $model_feedback->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($feedback_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $feedback_list = $model_feedback->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="feedback_del";
        $feedback_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="search";
        $search = $this->checkAuth($actionName3);
        $this->assign('search',$search);
        $this->assign('feedback_del',$feedback_del);
        $this->assign('del_all',$del_all);
        $this->assign('page',$page);
        $this->assign('feedback_list',$feedback_list);
        $this->display("Feedback/feedback_list");
    }



}