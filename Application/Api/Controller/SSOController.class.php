<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;

class SSOController extends MobileController{


	  //登录成功，微信用户信息
    public function Third_login(){
    	$type = $_REQUEST['type'];
    	$token = $_REQUEST['token'];
    	if($type == 'weixin'){
    		 $userInfo['reg_type'] = '微信';
    	}else if($type == 'sina'){
    		 $userInfo['reg_type'] = '微博';
    	}
    	import('Org.ThinkSDK.ThinkOauth');
        $weixin   = \ThinkOauth::getInstance($type, $token);
        if($type == 'weixin'){
			$data = $weixin->call('sns/userinfo');
        }
        if($data['ret'] == 0){
        	$user = $this->getRandOnlyId();
            $userInfo['nickname'] = $data['nickname'];
            $userInfo['head_url'] = $data['headimgurl'];
            $userinfo['username'] =$user;
            $userinfo['phone'] =$user;
            $userinfo['password'] =md5($user);
            $user_model = M('user');
            $user_info = $user_model->add($userinfo);
            if(){
            	//用户注册成功后,同时注册环信
                $hx_opt['password']=md5($user);
                $hx_opt['username']=$user;
                $HX = new \Api\Common\HxController;
                $hx_info = $HX->openRegister($hx_opt);
                $hx_a = json_decode($hx_info,true);
                $hx_save['id'] = $user_info;
                $hx_save['hx_password']=md5($user);
                $hx_save['hx_user']=$user;
                $hx_info = $user_model ->save($hx_save);
                output_data(array(
                        'userid' => $user_info,
                        'phone'=>$user,
                        'nickname' => $data['nickname'],
                        'password'=>md5($user),
                        'hx_user' =>$_REQUEST['phonenumber'],
                        'hx_password' =>md5($_REQUEST['password'])
                        ));
            }else{
            	output_error('第三方登录失败');
            }
                
        } else {
            output_error("获取第三方用户信息失败：{$data['errmsg']}");
        }
    }


    //时间戳+随即说得到唯一id
    function getRandOnlyId() {
        //新时间截定义,基于世界未日2012-12-21的时间戳。
        $endtime=1356019200;//2012-12-21时间戳
        $curtime=time();//当前时间戳
        $newtime=$curtime-$endtime;//新时间戳
        $rand=rand(0,99);//两位随机
        $all=$rand.$newtime;
        return $all;
    }
}