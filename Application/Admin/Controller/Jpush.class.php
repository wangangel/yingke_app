<?php
/**
 * 极光推送
 * @author 晨曦
 * @Email jakehu1991@163.com
 * @Website http://www.jakehu.me/
 * @version 20130706
 */
 

class jpush {
	private $_masterSecret = '';
	private $_appkeys = '';
	
	/**
	 * 构造函数
	 * @param string $username
	 * @param string $password
	 * @param string $appkeys
	 */
	function __construct($masterSecret = '',$appkeys = '') {
		$this->_masterSecret = $masterSecret;
		$this->_appkeys = $appkeys;
	}
	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param string $param
	 */
	function request_post($url = '', $param = '') {
		if (empty($url) || empty($param)) {
			return false;
		}
		
		$postUrl = $url;
		$curlPost = $param;
		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
		curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$data = curl_exec($ch);//运行curl
		curl_close($ch);
		
		return $data;
	}
	/**
	 * 发送
	 * @param int $sendno 发送编号。由开发者自己维护，标识一次发送请求
	 * @param int $receiver_type 接收者类型。1、指定的 IMEI。此时必须指定 appKeys。2、指定的 tag。3、指定的 alias。4、 对指定 appkey 的所有用户推送消息。
	 * @param string $receiver_value 发送范围值，与 receiver_type相对应。 1、IMEI只支持一个 2、tag 支持多个，使用 "," 间隔。 3、alias 支持多个，使用 "," 间隔。 4、不需要填
	 * @param int $msg_type 发送消息的类型：1、通知 2、自定义消息
	 * @param string $msg_content 发送消息的内容。 与 msg_type 相对应的值
	 * @param string $platform 目标用户终端手机的平台类型，如： android, ios 多个请使用逗号分隔
	 */
	function send($sendno = 0,$receiver_type = 1, $receiver_value = '', $msg_type = 1, $msg_content = '', $platform = 'android,ios') {
		$url = 'http://api.jpush.cn:8800/sendmsg/v2/sendmsg';
		$param = '';		
		$param .= '&sendno='.$sendno;			
		$appkeys = $this->_appkeys;				
		$param .= '&app_key='.$appkeys;		
		$param .= '&receiver_type='.$receiver_type;				
		$param .= '&receiver_value='.$receiver_value;		
		$masterSecret = $this->_masterSecret;		
		$verification_code = md5($sendno.$receiver_type.$receiver_value.$masterSecret);				
		$param .= '&verification_code='.$verification_code;			
		$param .= '&msg_type='.$msg_type;			
		$param .= '&msg_content='.$msg_content;		
		$param .= '&platform='.$platform;				
		$res = $this->request_post($url, $param);		
		if ($res === false) {
			return false;
		}	
		$res_arr = json_decode($res, true);	
          return $res_arr;
	}
	
}

?>