<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class LiveController extends AdminController{
	//protected $autoCheckFields =false;

	public function live_list(){
        $live_model = M('live');  
        //获取总数
        $live_count = $live_model->count();

        //倒入分页类
        import('Think.Page');
        $page_class = new Page($live_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //为权限加上
        $actionName1["auth_a"]="live_list";
        $live_list = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="search";
        $search = $this->checkAuth($actionName2);
        $live_info = $live_model  ->select();

        //根据room_user来查询房主名称
        for($i=0;$i<$live_count;$i++){
        	$user_name['id'] = $live_info[$i]['room_user'];
        	$info = M('user')->where($user_name)->find();
        	$live_info[$i]['user_name'] = $info['user_name'];
        }

        //tag
        
        //dump($live_info);
        $this->assign('live_list',$live_list);
        $this->assign('page',$page);
         $this->assign('search',$search);
        $this->assign('live_info',$live_info);
		$this->display("Live/live_list");
	}

	public function search(){
		$reg_date1 = strtotime($_POST["reg_date"]);
                $reg_date2 = strtotime($_POST["reg_date2"]);
                $room_name = $_POST['room_name'];
                if($reg_date1 != "" && $reg_date2 !=""){
                    $map['add_date']  = array('between',array($reg_date1,$reg_date2));
                    $this->assign('reg_date',$_POST["reg_date"]);
                    $this->assign('reg_date2',$_POST["reg_date2"]);
                } 
                if($room_name !=""){
                    $map["room_name"] = array('like','%'.$room_name.'%');
                    $this->assign('room_name',$room_name);
                } 
                $model = M('live');
                $live_count = $model->where($map)->count();
                //导入分页类
                import('Think.Page');
                $page_class = new Page($report_count,15);
                $page_class->setConfig('prev', '«');
                $page_class->setConfig('next', '»');
                $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
                $page = $page_class->show();
                $live_info = $model->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
                //为权限加上
                
                $this->assign('gift_status_set',$gift_status_set);
                $this->assign('search',$search);
                $this->assign('page',$page);
                $this->assign('live_info',$live_info);
               
                $this->display("Live/live_list");  
                 
                

	}
}