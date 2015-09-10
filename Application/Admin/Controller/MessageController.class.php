<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
require_once 'vendor/autoload.php';
//引用推送接口
use JPush\Model as M;
use JPush\JPushClient;
use JPush\JPushLog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;
class MessageController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 消息列表展示
     */
    public function message_list() {
        $model_message = M('message');        
        //获取总数
        $message_count = $model_message->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($message_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $message_list = $model_message->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="add_show";
        $add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="look_show";
        $look_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="message_del";
        $message_del = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName4);
        $actionName5["auth_a"]="search";
        $search = $this->checkAuth($actionName5);
        $this->assign('search',$search);
        $this->assign('del_all',$del_all);
        $this->assign('add_show',$add_show);
        $this->assign('look_show',$look_show);
        $this->assign('message_del',$message_del);
        $this->assign('page',$page);
        $this->assign('message_list',$message_list);
      	$this->display();
    }
    /**
     * 添加前
     */
    public function add_show() {
      $this->display();
    }

    /**
     * 发送
     */
    public function message_add(){
      $data["userid"] = $_SESSION['id'];
      $data["sendname"] = $_POST['sendname'];
      $data["title"] = $_POST['title'];
      $data["messcontent"] = $_POST['messcontent'];
      $data["messtype"] = $_POST['messtype'];
      $data["messagetime"] = time();
      $model_message = M('message');  
      $lastInsId = $model_message->add($data);
      $model_user = M("user");
      $userid_list = $model_user->where('attribute <> 0')->select();
      $model_usermessage = M("usermessage");
      $data2['status'] = 0;
      $data2['messageid'] = $lastInsId;
      $sum = count($userid_list);
      for ($i=0; $i < $sum ; $i++) { 
        $userid = $userid_list[$i]['id'];
        $data2["userid"] = $userid;
        $model_usermessage->add($data2);
      }
        
        
/*
        $br = '<br/>';
        $spilt = ' - ';
        $master_secret = 'b83fa7d7223af28c13c6d2d6';
        $app_key='88defe3f1137c4d3c9da2fc4';
        JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
        $client = new JPushClient($app_key, $master_secret);
        //easy push
        try {
            $result = $client->push()
                ->setPlatform(M\all)
                ->setAudience(M\all)
                ->setNotification(M\notification('Hi, JPush'))
                ->printJSON()
                ->send();
            echo 'Push Success.' . $br;
            echo 'sendno : ' . $result->sendno . $br;
            echo 'msg_id : ' .$result->msg_id . $br;
            echo 'Response JSON : ' . $result->json . $br;
        } catch (APIRequestException $e) {
            echo 'Push Fail.' . $br;
            echo 'Http Code : ' . $e->httpCode . $br;
            echo 'code : ' . $e->code . $br;
            echo 'Error Message : ' . $e->message . $br;
            echo 'Response JSON : ' . $e->json . $br;
            echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
            echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
            echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
        } catch (APIConnectionException $e) {
            echo 'Push Fail: ' . $br;
            echo 'Error Message: ' . $e->getMessage() . $br;
            //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
            echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
        }

        echo $br . '-------------' . $br;*/



      if($lastInsId){
        $this->success('发送成功！',U("admin/message/message_list"));
      }else{
        $this->error('发送失败！',U("admin/message/message_list"));
      }
    }
    /**
     * 查看
     */
    public function look_show(){
      $data["id"] = $_GET['id'];
      $model_message = M('message'); 
      $message_info =  $model_message->where($data)->find();
      $this->assign('message_info',$message_info);
      $this->display();
    }

   /**
    * 根据id进行删除--并将其在用户已查阅的表进行删除
    */
   
   public function message_del(){
   		$model_message = M('message');
      $data['id'] = $_GET['id'];
      $result = $model_message->where($data)->delete();
      if($result){
            $this->success('操作成功！',U("admin/message/message_list"));
      }else{
            $this->error('操作失败',U("admin/message/message_list"));
      }

   }
   /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $m_content = $_POST["m_content"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['m_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }  
        if($m_content != ""){
            $map["m_content"] = array('like','%'.$m_content.'%');
            $this->assign('m_content',$m_content);
        }
        $model_message = M("message");
        //获取总数
        $message_count = $model_message->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($message_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $message_list = $model_message->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="add_show";
        $add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="look_show";
        $look_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="message_del";
        $message_del = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="del_all";
        $del_all = $this->checkAuth($actionName4);
        $actionName5["auth_a"]="search";
        $search = $this->checkAuth($actionName5);
        $this->assign('search',$search);
        $this->assign('del_all',$del_all);
        $this->assign('add_show',$add_show);
        $this->assign('look_show',$look_show);
        $this->assign('message_del',$message_del);
        $this->assign('page',$page);
        $this->assign('message_list',$message_list);
        $this->display("message/message_list");
    }

}