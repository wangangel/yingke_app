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
        $data["id"] = $_POST['id'];
        $data["suggestion"] = $_POST["suggestion"];
        if($_POST["status"] == "notreal"){
            $data["result"] = "举报不实";
        }elseif($_POST["status"] == "gag"){//停用房间，房主一周不能发言
            $data["result"] = "查实禁言一周";
            $live_arr["status"] = "stop";
            $user_arr["deal_status"] = "gag";
        }elseif($_POST["status"] == "dark"){//小黑屋是不能进别人的房间
            $data["result"] = "查实小黑屋一周";
            $user_arr["deal_status"] = "dark";
        }elseif($_POST["status"] == "letter"){//房间停止，提现，停掉一切功能
            $data["result"] = "查实封款账号";
            $live_arr["status"] = "stop";
            $user_arr["deal_status"] = "letter";
        }
        $report = M("report")->find($_POST['id']);
        $report_list = M("report")->where($report["room_id"])->select();
        for ($i=0; $i < count($report_list) ; $i++) { 
            $data["id"] = $report_list[$i]["id"];
            $report_res = M("report")->save($data);
         } 
        $record["room_id"] = $report["room_id"];
        $record["change_reason"] = $data["suggestion"];
        $record["change_date"] = time();
        $record["change_user"] = $_SESSION['admin_name'];
        $record["change_status"] = $data["result"];
        $record_res = M("room_stop_record")->add($record);
        if($_POST["status"] != "notreal"){
            $live = M("live")->find($report["room_id"]);
            $user_arr["id"] =  $live["room_user"];
            $user_arr["deal_time"] = time();
            $user_res = M("user")->save($user_arr);
        }
        if($_POST["status"] == "gag" || $_POST["status"] == "letter"){
            $live_arr["id"] = $report["room_id"];
            $live_res = M("live")->save($live_arr);
        }
        $res = "1";
        $this->ajaxReturn($res,"JSON");
    }
    /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $re_phone = $_POST["re_phone"];
        $re_reason = $_POST["re_reason"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['re_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }  
        if($phone_num !=""){
            $map["re_phone"] = $re_phone;
            $this->assign('re_phone',$re_phone);
        }
        if($re_reason != ""){
            $map["re_reason"] = array('like','%'.$re_reason.'%');
            $this->assign('re_reason',$re_reason);
        }
        $model_report = M("report");
        //获取总数
        $report_count = $model_report->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($report_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $report_list = $model_report->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="search";
        $search = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="handle";
        $handle = $this->checkAuth($actionName2);
        $this->assign('handle',$handle);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('report_list',$report_list);
        $this->display("Report/report_list");
    }



}