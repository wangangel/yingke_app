<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;
use Org\ThinkSDK\ThinkOauth;

class AlipayController extends MobileController{
    public function _initialize() {
        vendor('Alipay.alipay_notify');
    }

    function notify_url(){
      //计算得出通知验证结果
      $alipay_config = C('alipay_config');
      $alipayNotify = new \AlipayNotify($alipay_config);
      $verify_result = $alipayNotify->verifyNotify();
      if($verify_result) {
        //验证成功
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
            //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
            //注意：
            //付款完成后，支付宝系统发送该交易状态通知
            //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        //这里记录订单详情
        if(strpos($_POST('subject'),"G")){
            $name = $_POST('subject').split("G");
            $shop_data['shop_type'] = "gift";
            $shop_data['shop_name'] = $name[0];
            $shop_data['is_room'] = 0;
        }else if(strpos($_POST('subject'),"R")){
            $name = $_POST('subject').split("R");
            $shop_data['shop_type'] = "live";
            $shop_data['shop_name'] = $name[0];
            $shop_data['is_room'] = 1;
        }
        $shop_data['shop_num'] = $_POST('out_trade_no');
        $shop_data['pay_status'] = 1;
        $shop_data['pay_type'] = "支付宝支付";
        $shop_data['pay_date'] = time();
        $shop_data['shop_cash'] = $_POST['total_fee'];
        $pay_info = M('pay')->add($shop_data);
        echo "success";     //请不要修改或删除
      }else {
        //$data['shop_name'] =2;
        //验证失败
        echo "fail";
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
   //$info = M('pay')->add($data);
    

    }


}