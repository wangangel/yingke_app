<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Upload;
use Org\Util\Date;
class GoodsController extends MobileController{
    public function __construct(){
        parent::__construct();
    }


    /*
     *获取商品详情
     */
    public function goods_detail(){
        if($_REQUEST['goodsid'] == NULL || $_REQUEST['attr'] == NULL){
            output_error('参数不全');
        }
        $arrOpt = array();
        $arrOpt['id'] = intval($_REQUEST['goodsid']);
        $arrOpt['attr'] = intval($_REQUEST['attr']);
        $goods_model = M('servergoods');
        $result = $goods_model->where($arrOpt)->find();
        $data = array();
        if($result == NULL){
            output_error('没有该商品详细信息');
        }else{
            
                $data['id'] = $result['id'];
                $data['serverid'] = $result['serverid'];
                $data['servernum'] = $result['servernum'];
                $data['goodsname'] = $result['goodsname'];
                $data['faceurl'] = $result['faceurl'];
                $data['ceurl'] = $result['ceurl'];
                $data['goodsprice1'] = $result['goodsprice1'];
                $data['goodsprice2'] = $result['goodsprice2'];
                $data['goodsprice3'] = $result['goodsprice3'];
                $data['goodsprice4'] = $result['goodsprice4'];
                $data['goodsprice5'] = $result['goodsprice5'];
                $data['kg'] = $result['kg'];
                $data['sale_count'] = $result['sale_count'];
                $data['comment_num'] =  $result['comment_num'];
           
            output_data($data);
        }
    }
   





     /*
     *获取服务详情
     */
    public function server_detail(){
        if($_REQUEST['serverid'] == NULL || $_REQUEST['attr'] == NULL){
            output_error('参数不全');
        }
        $arrOpt = array();
        $arrOpt['id'] = intval($_REQUEST['serverid']);
        $arrOpt['attr'] = intval($_REQUEST['attr']);
        $goods_model = M('servergoods');
        $result = $goods_model->where($arrOpt)->find();
        $data = array();
        if($result == NULL){
            output_error('没有该服务详细信息');
        }else{
            
                $data['id'] = $result['id'];
                $data['serverid'] = $result['serverid'];
                $data['servernum'] = $result['servernum'];
                $data['goodsname'] = $result['goodsname'];
                $data['faceurl'] = $result['faceurl'];
                $data['ceurl'] = $result['ceurl'];
                $data['goodsprice1'] = $result['goodsprice1'];
                $data['goodsprice2'] = $result['goodsprice2'];
                $data['goodsprice3'] = $result['goodsprice3'];
                $data['goodsprice4'] = $result['goodsprice4'];
                $data['goodsprice5'] = $result['goodsprice5'];
                $data['kg'] = $result['kg'];
                $data['sale_count'] = $result['sale_count'];
                $data['comment_num'] =  $result['comment_num'];
           
            output_data($data);
        }
    }
   





