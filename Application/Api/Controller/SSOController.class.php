<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;

class SSOController extends MobileController{
    public function _initialize() {
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit');    
    }


//doalipay方法
        /*该方法其实就是将接口文件包下alipayapi.php的内容复制过来
          然后进行相关处理
        */
    public function alipay(){
       //这里我们通过TP的C函数把配置项参数读出，赋给$alipay_config；
       $alipay_config=C('alipay_config');  
        /**************************请求参数**************************/
        $payment_type = "1"; //支付类型 //必填，不能修改
        $notify_url = C('alipay.notify_url'); //服务器异步通知页面路径
        $return_url = C('alipay.return_url'); //页面跳转同步通知页面路径
        $seller_email = C('alipay.seller_email');//卖家支付宝帐户必填
        $out_trade_no = $_POST['trade_no'];//商户订单号 通过支付页面的表单进行传递，注意要唯一！
        $subject = $_POST['ordsubject'];  //订单名称 //必填 通过支付页面的表单进行传递
        $total_fee = $_POST['ordtotal_fee'];   //付款金额  //必填 通过支付页面的表单进行传递
        $body = $_POST['ordbody'];  //订单描述 通过支付页面的表单进行传递
     
        /************************************************************/
    
        //构造要请求的参数数组，无需改动
    $parameter = array(
        "service" => "create_direct_pay_by_user",
        "partner" => trim($alipay_config['partner']),
        "payment_type"    => $payment_type,
        "notify_url"    => $notify_url,
        "return_url"    => $return_url,
        "seller_email"    => $seller_email,
        "out_trade_no"    => $out_trade_no,
        "subject"    => $subject,
        "total_fee"    => $total_fee,
        "body"            => $body,
        "show_url"    => $show_url,
        "anti_phishing_key"    => $anti_phishing_key,
        "exter_invoke_ip"    => $exter_invoke_ip,
        "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );
        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
        echo $html_text;
    }
    
        /******************************
        服务器异步通知页面方法
        其实这里就是将notify_url.php文件中的代码复制过来进行处理
        
        *******************************/
    function notifyurl(){
                /*
                同理去掉以下两句代码；
                */ 
                //require_once("alipay.config.php");
                //require_once("lib/alipay_notify.class.php");
                
                //这里还是通过C函数来读取配置项，赋值给$alipay_config
        $alipay_config=C('alipay_config');
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {
               //验证成功
                   //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
           $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
           $trade_no       = $_POST['trade_no'];          //支付宝交易号
           $trade_status   = $_POST['trade_status'];      //交易状态
           $total_fee      = $_POST['total_fee'];         //交易金额
           $notify_id      = $_POST['notify_id'];         //通知校验ID。
           $notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
           $buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
                   $parameter = array(
             "out_trade_no"     => $out_trade_no, //商户订单编号；
             "trade_no"     => $trade_no,     //支付宝交易号；
             "total_fee"     => $total_fee,    //交易金额；
             "trade_status"     => $trade_status, //交易状态
             "notify_id"     => $notify_id,    //通知校验ID。
             "notify_time"   => $notify_time,  //通知的发送时间。
             "buyer_email"   => $buyer_email,  //买家支付宝帐号；
           );
           if($_POST['trade_status'] == 'TRADE_FINISHED') {
                       //
           }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {                           if(!checkorderstatus($out_trade_no)){
               orderhandle($parameter); 
                           //进行订单处理，并传送从支付宝返回的参数；
               }
            }
            output_data("支付成功！");
                echo "success";        //请不要修改或删除
         }else {
         	output_error("支付失败");
                //验证失败
                echo "fail";
        }    
    }
    
    /*
        页面跳转处理方法；
        这里其实就是将return_url.php这个文件中的代码复制过来，进行处理； 
        */
    function returnurl(){
                //头部的处理跟上面两个方法一样，这里不罗嗦了！
        $alipay_config=C('alipay_config');
        $alipayNotify = new AlipayNotify($alipay_config);//计算得出通知验证结果
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {
            //验证成功
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        $out_trade_no   = $_GET['out_trade_no'];      //商户订单号
        $trade_no       = $_GET['trade_no'];          //支付宝交易号
        $trade_status   = $_GET['trade_status'];      //交易状态
        $total_fee      = $_GET['total_fee'];         //交易金额
        $notify_id      = $_GET['notify_id'];         //通知校验ID。
        $notify_time    = $_GET['notify_time'];       //通知的发送时间。
        $buyer_email    = $_GET['buyer_email'];       //买家支付宝帐号；
            
        $parameter = array(
            "out_trade_no"     => $out_trade_no,      //商户订单编号；
            "trade_no"     => $trade_no,          //支付宝交易号；
            "total_fee"      => $total_fee,         //交易金额；
            "trade_status"     => $trade_status,      //交易状态
            "notify_id"      => $notify_id,         //通知校验ID。
            "notify_time"    => $notify_time,       //通知的发送时间。
            "buyer_email"    => $buyer_email,       //买家支付宝帐号
        );
        
 if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        if(!checkorderstatus($out_trade_no)){
             orderhandle($parameter);  //进行订单处理，并传送从支付宝返回的参数；
    }
        $this->redirect(C('alipay.successpage'));//跳转到配置项中配置的支付成功页面；
    }else {
        echo "trade_status=".$_GET['trade_status'];
        $this->redirect(C('alipay.errorpage'));//跳转到配置项中配置的支付失败页面；
    }

 }else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    output_error("支付失败");
    echo "支付失败！";
    }
 }
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
            $userinfo['server_code'] = "QXSJSP";
            $user_model = M('user');
            $user_info = $user_model->add($userinfo);
            if($user_info){
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
                        'hx_password' =>md5($_REQUEST['password']),
                        'server_code' => "QXSJSP"
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

    //登录地址
	public function login($type = null){
		empty($type) && $this->error('参数错误');

		//加载ThinkOauth类并实例化一个对象
		import("ORG.ThinkSDK.ThinkOauth");
		$sns  = \ThinkOauth::getInstance($type);

		//跳转到授权页面
		redirect($sns->getRequestCodeURL());
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