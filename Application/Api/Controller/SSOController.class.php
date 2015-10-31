<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;
use Org\ThinkSDK\ThinkOauth;

class SSOController extends MobileController{


	 //登录成功，微信用户信息
    public function Third_login(){
    	$type = $_REQUEST['tyep'];
    	$token = $_REQUEST['token'];
    	import("Org.ThinkSDK.ThinkOauth");
        $weixin = \ThinkOauth::getInstance($type,$token);
        $data = $weixin->call('sns/userinfo');
        dump($weixin);
        if($data['ret'] == 0){
            $userInfo['reg_type'] = 'WEIXIN';
            $userInfo['name'] = $data['nickname'];
            $userInfo['nick'] = $data['nickname'];
            $userInfo['head_url'] = $data['headimgurl'];
            return $userInfo;
        } else {
            throw_exception("获取微信用户信息失败：{$data['errmsg']}");
        }
    }


//授权回调地址
	public function callback($type = null, $code = null){
		(empty($type) || empty($code)) && $this->error('参数错误');
		
		//加载ThinkOauth类并实例化一个对象
		import("ORG.ThinkSDK.ThinkOauth");
		$sns  = ThinkOauth::getInstance($type);

		//腾讯微博需传递的额外参数
		$extend = null;
		if($type == 'tencent'){
			$extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
		}

		//请妥善保管这里获取到的Token信息，方便以后API调用
		//调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
		//如： $qq = ThinkOauth::getInstance('qq', $token);
		$token = $sns->getAccessToken($code , $extend);

		//获取当前登录用户信息
		if(is_array($token)){
			$user_info = A('Type', 'Event')->$type($token);

			echo("<h1>恭喜！使用 {$type} 用户登录成功</h1><br>");
			echo("授权信息为：<br>");
			dump($token);
			echo("当前登录用户信息为：<br>");
			dump($user_info);
		}
	}   

}