<?php
namespace Home\Controller;
use Think\Controller;
class ThirdLoginController extends Controller {

/**
 * [show_bind 显示绑定页面]
 * @return [type] [description]
 */
	public function show_bind(){
		$openid = $_GET['openid'];
		$type = $_GET['type'];
		$this->assign('openid',$openid);
		$this->assign('type',$type);
		$this->assign('id',$id);
        $this->assign('usr',$_SESSION['usr']);
        $this->display("Bind/bind");
	}
/**
 * [login 第三方登录]
 * @return [type] [description]
 */
	public function login(){
      if($_GET){
      			$type = $_GET['type'];
                $openid = $_GET['openid'];
 				$data1[$type.'openid'] = $openid;
 				$model_user = M('user');
                $user_info = $model_user->where($data1)->find();
                //dump($user_info);
                if($user_info){
                    $arr = array();
                    $arr['id'] = $user_info['id'];
                    $arr['logintime'] = time();
                    $result = $model_user->save($arr);
                    $_SESSION['username'] = $user_info['username'];
                    $_SESSION['id'] = $user_info['id'];
                    $_SESSION['userid'] = $user_info['id'];
                    $_SESSION['login_time'] = date("Y-m-d H:i:s", time());
                    $_SESSION['isdelete']=0;
                    //查系统配置信息
                    $model_sys = M("sysconfig");
                    $sys_ = $model_sys->where('id=1')->find();
                    $_SESSION['tel'] = $sys_['tel'];
                    $_SESSION['email'] = $sys_['email'];
                    $_SESSION['record'] = $sys_['record'];
                    //查询还有几条信息未读！
                    $model_usermessage = M("usermessage");
                    $data["userid"] = $user_info['id'];
                    $data["status"] = 0;
                    $_SESSION['message_count'] = $model_usermessage->where($data)->count();

                    $data['userid'] = $user_info['id'];
                    $data['flag'] = 1;
                    $_SESSION['cart_count'] = M('cart') ->where($data)->count();
                    $this->success('登录成功！',U('Home/index/index')); 
                    die;
                }else{
                    $this->error('账号或者密码错误！');
                }
            
            
        }
        
        $this->display();
        
    }


}
   
