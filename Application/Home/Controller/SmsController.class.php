<?php
namespace Home\Controller;
use Think\Controller;
class SmsController extends Controller{
//短信接口
    function Post_1($data, $target) {
        $url_info = parse_url($target);
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
        $httpheader .= $data;

        $fd = fsockopen($url_info['host'], 80);
        fwrite($fd, $httpheader);
        $gets = "";
        while(!feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);
        return $gets;
}


/**
 * [smsinterface 短信验证接口]
 * @return [type] [description]
 */
public function smsinterface(){
        $phone = $_POST['phone'];
        //测试短信接口
        $target = "http://sms.chanzor.com:8001/sms.aspx";
        //替换成自己的测试账号,参数顺序和wenservice对应
        $num = $_SESSION['num'];
        if(null == $num || "" == $num){
           $num = rand(10,1000000); 
           $_SESSION['num'] = $num;
        }
        //判断手机号码是否注册过
        $type=$_POST['type'];
        if($type!='findpassword'){
            $verphone = M('user');
            $phone_info = $verphone ->where(array('phone'=>$phone))->find();
            //根据手机号码来判断是否执行
            if(null == $phone_info){
            $post_data = "action=send&userid=&account=ajywangluokeji&password=200005&mobile=".$phone."&sendTime=&content=".rawurlencode("您的验证码为".$num.",如非本人操作请忽略,验证码有效时间:10分钟.【安居易网络科技】");
            }
        }else{
            $post_data = "action=send&userid=&account=ajywangluokeji&password=200005&mobile=".$phone."&sendTime=&content=".rawurlencode("您的验证码为".$num.",如非本人操作请忽略,验证码有效时间:10分钟.【安居易网络科技】");
            }
       
        
        //$binarydata = pack("A", $post_data);
        $gets = $this->Post_1($post_data, $target);
        $start=strpos($gets,"<?xml");
        $data=substr($gets,$start);
        $xml=simplexml_load_string($data);
        // return  json_decode(json_encode($xml),TRUE);
        //$array = array('number'=>$phone);
        $lala = json_decode(json_encode($xml),TRUE);
        //判断短信返回值
        $status = $lala['returnstatus'];
        $this ->ajaxReturn($status,'json');
}




        
}