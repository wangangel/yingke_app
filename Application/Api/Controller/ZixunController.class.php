<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Upload;
class ZixunController extends MobileController{
    public function __construct(){
        parent::__construct();
    }


    /*
     *获取商家资讯列表
     */
    public function zixun_list(){
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):8;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $content_model = M('content');
        $result = $content_model->where(array('channelid'=>3))->order('id desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有商家资讯信息');
        }else{
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['channelid'] = $v['channelid'];
                $data[$k]['channelname'] = $v['channelname'];
                $data[$k]['title'] = $v['title'];
                $data[$k]['picurl'] =  $v['picurl'];
                $data[$k]['author'] =  $v['author'];
                $data[$k]['releasedate'] =  $v['releasedate'];
                 $str = str_replace('src="','src="www.edeco.cc',$v['content']);
                $str = str_replace('"',"'",$str);
                //$str = str_replace('"',"'",$v['content']);
                $data[$k]['content'] =  htmlspecialchars($str);
                //$data[$k]['content'] =  htmlspecialchars($v['content']);
              
            }
           
            output_data($data);
        }
    }
   





    /*
     *根据id获取资讯详情
     */
    public function zixun_detail(){
        $arrOpt = array();
        if($_REQUEST['zixun_id'] == NULL){
            output_error('参数缺失');
        }
        $arrOpt['id'] = $_REQUEST['zixun_id'];
        $content_model = M('content');
        $result = $content_model->where($arrOpt)->find();
        $data = array();
        if($result == NULL){
            output_error('没有该商家资讯信息');
        }else{
            $data['id'] = $result['id'];
            $data['channelid'] = $result['channelid'];
            $data['channelname'] = $result['channelname'];
            $data['title'] = $result['title'];
            $data['picurl'] =  $result['picurl'];
            $data['author'] =  $result['author'];
            $data['releasedate'] =  $result['releasedate'];
            //$data['content'] =  htmlspecialchars($result['content']);
            $str = str_replace('src="','src="www.edeco.cc',$result['content']);
            $str = str_replace('"',"'",$str);
            $data['content'] =  htmlspecialchars($str);
              
        }
           
        output_data($data);
        
    }
   



    /*
     *获取优惠券列表
     */
    public function coupon_list(){
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):8;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;

        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $coupon_model = M('coupon');
        $result = $coupon_model->where(array('status'=>0))->order('id desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有优惠券信息');
        }else{
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                //根据优惠券id和用户id查看用户是否领取过该优惠券
                $arr = array();
                $arr['userid'] = $_REQUEST['userid'];
                $arr['discountid'] = $v['id'];
                $mydiscount_model = M('mydiscount');
                $result = $mydiscount_model->where($arr)->select();
                if($result[0] == NULL){
                    //说明该用户没有领取过该优惠券
                    $data[$k]['is_receive'] = 0;
                }else{
                    $data[$k]['is_receive'] = 1;
                }
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['picurl'] =  $v['picurl'];
                $data[$k]['price'] =  $v['price'];
                $data[$k]['detail'] =  htmlspecialchars($v['detail']);
                $data[$k]['createtime'] =  $v['createtime'];
                $data[$k]['attr'] =  $v['attr'];
                $data[$k]['status'] =  $v['status'];
                $data[$k]['morecash'] =  $v['morecash'];
            }
           
            output_data($data);
        }
    }


    /*
     *领取优惠券
     */
    public function receive_coupon(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        if($_REQUEST['coupon_id'] == NULL){
            output_error('参数缺失');
        }
         //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $arrOpt = array();
        $arrOpt['userid'] = $_REQUEST['userid'];
        $arrOpt['discountid'] = $_REQUEST['coupon_id'];
        $mydiscount_model = M('mydiscount');
        //先判断该用户是否已经领取过该优惠券
        $result = $mydiscount_model->where($arrOpt)->select();
        if($result[0] == NULL){
            //没有领取过
            $res = $mydiscount_model->add($arrOpt);
            if($res>0){
                //领取成功
                $data = array();
                $data['id'] = $res;
                output_data($data);
            }else{
                output_error('优惠券领取失败');
            }
        }else{
            output_error('已经领取过该优惠券');
        }
        
    }


    /*
     *我的优惠券
     */
    public function my_coupon(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
         //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $arrOpt = array();
        $arrOpt['userid'] = $_REQUEST['userid'];
        $coupon_model = M('coupon');
        $mydiscount_model = M('mydiscount');
        //根据用户id获取对应的优惠券
        $result = $mydiscount_model->where($arrOpt)->select();
        if($result[0] == NULL){
           output_error('没有优惠券信息');
        }else{
            foreach ($result as $k => $v) {
                $coupon_id = $v['discountid'];
                //根据优惠券信息id获取优惠券信息
                $res = $coupon_model->where(array('id'=>$coupon_id))->find();
                $data = array();
                $data['is_use'] = $v['status'];
                $data['use_time'] = $v['usetime'];
                $data['id'] = $res['id'];
                $data['userid'] = $res['userid'];
                $data['picurl'] = $res['picurl'];
                $data['price'] = $res['price'];
                $data['detail'] = htmlspecialchars($res['detail']);
                $data['createtime'] = $res['createtime'];
                $data['attr'] = $res['attr'];
                $data['status'] = $res['status'];
                $data['morecash'] = $res['morecash'];
            }
            output_data($data);
            
        }
        
    }

    
}
   
    
    
    