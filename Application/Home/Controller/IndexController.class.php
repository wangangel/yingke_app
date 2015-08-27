<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->assign('status',1);
        //从session中获取值.如果session中存在用户信息则取出!
        $user_info['username'] = $_SESSION['username'];
        //$user_info['attribute'] = $_SESSION['attribute'];
        $user_info['isdelete']; //
        $user_info['isdelete'] = 0; //
        $user_model=M('user');
        $usr_info = $user_model->where($user_info)->find();
        session('usr',$user_info);

        //获取幻灯片
        $bannerModel = new \Think\Model();
        $banner_sql="SELECT * FROM ajy_banner where 1=1 order by id desc limit 4 ";
        $banner_info = $bannerModel->query($banner_sql);
      

        //获取导航栏目
        $channel_sql="select * from ajy_channel where 1=1 order by id desc limit 5";
        $channel_info =  $bannerModel->query($channel_sql);
        // var_dump($channel_info);
        // die;
        
         //获取安居活动信息
        $content_model = M("content");
        $content_sql = "select * from ajy_content where channelid=6 order by id desc limit 6";
        $content_info = $content_model->query($content_sql);
        
        //获取商家咨讯信息
        $shop_sql = "select * from ajy_content where channelid=3 order by id desc limit 6";
        $shop_info = $content_model->query($shop_sql);

        //获取优惠券信息
        $coupon_model = M("coupon");
        $coupon_sql = "select * from ajy_coupon where status=0 order by id desc limit 6";
        $coupon_info = $coupon_model->query($coupon_sql);
        
        //获取热门服务信息
        $server_model = M("servergoods");
        $server_sql = "select * from ajy_servergoods where attr=0 order by serverid desc limit 8";
        $server_info = $server_model->query($server_sql);
        
        //获取基础建材的channel
        $category_model = M("servercategory");
        $category_sql = "select * from ajy_servercategory where attr = 1";
        $category_info = $category_model->query($category_sql);

        //获取配装服务的channel
        $servercategory_sql = "select * from ajy_servercategory where attr = 0";
        $servercategory_info = $category_model->query($servercategory_sql);
        //获取首页商品展示信息
        $goods_model = M("servergoods");
        $goods_sql = "select * from ajy_servergoods where attr=1 order by serverid desc limit 8";
        $goods_info = $goods_model->query($goods_sql);
        //查系统配置信息
        $model_sys = M("sysconfig");
        $sys_ = $model_sys->where('id=1')->find();
        $_SESSION['tel'] = $sys_['tel'];
        $_SESSION['email'] = $sys_['email'];
        $_SESSION['record'] = $sys_['record'];
        $_SESSION['statename'] = $sys_['statename'];
        $_SESSION['address'] = $sys_['address'];
        //查询底部内容信息
        $model_content = M("content");
        $arr1['channelid'] = 19;
        $arr2['channelid'] = 20;
        $arr3['channelid'] = 21;
        $arr4['channelid'] = 22;
        $new_hand = $model_content->where($arr1)->select();
        $pay_type = $model_content->where($arr2)->select();
        $peisong = $model_content->where($arr3)->select();
        $business = $model_content->where($arr4)->select();
        $_SESSION['new_hand'] = $new_hand;
        $_SESSION['pay_type'] = $pay_type;
        $_SESSION['peisong'] = $peisong;
        $_SESSION['business'] = $business;
        $this->assign('channel_info',$channel_info);
        $this->assign('banner_info',$banner_info);
        $this->assign('content_info',$content_info);
        $this->assign('shop_info',$shop_info);
        $this->assign('coupon_info',$coupon_info);
        $this->assign('server_info',$server_info);
        $this->assign('category_info',$category_info);
        $this->assign('servercategory_info',$servercategory_info);
        $this->assign('goods_info',$goods_info);
        $this->assign('usr',$usr_info);
        $this->display();
    }

    //获取对应的商品
    public function getServer(){
        $result = array();
        $categoryid = $_POST['categoryid'];

        $result = M('servergoods')->where(array('serverid'=> $categoryid))->order('sale_count desc')->limit(8)->select();
        
        $this->ajaxReturn($result,"JSON");

    }
    //获取底部内容
    public function getcontent(){
        $content_id = $_GET['id'];
        $model_content = M('content');
        $data['id'] = $content_id;
        $content = $model_content->where($data)->find();
        $this->assign('content',$content);
        $this->display();
    }


    //查阅优惠信息是否失效
    public function setcoupon(){

        $zero1= date("y-m-d"); //当前时间
        $coupon_list = M("coupon")->select();
        $count = count($coupon_list);
        $arr["status"] = 1;
        for ($i=0; $i <$count ; $i++) { 
           $time = $coupon_list[$i]["last_time"];
           if(strtotime($time) <= strtotime($zero1)){
            $arr["id"] = $coupon_list[$i]["id"];
            M("coupon")->save($arr);
            
           }
        }
       
        $result = 0;
        $this->ajaxReturn($result,"JSON");
        
    }

}