     /*
     *我的购物车
     */
    public function my_shoppingcart(){
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
        $arrOpt['userid'] = intval($_REQUEST['userid']);
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $cart_model = M('cart');
        $result = $cart_model->where(array('userid'=>$arrOpt['userid']))->order('id desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有购物车信息');
        }else{
            
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['goodsid'] = $v['goodsid'];
                $data[$k]['servernum'] = $v['servernum'];
                $data[$k]['areaid'] =  $v['areaid'];
                $data[$k]['goodsname'] =  $v['goodsname'];
                $data[$k]['goodspicurll'] =  $v['goodspicurll'];
                $data[$k]['goodsprice'] = $v['goodsprice'];
                $data[$k]['goodscount'] = $v['goodscount'];
                $data[$k]['acount'] = $v['acount'];
                $data[$k]['goodsprice2'] = $v['goodsprice2'];
                $data[$k]['goodsprice3'] = $v['goodsprice3'];
                $data[$k]['goodsprice4'] = $v['goodsprice4'];
                $data[$k]['goodsprice5'] = $v['goodsprice5'];
                $data[$k]['colorname'] = $v['colorname'];
                $data[$k]['specname'] = $v['specname'];
                $data[$k]['cart_category'] = $v['cart_category'];
                $data[$k]['attr'] = $v['attr'];
                $data[$k]['flag'] = $v['flag'];

            }
            output_data($data);
        }
    }
   





     /*
     *提交订单
     */
    public function add_order(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        if($_REQUEST['addid'] == NULL || $_REQUEST['totalprice'] == NULL || $_REQUEST['orderstatus'] == NULL || $_REQUEST['attribute'] == NULL || $_REQUEST['goodsid'] == NULL || $_REQUEST['goodscount'] == NULL){
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
        $arrOpt['userid'] = intval($_REQUEST['userid']);
        $arrOpt['addid'] = intval($_REQUEST['addid']);
        $arrOpt['totalprice'] = $_REQUEST['totalprice'];
        $arrOpt['orderstatus'] = $_REQUEST['orderstatus'];
        $arrOpt['attribute'] = $_REQUEST['attribute'];
        if($_REQUEST['istoked'] == NULL){
            $arrOpt['istoked'] = 1;
        }else{
            $arrOpt['istoked'] = $_REQUEST['istoked'];
        }
        if($_REQUEST['isurgent'] == NULL){
            $arrOpt['isurgent'] = 1;
        }else{
            $arrOpt['isurgent'] = $_REQUEST['isurgent'];
        }
        if($_REQUEST['isstore'] == NULL){
            $arrOpt['isstore'] = 1;
        }else{
            $arrOpt['isstore'] = $_REQUEST['isstore'];
        }
        if($_REQUEST['isorder'] == NULL){
            $arrOpt['isorder'] = 0;
        }else{
            $arrOpt['isorder'] = $_REQUEST['isorder'];
        }

        $arrOpt['areaid'] = $_REQUEST['areaid'];
        $arrOpt['billtype'] = $_REQUEST['billtype'];
        $arrOpt['billhead'] = $_REQUEST['billhead'];
        $arrOpt['billcontent'] = $_REQUEST['billcontent'];
        $arrOpt['expresstype'] = $_REQUEST['expresstype'];
        $arrOpt['address'] = $_REQUEST['address'];
        $arrOpt['urgentshuom'] = htmlspecialchars($_REQUEST['urgentshuom']);
        $arrOpt['ordertime'] = $_REQUEST['ordertime'];
        $arrOpt['ordershuom'] = $_REQUEST['ordershuom'];
        $arrOpt['storestart'] = $_REQUEST['storestart'];
        $arrOpt['storeend'] = $_REQUEST['storeend'];
        $arrOpt['remark'] = htmlspecialchars($_REQUEST['remark']);
        $arrOpt['facepic'] = $_REQUEST['facepic'];
        $arrOpt['cepic'] = $_REQUEST['cepic'];
        $type = "";
        if($arrOpt['attribute'] == 0){
            //服务安装
            $type = "install";
        }else if($arrOpt['attribute'] == 1){
            //基础建材
            $type = "base";
        }else{
            //安装+配送
            $type = "distribution_install";
        }

        //根据规则得出订单编号
        /**
         * [$ordernum 生成订单号开始]
         * @var string
         */
        $ordernum = '';
        $ordertype = '';
        $ordercity = '01';
        import('ORG.Util.Date');// 导入日期类
        $date1 = date('Ymd',time()); 
        $date = substr($date1,2);
        //根据类型来判断订单类型
        if($type=='base'){
            $ordertype = 'D';
        }else if($type=='install'){
            $ordertype = 'A';
        }else if($type=='distribution_install'){
            $ordertype = 'C';
        }
        //根据当日时间来获取最大的订单号
        $cur_date['buytime'] = strtotime(date('Y-m-d'));
        $order_model = M('order');
        $order_max_today = $maxid = $order_model ->where($cur_date)->count();
        $maxnum = sprintf("%04d",intval($maxid)+1);
        //拼接订单号
        $order_num = $ordertype.$ordercity.$date.$maxnum;

        $arrOpt['ordernum'] = $order_num;
        $arrOpt['buytime'] = strtotime(date('Y-m-d'));

        $order_model = M('order');
        $result = $order_model->add($arrOpt);
        
        if($result>0){
            //将商品详细信息继续添加到订单详情表
            $arr = array();
            $arr['goodsid'] = $_REQUEST['goodsid'];
            //根据商品id获取商品详细信息
            $goods_model = M('servergoods');
            $res = $goods_model->where(array('id'=>$arr['goodsid']))->find();
            if($res){
                $arr['goodscount'] = $_REQUEST['goodscount'];
                $arr['picurl'] = $res['faceurl'];
                $arr['goodsname'] = $res['goodsname'];
                $arr['goodsaccount'] = $res['goodsprice1'] * $arr['goodscount'];

                $arr['userid'] = $_REQUEST['userid'];
                $arr['orderid'] = $order_num;
                $arr['addid'] = $_REQUEST['addid'];
                $arr['orderstatus'] = $_REQUEST['orderstatus'];
                $arr['isorder'] = $arrOpt['isorder'];
                $arr['ordertime'] = $_REQUEST['ordertime'];
                if($_REQUEST['flag'] == NULL){
                    $arr['flag'] = 0;
                }else{  
                    $arr['flag'] = $_REQUEST['flag'];
                }
                $arr['buyerwords'] = htmlspecialchars($_REQUEST['buyerwords']);
                $orderdetail_model = M('orderdetail');
                $jieguo = $orderdetail_model->add($arr);
                if($jieguo>0){
                    $data = array();
                    $data['order_id'] = $result;
                    $data['orderdetail_id'] = $jieguo;
                    output_data($data);
                }else{
                    output_error('添加订单详情失败');
                }
            }else{
                output_error('没有商品信息'); 
            }
            
        }else{ 
           output_error('添加订单失败');  
        }
    }
   



    /*
     *获取商品/服务列表
     */
    public function goods_list(){
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):8;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $goods_model = M('servergoods');
        $result = $goods_model->where()->order('id desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有商品/服务信息');
        }else{
            foreach ($result as $k => $v) {
                $data[$k]['serverid'] = $v['serverid'];
                $data[$k]['servernum'] = $v['servernum'];
                $data[$k]['goodsname'] = $v['goodsname'];
                $data[$k]['faceurl'] = $v['faceurl'];
                $data[$k]['ceurl'] = $v['ceurl'];
                $data[$k]['goodsprice1'] = $v['goodsprice1'];
                $data[$k]['goodsprice2'] = $v['goodsprice2'];
                $data[$k]['goodsprice3'] = $v['goodsprice3'];
                $data[$k]['goodsprice4'] = $v['goodsprice4'];
                $data[$k]['goodsprice5'] = $v['goodsprice5'];
                $data[$k]['kg'] = $v['kg'];
                $data[$k]['sale_count'] = $v['sale_count'];
                $data[$k]['comment_num'] =  $v['comment_num'];
              
            }
           
            output_data($data);
        }
    }
   




    /*
     *条件搜索
     */
    public function tiaojian_search(){
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):8;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $arr = array();
        $arr['attr'] = intval($_REQUEST['attr'])>0?intval($_REQUEST['attr']):0;
        $priceflag = $_REQUEST['price']; 
        $sid = $_REQUEST['channelid'];
        $keywords = $_REQUEST['keywords'];
        $order = $_REQUEST['order'];
        $goods_model = M('servergoods');
        $map = array();
        if($order == ""){
            if($priceflag == ""){
                //没有选中价格区间，查询对应栏目所有商品服务
                if($sid == ""){
                    //第一次进来，没有选中对应栏目
                     $_info = $goods_model->where("goodsname like '%$keywords%' ")->where($arr)->limit($start,$arrOpt['ps'])->order("id desc")->select();
    
                }
                else{
                    $map['serverid'] = $sid;
                    $map['attr'] = $arr['attr'];
                    $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }

           }elseif($priceflag == "flag1"){
                //价格区间在100~299的服务商品
                if($sid == ""){
                    $map["goodsprice1"] = array('between','100,299');
                    $map['attr'] = $arr['attr'];
                    $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }else{
                     $map["goodsprice1"] = array('between','100,299');
                     $arr['serverid'] = $sid;
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->where($arr)->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }
           
           }elseif($priceflag == "flag2"){
                 //价格区间在300~499的服务商品
                 if($sid == ""){
                     $map["goodsprice1"] = array('between','300,499');
                     $map['attr'] = $arr['attr'];
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }else{
                     $map["goodsprice1"] = array('between','300,499');
                     $arr['serverid'] = $sid;
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->where($arr)->limit($start,$arrOpt['ps'])->order("id desc")->select();

                }
           }else{
                //查询价格区间为500以上的服务商品
                if($sid == ""){
                    $map["goodsprice1"] = array('egt','500');
                    $map['attr'] = $arr['attr'];
                    $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }else{
                   $map["goodsprice1"] = array('egt','500');
                   $arr['serverid'] = $sid;
                   $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->where($arr)->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }
           }
       }else{
            if($priceflag == ""){
            //没有选中价格区间，查询对应栏目所有商品服务
                if($sid == ""){
                    //第一次进来，没有选中对应栏目
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($arr)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }
                else{
                    $map['serverid'] = $sid;
                    $map['attr'] = $arr['attr'];
                    $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }

           }elseif($priceflag == "flag1"){
                //价格区间在100~299的服务商品
                if($sid == ""){
                    $map["goodsprice1"] = array('between','100,299');
                    $map['attr'] = $arr['attr'];
                    $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }else{
                     $map["goodsprice1"] = array('between','100,299');
                     $arr['serverid'] = $sid;
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->where($arr)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }
           
           }elseif($priceflag == "flag2"){
                 //价格区间在300~499的服务商品
                 if($sid == ""){
                     $map["goodsprice1"] = array('between','300,499');
                     $map['attr'] = $arr['attr'];
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }else{
                     $map["goodsprice1"] = array('between','300,499');
                     $arr['serverid'] = $sid;
                     $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->where($arr)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();

                }
           }else{
                //查询价格区间为500以上的服务商品
                if($sid == ""){
                    $map["goodsprice1"] = array('egt','500');
                    $map['attr'] = $arr['attr'];
                    $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }else{
                   $map["goodsprice1"] = array('egt','500');
                   $arr['serverid'] = $sid;
                   $_info = $goods_model->where("goodsname like '%$keywords%'")->where($map)->where($arr)->order("'%$order% desc'")->limit($start,$arrOpt['ps'])->order("id desc")->select();
                }
           }
       }
        $data = array();
        if($_info[0] == NULL){
            output_error('没有商品/服务信息');
        }else{
            foreach ($_info as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['serverid'] = $v['serverid'];
                $data[$k]['servernum'] = $v['servernum'];
                $data[$k]['goodsname'] = $v['goodsname'];
                $data[$k]['faceurl'] = $v['faceurl'];
                $data[$k]['ceurl'] = $v['ceurl'];
                $data[$k]['goodsprice1'] = $v['goodsprice1'];
                $data[$k]['goodsprice2'] = $v['goodsprice2'];
                $data[$k]['goodsprice3'] = $v['goodsprice3'];
                $data[$k]['goodsprice4'] = $v['goodsprice4'];
                $data[$k]['goodsprice5'] = $v['goodsprice5'];
                $data[$k]['kg'] = $v['kg'];
                $data[$k]['sale_count'] = $v['sale_count'];
                $data[$k]['comment_num'] =  $v['comment_num'];
              
            }
           
            output_data($data);
        }
    }
   



    
}
   
    
    
    