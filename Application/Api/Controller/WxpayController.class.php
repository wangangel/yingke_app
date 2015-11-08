<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;
class WxpayController extends MobileController{

    /**
     * 初始化
     */
    public function _initialize(){
        //引入WxPayPubHelper
        vendor('WxPayPubHelper.WxPayHelper');
       // vendor('WxPayPubHelper.WxPayConfig');
        //vendor('WxPayPubHelper.WxPayJsApiPay');
        //vendor('WxPayPubHelper.WxPayData');
    }

     public function start_pay(){
         if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
         //验证key是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }  
        //=========步骤2：使用统一支付接口，获取prepay_id============
        
        if($_REQUEST['shop_desc'] == NULL || $_REQUEST['shop_cash'] == NULL){
            output_error('参数不全');
        }
        $shop_desc = $_REQUEST['shop_desc'];
        //隨機生成訂單號
        $shop_num = date('YmdHis').rand(0,9999);
        //微信支付是以分為單位,這裡需要乘以100
        $shop_cash = $_REQUEST['shop_cash']*100;

        $WxPayHelper = new \WxPayHelper();
        $response = $WxPayHelper->getPrePayOrder($shop_desc, $shop_num, $shop_cash);
        //dump($response);
        $x = $WxPayHelper->getOrder($response['prepay_id']);
        //訂單記錄保存到表中
        $pay_model = M('pay');
        $pay_data['shop_name'] = $shop_desc;
        $pay_data['shop_num'] = $shop_num;
        $pay_data['shop_cash'] = $_REQUEST['shop_cash'];
        $pay_data['pay_type'] = '微信支付';
        $pay_data['pay_date'] = time();
        $pay_data['pay_userid'] = $_REQUEST['userid'];
        if(empty($_REQUEST['liveroom_id'])){
            $pay_data['is_room'] = 0;
        }else{
            $pay_data['pay_userid'] = $_REQUEST['liveroom_id'];
            $pay_data['is_room'] = 1;
        }
        $pay_info = $pay_model ->add($pay_data);
        output_data($x);
    }


   public function notify(){
        //使用通用通知接口
        $notify = new \Notify_pub();
         
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
         
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
         
        //以log文件形式记录回调信息
        //         $log_ = new Log_();
        $log_name= __ROOT__."/Public/log/notify_url.log";//log文件路径
       //  $zt['orderstatus']='';
        $this->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
        if($notify->checkSign() == TRUE)
        {
            //定义订单状态
           
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【业务出错】:\n".$xml."\n");
            }
            else{
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【支付成功】:\n".$xml."\n");
            }
            //增加处理流程
            output_data("支付成功!");
        }
    }

}