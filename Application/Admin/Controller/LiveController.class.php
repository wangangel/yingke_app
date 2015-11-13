<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class LiveController extends AdminController{
	//protected $autoCheckFields =false;

	public function live_list(){
        $live_model = M('live');  
        //获取总数
        $live_count = $live_model ->order('add_date desc')->count();

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
        $live_info = $live_model ->order('add_date desc') ->select();

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

/**
 * [search 直播列表页搜索]
 * @return [type] [description]
 */
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


    /**
     * 设置是否启用
     */
    public function live_set(){
        $data['id'] = $_POST["id"];
        $status = $_POST['status'];
        //这里只对房间进行停止或启用操作，若状态in  停止再启用后状态就是已完成
        if($status == 'stop'){
            $data['status'] = 'success';
        }else{
            $data['status'] = 'stop';
        }
        $result = M("live")->save($data);
        $this->ajaxReturn($result,'JSON');
    }

    /**
     * [live_detail 查看直播间详情]
     * @return [type] [description]
     */
    public function live_detail(){
        //根据前台传递id获取房间详情
        $data['id'] = $_REQUEST['id'];
        $live_model = M('live');
        $live_info = $live_model->where($data)->find();
        //根据房主id查询房主username
        $user_data['id'] = $live_info['user_room'];
        $user_info = M('user')->where($user_data)->find();
        $live_info['username'] = $user_info['ni_name'];
        //获取环信聊天记录
        $hx = new \Api\Common\HxController;
        $record = $hx->chatRecord('','', 50);
        $data = json_decode($record, true);
        //根据参数判断显示循环聊天记录
        $chat_data= $data['entities'];
        //获取该直播间的环信组id
        $groupid = $live_info['groupid'];
        for($i=0;$i<count($chat_data);$i++){
            if($chat_data[$i]['groupId']==$groupid){
              $opt .= $chat_data[$i]['from'].":".$chat_data[$i]['payload']['bodies'][0]['msg']."\n";
            }
        }
        //获取当前直播间的观众人数
        $userroom_model = M('user_room');
        $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$_REQUEST['id']))->select();
        $live_info['user_num'] = count($guanzhong_info);
        $live_info['chatRecord'] = $opt;
        $this->assign('live_info',$live_info);
        $this->display('Live/live_detail');
    }










}