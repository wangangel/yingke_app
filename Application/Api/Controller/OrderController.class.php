<?php
namespace Api\Controller;
use Api\Common\MobileController;
class OrderController extends MobileController{
    /*
     * 创建订单大
     */
    public function create_order(){
        if($_POST['key'] == null || $_POST['member_id'] == null || $_POST['client'] == null){
            output_error('请先登录！');
        }
        if($_POST['price_id'] == null){
            output_error('参数错误');
        }
        
        $model_member = M('member');
        $member_info = $model_member->where(array('member_id'=>$_POST['member_id']))->find();

        $model_businessman_venues_price = M('businessman_venues_price');
        
        $businessman_venues_price_info = $model_businessman_venues_price->where(array('price_id'=>$_POST['price_id']))->find();
        if($businessman_venues_price_info == null){
            output_error('没有该商品！');
        }
        if($businessman_venues_price_info['state'] == 1){
            output_error('该商品暂时不可售！！');
        }
        if($businessman_venues_price_info['ven_data'] < strtotime(date('Y-m-d',NOW_TIME))){
            output_error('请检查您选择的时间哦！');
        }
        if($businessman_venues_price_info['ven_data'] == strtotime(date('Y-m-d',NOW_TIME) && date('H',NOW_TIME) > $businessman_venues_price_info['ven_time'])){
            output_error('请检查您选择的时间哦！');
        }
        $model_businessman_venues = M('businessman_venues');
        $businessman_venues_info = $model_businessman_venues->where(array('ven_id'=>$businessman_venues_price_info['ven_id']))->find();
        
        
        $model_orderlist = M('orderlist');
        $array = array();
        $array['member_id'] = $_POST['member_id'];
        $array['member_name'] = $member_info['member_name'];
        $array['ordid'] = 'Y' . NOW_TIME . rand(100000, 999999);
        $array['ordtime'] = NOW_TIME;
        $array['businessman_id'] = $businessman_venues_price_info['businessman_id'];
        $array['productid'] = $businessman_venues_price_info['price_id'];
        $array['ordtitle'] = '由' . $businessman_venues_info['ven_name'] . '提供的场馆服务';
        $array['ordbuynum'] = 1;
        $array['ordprice'] = $businessman_venues_price_info['ven_price'];
        $array['ordfee'] = $businessman_venues_price_info['ven_price'] * $array['ordbuynum'];

        $result = $model_orderlist->add($array);
        if($result){
            $datas = array();
            $datas['or_id'] = $result;
            output_data($datas);
        }else{
            output_error('下单失败');
        }
    }
    /*
     * 给客户端返回给支付宝提交的数据
     */
    public function return_order_data(){
        if($_POST['key'] == null || $_POST['member_id'] == null || $_POST['client'] == null){
            output_error('请先登录！');
        }
        if($_POST['or_id'] == null){
            output_error('参数错误');
        }
        $model_orderlist = M('orderlist');
        $orderlist_info = $model_orderlist->where(array('or_id'=>$_POST['or_id']))->find();
        if($orderlist_info == null){
            output_error('没有该订单！');
        }
        
        if($orderlist_info['ordstatus'] != 0){
            output_error('该订单已被处理');
        }
        $array = array();
        $array['out_trade_no'] = $orderlist_info['ordid'];         //商户订单号 通过支付页面的表单进行传递，注意要唯一！
        $array['subject'] = $orderlist_info['ordtitle'];            //订单名称 //必填 通过支付页面的表单进行传递
        $array['total_fee'] = $orderlist_info['ordfee'];        //付款金额  //必填 通过支付页面的表单进行传递
        $array['body'] = '';                  //订单描述 通过支付页面的表单进行传递
        $array['show_url'] = '';          //商品展示地址 通过支付页面的表单进行传递
        $array['anti_phishing_key'] = "";
        
        output_data($array);
        
    }
}