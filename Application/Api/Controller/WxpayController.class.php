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
        vendor('WxPayPubHelper.WxPayPubHelper');
    }


     public function wxpay(){
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
        $unifiedOrder->setParameter("body","微信安全支付");//商品描述
        //自定义订单号，此处仅作举例
        $timeStamp = time();
        $out_trade_no = $_REQUEST['ordernum'];
        $unifiedOrder->setParameter("out_trade_no","454654");//商户订单号 
        $unifiedOrder->setParameter("total_fee","100");//总金额
        $unifiedOrder->setParameter("notify_url", C('WxPayConf_pub.NOTIFY_URL'));//通知地址 
        $unifiedOrder->setParameter("trade_type","APP");//交易类型
        
        //获取统一支付接口结果
        $unifiedOrderResult = $unifiedOrder->getResult();
        
        //商户根据实际情况设置相应的处理流程
        if ($unifiedOrderResult["return_code"] == "FAIL") 
        {
            //商户自行增加处理流程
            echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
        }
        elseif($unifiedOrderResult["result_code"] == "FAIL")
        {
            //商户自行增加处理流程
            echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
            echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
        }
        output_data($unifiedOrderResult);
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
         $zt['orderstatus']='';
        $this->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
        if($notify->checkSign() == TRUE)
        {
            //定义订单状态
           
            if ($notify->data["return_code"] == "FAIL") {
                $zt['orderstatus']='0';
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                $zt['orderstatus']='0';
                log_result($log_name,"【业务出错】:\n".$xml."\n");
            }
            else{
                $zt['orderstatus']='1';
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【支付成功】:\n".$xml."\n");
            }
            $data['ordernum'] = $notify->data["result_code_no"];
            $infos = M('orderdetail') -> where($data) ->save($zt);
            $info = M('order') -> where($data) ->save($zt);
            if($info){
                $this->display("Myspace/myspace");
            }
        }
    }


    function  log_result($file,$word){
        $fp = fopen($file,"a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    public function todoPost()
        {
            //以log文件形式记录回调信息，用于调试
            $log_name = __ROOT__."/Public/log/native_call.log";
            //使用native通知接口
            $nativeCall = new \NativeCall_pub();
            
            //接收微信请求
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            log_result($log_name,"【接收到的native通知】:\n".$xml."\n");
            $nativeCall->saveData($xml);
            
            if($nativeCall->checkSign() == FALSE){
                $nativeCall->setReturnParameter("return_code","FAIL");//返回状态码
                $nativeCall->setReturnParameter("return_msg","签名失败");//返回信息
            }
            else
            {
                //提取product_id
                $product_id = $nativeCall->getProductId();
            
                //使用统一支付接口
                $unifiedOrder = new \UnifiedOrder_pub();
            
                //根据不同的$product_id设定对应的下单参数，此处只举例一种
                switch ($product_id)
                {
                    
                    case C('WxPayConf_pub.APPID')."static"://与native_call_qrcode.php中的静态链接二维码对应
             
                        $unifiedOrder->setParameter("body","贡献一分钱");//商品描述

                        $timeStamp = time();
                        $out_trade_no = C('WxPayConf_pub.APPID').$timeStamp;
                        $unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号             $unifiedOrder->setParameter("product_id","$product_id");//商品ID
                        $unifiedOrder->setParameter("total_fee","1");//总金额
                        $unifiedOrder->setParameter("notify_url",C('WxPayConf_pub.NOTIFY_URL'));//通知地址
                        $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
                        $unifiedOrder->setParameter("product_id",$product_id);//用户标识
                
                        $prepay_id = $unifiedOrder->getPrepayId();
                       
                        $nativeCall->setReturnParameter("return_code","SUCCESS");//返回状态码
                        $nativeCall->setReturnParameter("result_code","SUCCESS");//业务结果
                        $nativeCall->setReturnParameter("prepay_id",$prepay_id);//预支付ID
            
                        break;
                    default:
                        
                        $nativeCall->setReturnParameter("return_code","SUCCESS");//返回状态码
                        $nativeCall->setReturnParameter("result_code","FAIL");//业务结果
                        $nativeCall->setReturnParameter("err_code_des","此商品无效");//业务结果
                        break;
                }
            
            }
            
            //将结果返回微信
            $returnXml = $nativeCall->returnXml();
            log_result($log_name,"【返回微信的native响应】:\n".$returnXml."\n");
            echo $returnXml;
        }
}