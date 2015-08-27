<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
    public function _initialize(){
		header("Content-type:text/html;charset=utf-8");
		import('Vendor.Alipay.alipayCore','','.php');
		import('Vendor.Alipay.alipayMd5','','.php');
		import('Vendor.Alipay.alipayNotify','','.php');
		import('Vendor.Alipay.alipaySubmit','','.php');
    }
	//提交订单入口
    public function userspay(){
    	if(isset($_SESSION['id'])){
	    	$data['userid']		= $_SESSION['id']; //用户账号
			$updata['paytime']	= time(); //订单记录产生时间
	    	$orderscash = M('orderpay');
	    	$result = $orderscash->data($data)->add();
	    	if($result){
	    		$codeno = $_POST['ordernum'];
	    		//dump($_POST);
	    		//die;
	    		$jfee = (float) $_POST['jine']; //强制金额浮点数
	    		$goodsname = $_POST['goodsname'];
	    		$updata['orderid']		= $_POST['ordernum'];
	    		$total_fee = number_format($jfee,2);
	   			$updata['paymoney']			=  $total_fee;//精确到小数点后两位
	    		$updata['orderstatus'] 		= 0;	//未支付状态
	    		$upresult = $orderscash->where(array('id'=>$result))->save($updata);
	    		if($upresult){
			    	$configs = array(
						'return_url'	=>'http://www.edeco.cc/index.php?m=home&c=Pay&a=usersurl',//U('Home/Pay/usersurl'),	//服务器同步通知页面路径(必填) 
						'notify_url'	=>U('Home/Pay/notifyurl'),	//服务器异步通知页面路径(必填)     //若模块下配置文件已经配置则注释掉
						'out_trade_no'	=>$codeno,	//商户订单号(必填)
						'subject'		=>$goodsname,	//订单名称(必填)
						'total_fee'		=>$total_fee,	//付款金额(必填)
						'body'			=>'',	//订单描述
						'show_url'		=>'',	//商品展示地址
		    		);
			    	//调用支付宝接口
			    	//dump($configs);
			    	$this->alipayapi($configs);
	    		}else{ //付款异常(序号)，请联系客服或管理员。
	    			$this->error('付款订单异常，订单号【'.$_POST['ordernum'].'】。');
	    		}
	    	}else{
	    		$this->error('付款订单异常，未生成订单。');
	    	}
    	}else{
			$this->error('登录状态异常，无法提交付款操作！');    		
    	}
    }
	
	//alipay支付接口  //参数额外配置数组$configs
	public function alipayapi($configs){
		/****************************************************/
		//>>>>>>>>>>>>第一步
		//根据alipay源文件加载顺序依次加载配置
		$alipay_config = C('alipay_config');

		/**************************请求参数配置**************************/
        //支付类型
        $payment_type = C('alipay.payment_type');
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = C('alipay.notify_url');
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //卖家支付宝帐户
        $seller_email = C('alipay.seller_email');

        //必填
        //页面跳转同步通知页面路径
        $return_url = $configs['return_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		/****************************************************/
		//>>>>>>>>>>>>第二步
		//接收动态订单数据
        //商户订单号
        $out_trade_no = $configs['out_trade_no'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $configs['subject'];
        //必填

        //付款金额
        $total_fee = $configs['total_fee'];
        //必填

        //订单描述
        $body = $configs['body'];
        //商品展示地址
        $show_url = $configs['show_url'];
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

		$alipaySubmit = new \AlipaySubmit($alipay_config);
        //防钓鱼时间戳
        $anti_phishing_key = '';//$alipaySubmit->query_timestamp();
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = '';//get_client_ip();   //Thinkphp3.2 系统集成的获取客户端ip方法
        //非局域网的外网IP地址，如：221.0.0.1
		/************************************************************/
		//>>>>>>>>>>>>第三步
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);

		//建立请求
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
	}

	public function usersurl(){
		//计算得出通知验证结果
		$alipay_config = C('alipay_config');   //必须

		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];

			//支付宝交易号
			$trade_no = $_GET['trade_no'];

			//交易状态
			$trade_status = $_GET['trade_status'];

		    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				$orderscash = M('order');
				$curinfo = $orderscash->where(array('ordernum'=>$out_trade_no))->find();
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
					//根据付款成功来变更状态
					$data['orderstatus'] = 1;
					$data1['ordernum'] = $out_trade_no;
					$order_info = $orderscash ->where($data1)->save($data);
					$this->success("付款成功!",U('Home/Pay/successPage',array('ordernum'=>$out_trade_no)));
				
		    }
		    else {
				//echo "trade_status=".$_GET['trade_status'];
				echo '非法状态！';
		    }
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的verifyReturn函数
		    $this->success("付款成功!",U('Home/Pay/successPage',array('ordernum'=>$out_trade_no)));
		}
	}

	public function notifyurl(){
		//计算得出通知验证结果
		$alipay_config = C('alipay_config');	//必须

		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


		    if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//该种交易状态只在两种情况下出现
				//1、开通了普通即时到账，买家付款成功后。
				//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

		        //调试用，写文本函数记录程序运行情况是否正常
		        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		    }
		    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。

		        //调试用，写文本函数记录程序运行情况是否正常
		        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		    	//判断该笔订单是否在商户网站中已经做过处理
				$orderscash = M('order');
				$curinfo = $orderscash->where(array('ordernum'=>$out_trade_no))->find();

				if($curinfo['orderstatus'] == 0){ //jstate=0未支付状态 jgroup=1商户会员					
					/*
					 *
					 *	这里处理订单业务,并对特殊事件记录日志
					 *
					 **/						
				}
				if($curinfo['orderstatus'] ==0){ //jstate=0未支付状态 jgroup=0普通会员
					/*
					 *
					 *	这里处理订单业务,并对特殊事件记录日志
					 *
					 **/
				}
		    }

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		        
			echo "success";		//请不要修改或删除
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
		    //验证失败
		    echo "fail";

		    //调试用，写文本函数记录程序运行情况是否正常
		    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}


/**
 * [successPage 跳转到支付成功页面]
 * @return [type] [description]
 */
	public function successPage(){
		$this->assign("ordernum",$_GET['ordernum']);
		$this->display("pay_success");
	}


	public function Write_express(){
		$this->assign("ordernum",$_GET['ordernum']);
		$this->assign('usr',$_SESSION['usr']);
		$this->display("Order/success_detail");
	}
}