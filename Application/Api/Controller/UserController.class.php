<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;
use Org\ThinkSDK\ThinkOauth;
use Home\Event;


class UserController extends MobileController{
    public function __construct(){
        parent::__construct();
    }
   
    
    /**
     * 登陆生成token
     */
    private function _get_token($id, $phone, $client) {
        $user_token = M('usertoken');
        //重新登陆后以前的令牌失效,删除之前的token
        $condition = array();
        $condition['member_id'] = $id;
        //下面这句可以开启多设备登录
        $condition['client_type'] = $client;
        $user_token->where($condition)->delete();
    
        //生成新的token
        $mb_user_token_info = array();
        $token = md5($phone . strval(NOW_TIME) . strval(rand(0,999999)));
        $mb_user_token_info['userid'] = $id;
        $mb_user_token_info['phone'] = $phone;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = NOW_TIME;
        $mb_user_token_info['client_type'] = $client;
        $result = $user_token->add($mb_user_token_info);
        
        if($result) {
            return $token;
        } else {
            return null;
        }
    }


    /*
     * 用户名注册
     */
	public function register(){
        if($_REQUEST['username'] == NULL || $_REQUEST['password'] == NULL || $_REQUEST['attribute'] == NULL){
             output_error('参数不全');
        }
		$model_member	= M('user');
		$result = $model_member->where(array('phone'=>$_REQUEST['phonenumber']))->find();
		if($result != NULL){
		    output_error('已经存在该手机用户了！');
		}
        //验证手机验证码是否正确
        $jieguo = $this->checkphonecode($_REQUEST['phonenumber'],$_REQUEST['phonecode']);
        if($jieguo == -1){
            //手机验证码不正确
             output_error('手机验证码不正确');
        }
		//接收数据
        $register_info = array();
        $register_info['password'] = md5($_REQUEST['password']);
        $register_info['username'] = $_REQUEST['username'];
        $register_info['phone'] = $_REQUEST['phonenumber'];
        $register_info['attribute'] = $_REQUEST['attribute'];
        $register_info['isdelete'] = 0;
        $member_info = $model_member->add($register_info);
        if($member_info) {
            $token = $this->_get_token($member_info, $register_info['phone'], $_REQUEST['client']);
            if($token) {
                output_data(array(
                'userid' => $member_info,
                'phone'=>$register_info['phone'],
                'username' => $register_info['username'],
                'key' => $token
                ));
            } else {
                output_error('祝贺您成功注册安居易，请尝试登录');
            }
        } else {
			output_error("对不起，注册失败！");
        }
    }



    /*
     * 获取手机验证码
     */
    public function getphonecode()
    {
        //随机生成6位验证码
        $intCode = rand(1,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $intPhone = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
        $intOptype = intval($_REQUEST['optype']);
        $strSql = "select * from ajy_phonecode where phone=$intPhone and optype=$intOptype and status='0' and dateline>'".(time()-60)."'";
        $phonecode_model =  M('phonecode');
        $phonecode_info = $phonecode_model->query($strSql);
        $count = count($phonecode_info);
        if($count>0){
            //数据库中有未失效的验证码
            output_error("您的验证码已经发送，请不要重复发送");
        }else{
            $arr = array();
            $arr['phone'] = $intPhone;
            $arr['phonecode'] = $intCode;
            $arr['optype'] = $intOptype;
            $arr['dateline'] = time();
            $arr['status'] = 0;
            $result = $phonecode_model->add($arr);
            if($result>0){
                //向手机发送验证码
                $post_data = "action=send&userid=&account=ajywangluokeji&password=200005&mobile=".$intPhone."&sendTime=&content=".rawurlencode("您的验证码为".$intCode.",如非本人操作请忽略,验证码有效时间:1分钟.【安居易网络科技】");
                $target = "http://sms.chanzor.com:8001/sms.aspx";
                $arrResu = $this->Post_1($post_data,$target);
                output_data(array(
                'phone' => $intPhone,
                'phonecode'=>$intCode,
                'optype' => $intOptype
                
                ));
            }else{
                output_error("验证码获取失败");
            }
            
        }
       
    }




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

    

    /*
     *验证验证码
     */
    public function checkphonecode($phone,$code){
        // $intPhone = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
        // $intCode = intval($_REQUEST['code']);
        $intPhone =$phone;
        $intCode = $code;
        $strSql = "update ajy_phonecode set status='1' where phone='$intPhone' and phonecode='$intCode'";
        $phonecode_model =  M('phonecode');
        $result = $phonecode_model->execute($strSql);
        if($result == 0){
            //验证失败
           // output_error("验证码验证失败");
           return -1;
        }else{
            //验证成功
             // output_data(array(
             //    'id' => $result
                
                
             //    ));
             return 1;
        }
    }


    /*
     * 登录
     */
    public function login(){
        if($_REQUEST['username'] == null || $_REQUEST['password'] == null) {
            output_error('用户名或密码错误！');
        }
        $model_member = M('user');
        $arr = array();
        $arr['username'] = htmlspecialchars($_REQUEST['username'],ENT_QUOTES);
        $arr['password'] = htmlspecialchars($_REQUEST['password'],ENT_QUOTES);
        $arr['password']  = md5($arr['password']);
        $member_info = $model_member->where($arr)->select();
        if(!empty($member_info)) {

            $token = $this->_get_token($member_info[0]['id'], $member_info[0]['phone'], $_REQUEST['client']);
            if($token) {
                
                output_data(array(
                'id' => $member_info[0]['id'],
                'phone'=>$member_info[0]['phone'],
                'username' => $member_info[0]['username'],
                'headurl' => $member_info[0]['headurl'],
                'key' => $token
                ));
            } else {
                output_error('登陆失败');
            }
        } else {
            output_error('用户名密码错误');
        }
    }



    /*
     *第三方登录
     */
    public function third_login(){
        $type = $_REQUEST['type'];
        if($type == NUll || $type == ""){
            output_error("参数不全");
        }
         //加载ThinkOauth类并实例化一个对象
        // var_dump(import("Org/ThinkSDK/ThinkOauth"));
        // die;
        $sns  = ThinkOauth::getInstance($type);
        //跳转到授权页面
        redirect($sns->getRequestCodeURL());

    }

     //授权回调地址----不明白什么意思
    public function callback($type = null, $code = null){
        (empty($type) || empty($code)) && $this->error('参数错误');
          
        //加载ThinkOauth类并实例化一个对象
        $sns  = ThinkOauth::getInstance($type);

        //腾讯微博需传递的额外参数
        $extend = null;
        if($type == 'tencent'){
            $extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
        }

        //请妥善保管这里获取到的Token信息，方便以后API调用
        //调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
        //如： $qq = ThinkOauth::getInstance('qq', $token);
        $token = $sns->getAccessToken($code , $extend);
        //获取当前登录用户信息
        $oppenid = $token['openid'];
        //更具openid来判断是否绑定用户,若绑定即用绑定,否则直接绑定页面
        $user_model = M('user');
        $str2[$type.'openid'] = $oppenid;
        $userinfos = $user_model ->where($str2)->select();

        if(empty($userinfos)){
            $this->error('未绑定或未注册第三方,请绑定或者注册后再登录!',U('Home/ThirdLogin/show_bind?type='.$type.'&openid='.$oppenid));
        }else{
            $this->success('首页登录中...',U('Home/ThirdLogin/login?type='.$type.'&openid='.$oppenid));
        }
   
    }



    /*
     * 退出
     */
    public function logout(){
        $array = array();
        $array['token'] = $_REQUEST['key'];
        $array['userid'] = $_REQUEST['userid'];
        $array['client_type'] = $_REQUEST['client'];

        $model_user_token = M('usertoken');
        $result = $model_user_token->where($array)->delete();
        //$result = $model_mb_user_token->delMbUserToken($array);
        if($result){
            $datas = array();
            $datas['msg'] = '退出成功！';
            output_data($datas);
        }else{
            output_error('退出失败，请重试！');
        }
    }


    /*
     * 忘记密码
     */
    public function forget_password(){
        if($_REQUEST['phonenum'] == null){
            output_error('请输入手机号码！');
        }
        if($_REQUEST['password'] == null){
            output_error('请输入密码！');
        }
        if($_REQUEST['code'] == null){
            output_error('请输入验证码！');
        }
        $model_user = M('user');
        $member_info = $model_user->where(array('phone'=>$_REQUEST['phonenum']))->find();
        if($member_info == null){
            output_error('不存在该用户！');
        }
        //如果原密码和现在的修改密码相同则直接提示密码修改成功
        if($member_info['password'] == md5($_REQUEST['password'])){
            $datas = array();
            $datas['msg'] = '修改成功，请登陆！';
            output_data($datas);
        }
        $model_code = M('phonecode');
        $array = array();
        $array['phone'] = $_REQUEST['phonenum'];
        $array['phonecode'] = $_REQUEST['code'];
        $codes = $model_code->where($array)->find();
        
        //删除所有超过240秒的验证码
        $array = array();
        $array['dateline'] = array('LT',NOW_TIME-240);
        $model_code->where($array)->delete();
        if($codes == false){
            output_error('验证码错误！');
        }
        
        $array = array();
        $array['password'] = md5($_REQUEST['password']);
        
        //将密码更新
        $result = $model_user->where(array('phone'=>$_REQUEST['phonenum']))->save($array);

        if($result){
            $datas = array();
            $datas['msg'] = '修改成功，请登陆！';
            output_data($datas);
        }else{
            output_error('修改失败，请稍后重试！');
        }
        
        
    }




     /*
     * 获取个人信息
     */
    public function my_info(){
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

        $array = array();
        $array['userid'] = $_REQUEST['userid'];

        $model_user = M('user');
        $result = $model_user->where($array)->find();
        //$result = $model_mb_user_token->delMbUserToken($array);
        if($result != NULL){
            $datas = array();
            $datas['userid'] = $result['id'];
            $datas['username'] = $result['username'];
            $datas['phone'] = $result['phone'];
            $datas['telphone'] = $result['telphone'];
            $datas['linkman'] = $result['linkman'];
            $datas['department'] = $result['department'];
            $datas['email'] = $result['email'];
            $datas['coname'] = $result['coname'];
            $datas['pcount'] = $result['pcount'];
            $datas['logincount'] = $result['logincount'];
            $datas['logintime'] = $result['logintime'];
            $datas['status'] = $result['status'];
            $datas['attribute'] = $result['attribute'];
            $datas['headurl'] = $result['headurl'];
            $datas['accounturl'] = $result['accounturl'];
            $datas['bussinessurl'] = $result['bussinessurl'];
            $datas['taxurl'] = $result['taxurl'];
            $datas['empowerurl'] = $result['empowerurl'];
            $datas['isdelete'] = $result['isdelete'];

            output_data($datas);
        }else{
            output_error('没有该用户信息');
        }
    }




     /*
     * 获取用户收货地址信息
     */
    public function my_address(){
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

        $array = array();
        $array['userid'] = $_REQUEST['userid'];

        $model_address = M('address');
        $result = $model_address->where($array)->select();
        //$result = $model_mb_user_token->delMbUserToken($array);
        $datas = array();
        if($result[0] != NULL){
            foreach ($result as $k => $v) {
               $datas[$k]['id'] = $v['id'];
               $datas[$k]['userid'] = $v['userid'];
               $datas[$k]['userpro'] = $v['userpro'];
               $datas[$k]['usercity'] = $v['usercity'];
               $datas[$k]['usertown'] = $v['usertown'];
               $datas[$k]['userstr'] = $v['userstr'];
               $datas[$k]['consigner'] = $v['consigner'];
               $datas[$k]['attribute'] = $v['attribute'];
               $datas[$k]['isdefault'] = $v['isdefault'];
               $datas[$k]['phone'] = $v['phone'];
               $datas[$k]['youbian'] = $v['youbian'];
               
            }
           

            output_data($datas);
        }else{
            output_error('没有该用户的收货地址信息');
        }
    }






    /*
     * 修改用户收货地址信息
     */
    public function edit_myaddress(){
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
        if($_REQUEST['pro'] == NULL ||$_REQUEST['city'] == NULL || $_REQUEST['town'] == NULL || $_REQUEST['str'] == NULL || $_REQUEST['consigner'] == NULL || $_REQUEST['phone'] == NULL || $_REQUEST['id'] == NULL ){
            output_error('参数缺失');
        }
        $array = array();
        $tiaojian = array();
        $array['userid'] = $_REQUEST['userid'];
        $tiaojian['id'] = $_REQUEST['id'];
        $array['userpro'] = $_REQUEST['pro'];
        $array['usercity'] = $_REQUEST['city'];
        $array['usertown'] = $_REQUEST['town'];
        $array['userstr'] = $_REQUEST['str'];
        $array['consigner'] = $_REQUEST['consigner'];
        $array['phone'] = $_REQUEST['phone'];
        $array['youbian'] = $_REQUEST['youbian'];
        $array['attribute'] = intval($_REQUEST['attribute'])>0?intval($_REQUEST['attribute']):0;
        $array['isdefault'] = intval($_REQUEST['isdefault'])>0?intval($_REQUEST['isdefault']):0;
        $model_address = M('address');
        $result = $model_address->where($tiaojian)->save($array);
        if($result>0){
            $data = array();
            $data['id'] = $tiaojian['id'];
            output_data($data);
        }else{
            output_error('没有该用户的收货地址信息');
        }
    }



    /*
     * 增加用户收货地址信息
     */
    public function add_myaddress(){
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
        if($_REQUEST['pro'] == NULL ||$_REQUEST['city'] == NULL || $_REQUEST['town'] == NULL || $_REQUEST['str'] == NULL || $_REQUEST['consigner'] == NULL || $_REQUEST['phone'] == NULL ){
            output_error('参数缺失');
        }
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['userpro'] = $_REQUEST['pro'];
        $array['usercity'] = $_REQUEST['city'];
        $array['usertown'] = $_REQUEST['town'];
        $array['userstr'] = $_REQUEST['str'];
        $array['consigner'] = $_REQUEST['consigner'];
        $array['phone'] = $_REQUEST['phone'];
        $array['youbian'] = $_REQUEST['youbian'];
        $array['attribute'] = intval($_REQUEST['attribute'])>0?intval($_REQUEST['attribute']):0;
        $array['isdefault'] = intval($_REQUEST['isdefault'])>0?intval($_REQUEST['isdefault']):0;
        $model_address = M('address');
        $result = $model_address->add($array);
        if($result>0){
            $data = array();
            $data['id'] = $result;
            output_data($data);
        }else{
            output_error('添加收货地址失败');
        }
    }





     /*
     * 删除用户收货地址信息
     */
    public function del_myaddress(){
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
        if($_REQUEST['add_id'] == NULL ){
            output_error('参数缺失');
        }
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['id'] = $_REQUEST['add_id'];
        
        $model_address = M('address');
        $result = $model_address->where($array)->delete();
        if($result>0){
            $data = array();
            $data['id'] = $result;
            output_data($data);
        }else{
            output_error('添加收货地址失败');
        }
    }






    /*
     * 修改昵称
     */
    public function edit_nickname(){
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
        if($_REQUEST['username'] == NULL){
            output_error('参数缺失');
        }
        $arr = array();
        $tiaojian = array();
        $tiaojian['id'] = $_REQUEST['userid'];
        $arr['username'] = $_REQUEST['username'];
        $model_user = M('user');
        $result = $model_user->where($tiaojian)->save($arr);

        if($result>0){
            $data = array();
            $data['id'] = $_REQUEST['userid'];
            output_data($data);
        }else{
            output_error('修改昵称失败');
        }
    }





     /*
     * 我的订单列表
     */
    public function my_orderlist(){
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $order_model = M('order');
        $result = $order_model->where(array('userid'=>$_REQUEST['userid']))->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             output_error('没有订单信息');
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['areaid'] = $v['areaid'];
                $data[$k]['addid'] = $v['addid'];
                $data[$k]['ordernum'] = $v['ordernum'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['buytime'] = $v['buytime'];
                $data[$k]['totalprice'] = $v['totalprice'];
                $data[$k]['orderstatus'] = $v['orderstatus'];
                $data[$k]['iscancel'] = $v['iscancel'];
                $data[$k]['billtype'] = $v['billtype'];
                $data[$k]['billhead'] = $v['billhead'];
                $data[$k]['billcontent'] = $v['billcontent'];
                $data[$k]['expresstype'] = $v['expresstype'];
                $data[$k]['address'] = $v['address'];
                $data[$k]['istoked'] = $v['istoked'];
                $data[$k]['isurgent'] = $v['isurgent'];
                $data[$k]['urgentshuom'] = $v['urgentshuom'];
                $data[$k]['isorder'] = $v['isorder'];
                $data[$k]['ordertime'] = $v['ordertime'];
                $data[$k]['ordershuom'] = $v['ordershuom'];
                $data[$k]['isstore'] = $v['isstore'];
                $data[$k]['storestart'] = $v['storestart'];
                $data[$k]['storeend'] = $v['storeend'];
                $data[$k]['attribute'] = $v['attribute'];
                $data[$k]['facepic'] = $v['facepic'];
                $data[$k]['cepic'] = $v['cepic'];
                $data[$k]['remark'] = $v['remark'];
                //根据userid和订单编号获取订单详情
                $orderdetail_model = M('orderdetail');
                $array = array();
                $array['userid'] = $v['userid'];
                $array['orderid'] = $v['ordernum'];
                $jieguo = $orderdetail_model->where($array)->find();
                $data[$k]['order_detail']['goodsid'] = $jieguo['goodsid'];
                $data[$k]['order_detail']['picurl'] = $jieguo['picurl'];
                $data[$k]['order_detail']['goodsname'] = $jieguo['goodsname'];
                $data[$k]['order_detail']['goodsaccount'] = $jieguo['goodsaccount'];
                $data[$k]['order_detail']['goodscount'] = $jieguo['goodscount'];
                $data[$k]['order_detail']['buyerwords'] = $jieguo['buyerwords'];
            }
            output_data($data);
        }   
    }





     /*
     * 删除订单
     */
    public function del_order(){
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
        if($_REQUEST['ordernum'] == NULL){
            output_error('参数缺失');
        }
        $opt =array();
        $opt['userid'] = $_REQUEST['userid'];
        $opt['ordernum'] = $_REQUEST['ordernum'];
        $order_model = M('order');
        $result = $order_model->where($opt)->delete();
        if($result){
            //继续删除订单详情
             $orderdetail_model = M('orderdetail');
             $array = array();
             $array['userid'] = $opt['userid'];
             $array['orderid'] = $opt['ordernum'];
             $jieguo = $orderdetail_model->where($array)->delete();
             if($jieguo){
                output_data(array(0=>'1'));
            }else{
                output_error('订单详情删除失败');
            }
             
        }else{
           
             output_error('订单删除失败');
        }   

    }





    /*
     * 已取消的订单列表
     */
    public function my_cancellist(){
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['iscancel'] = 0;
        $order_model = M('order');
        $result = $order_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             output_error('没有已取消的订单信息');
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['areaid'] = $v['areaid'];
                $data[$k]['addid'] = $v['addid'];
                $data[$k]['ordernum'] = $v['ordernum'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['buytime'] = $v['buytime'];
                $data[$k]['totalprice'] = $v['totalprice'];
                $data[$k]['orderstatus'] = $v['orderstatus'];
                $data[$k]['iscancel'] = $v['iscancel'];
                $data[$k]['billtype'] = $v['billtype'];
                $data[$k]['billhead'] = $v['billhead'];
                $data[$k]['billcontent'] = $v['billcontent'];
                $data[$k]['expresstype'] = $v['expresstype'];
                $data[$k]['address'] = $v['address'];
                $data[$k]['istoked'] = $v['istoked'];
                $data[$k]['isurgent'] = $v['isurgent'];
                $data[$k]['urgentshuom'] = $v['urgentshuom'];
                $data[$k]['isorder'] = $v['isorder'];
                $data[$k]['ordertime'] = $v['ordertime'];
                $data[$k]['ordershuom'] = $v['ordershuom'];
                $data[$k]['isstore'] = $v['isstore'];
                $data[$k]['storestart'] = $v['storestart'];
                $data[$k]['storeend'] = $v['storeend'];
                $data[$k]['attribute'] = $v['attribute'];
                $data[$k]['facepic'] = $v['facepic'];
                $data[$k]['cepic'] = $v['cepic'];
                $data[$k]['remark'] = $v['remark'];
                //根据userid和订单编号获取订单详情
                $orderdetail_model = M('orderdetail');
                $array = array();
                $array['userid'] = $v['userid'];
                $array['orderid'] = $v['ordernum'];
                $jieguo = $orderdetail_model->where($array)->find();
                $data[$k]['order_detail']['goodsid'] = $jieguo['goodsid'];
                $data[$k]['order_detail']['picurl'] = $jieguo['picurl'];
                $data[$k]['order_detail']['goodsname'] = $jieguo['goodsname'];
                $data[$k]['order_detail']['goodsaccount'] = $jieguo['goodsaccount'];
                $data[$k]['order_detail']['goodscount'] = $jieguo['goodscount'];
                $data[$k]['order_detail']['buyerwords'] = $jieguo['buyerwords'];
            }
            output_data($data);
        }   
    }






     /*
     * 已完成的订单列表
     */
    public function my_completelist(){
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['orderstatus'] = 6;
        $order_model = M('order');
        $result = $order_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             output_error('没有已完成的订单信息');
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['areaid'] = $v['areaid'];
                $data[$k]['addid'] = $v['addid'];
                $data[$k]['ordernum'] = $v['ordernum'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['buytime'] = $v['buytime'];
                $data[$k]['totalprice'] = $v['totalprice'];
                $data[$k]['orderstatus'] = $v['orderstatus'];
                $data[$k]['iscancel'] = $v['iscancel'];
                $data[$k]['billtype'] = $v['billtype'];
                $data[$k]['billhead'] = $v['billhead'];
                $data[$k]['billcontent'] = $v['billcontent'];
                $data[$k]['expresstype'] = $v['expresstype'];
                $data[$k]['address'] = $v['address'];
                $data[$k]['istoked'] = $v['istoked'];
                $data[$k]['isurgent'] = $v['isurgent'];
                $data[$k]['urgentshuom'] = $v['urgentshuom'];
                $data[$k]['isorder'] = $v['isorder'];
                $data[$k]['ordertime'] = $v['ordertime'];
                $data[$k]['ordershuom'] = $v['ordershuom'];
                $data[$k]['isstore'] = $v['isstore'];
                $data[$k]['storestart'] = $v['storestart'];
                $data[$k]['storeend'] = $v['storeend'];
                $data[$k]['attribute'] = $v['attribute'];
                $data[$k]['facepic'] = $v['facepic'];
                $data[$k]['cepic'] = $v['cepic'];
                $data[$k]['remark'] = $v['remark'];
                //根据userid和订单编号获取订单详情
                $orderdetail_model = M('orderdetail');
                $array = array();
                $array['userid'] = $v['userid'];
                $array['orderid'] = $v['ordernum'];
                $jieguo = $orderdetail_model->where($array)->find();
                $data[$k]['order_detail']['goodsid'] = $jieguo['goodsid'];
                $data[$k]['order_detail']['picurl'] = $jieguo['picurl'];
                $data[$k]['order_detail']['goodsname'] = $jieguo['goodsname'];
                $data[$k]['order_detail']['goodsaccount'] = $jieguo['goodsaccount'];
                $data[$k]['order_detail']['goodscount'] = $jieguo['goodscount'];
                $data[$k]['order_detail']['buyerwords'] = $jieguo['buyerwords'];
            }
            output_data($data);
        }   
    }







     /*
     * 待收货的订单列表
     */
    public function my_shouhuolist(){
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['orderstatus'] = 1;
        $order_model = M('order');
        $result = $order_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             output_error('没有待收货的订单信息');
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['areaid'] = $v['areaid'];
                $data[$k]['addid'] = $v['addid'];
                $data[$k]['ordernum'] = $v['ordernum'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['buytime'] = $v['buytime'];
                $data[$k]['totalprice'] = $v['totalprice'];
                $data[$k]['orderstatus'] = $v['orderstatus'];
                $data[$k]['iscancel'] = $v['iscancel'];
                $data[$k]['billtype'] = $v['billtype'];
                $data[$k]['billhead'] = $v['billhead'];
                $data[$k]['billcontent'] = $v['billcontent'];
                $data[$k]['expresstype'] = $v['expresstype'];
                $data[$k]['address'] = $v['address'];
                $data[$k]['istoked'] = $v['istoked'];
                $data[$k]['isurgent'] = $v['isurgent'];
                $data[$k]['urgentshuom'] = $v['urgentshuom'];
                $data[$k]['isorder'] = $v['isorder'];
                $data[$k]['ordertime'] = $v['ordertime'];
                $data[$k]['ordershuom'] = $v['ordershuom'];
                $data[$k]['isstore'] = $v['isstore'];
                $data[$k]['storestart'] = $v['storestart'];
                $data[$k]['storeend'] = $v['storeend'];
                $data[$k]['attribute'] = $v['attribute'];
                $data[$k]['facepic'] = $v['facepic'];
                $data[$k]['cepic'] = $v['cepic'];
                $data[$k]['remark'] = $v['remark'];
                //根据userid和订单编号获取订单详情
                $orderdetail_model = M('orderdetail');
                $array = array();
                $array['userid'] = $v['userid'];
                $array['orderid'] = $v['ordernum'];
                $jieguo = $orderdetail_model->where($array)->find();
                $data[$k]['order_detail']['goodsid'] = $jieguo['goodsid'];
                $data[$k]['order_detail']['picurl'] = $jieguo['picurl'];
                $data[$k]['order_detail']['goodsname'] = $jieguo['goodsname'];
                $data[$k]['order_detail']['goodsaccount'] = $jieguo['goodsaccount'];
                $data[$k]['order_detail']['goodscount'] = $jieguo['goodscount'];
                $data[$k]['order_detail']['buyerwords'] = $jieguo['buyerwords'];
            }
            output_data($data);
        }   
    }





    /*
     *我的预约
     */
    public function my_yuyuelist(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
        }
        if($_REQUEST['isorder'] == NULL){
            output_error("参数缺失");
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['isorder'] = $_REQUEST['isorder'];
        $array['flag'] = intval($_REQUEST['flag'])>0?intval($_REQUEST['flag']):0;
        $orderdetail_model = M('orderdetail');
        $result = $orderdetail_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
            if($array['isorder'] == 0){

                output_error('没有未预约的订单信息');
            }else{
                output_error('没有已预约的订单信息');
            }
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['orderid'] = $v['orderid'];
                $data[$k]['goodsid'] = $v['goodsid'];
                $data[$k]['addid'] = $v['addid'];
                $data[$k]['orderstatus'] = $v['orderstatus'];
                $data[$k]['picurl'] = $v['picurl'];
                $data[$k]['goodsname'] = $v['goodsname'];
                $data[$k]['goodsaccount'] = $v['goodsaccount'];
                $data[$k]['goodscount'] = $v['goodscount'];
                $data[$k]['isorder'] = $v['isorder'];
                $data[$k]['ordertime'] = $v['ordertime'];
                $data[$k]['flag'] = $v['flag'];
                $data[$k]['buyerwords'] = $v['buyerwords'];
               
            }
            output_data($data);
        }   
    }







     /*
     *我的收藏
     */
    public function my_store(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $store_model = M('store');
        $result = $store_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             output_error('没有收藏信息');
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['goodsid'] = $v['goodsid'];
                $data[$k]['picurl'] = $v['picurl'];
                $data[$k]['goodsname'] = $v['goodsname'];
                $data[$k]['goodsprice'] = $v['goodsprice'];
                $data[$k]['flag'] = $v['flag'];
            }
            output_data($data);
        }   
    }






    /*
     *我的消息
     */
    public function my_message(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $array = array();
        $array['userid'] = $_REQUEST['userid'];
        $array['messtype'] = intval($_REQUEST['message_type'])>0?$_REQUEST['message_type']:0;
        $message_model = M('message');
        $result = $message_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             output_error('没有信息');
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['userid'] = $v['userid'];
                $data[$k]['sendname'] = $v['sendname'];
                $data[$k]['title'] = $v['title'];
                $data[$k]['messcontent'] = htmlspecialchars($v['messcontent']);
                $data[$k]['messtype'] = $v['messtype'];
                $data[$k]['messagetime'] = $v['messagetime'];
            }
            output_data($data);
        }   
    }



    /*
     *添加头像
     */
    public function add_headpic(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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

        if($_REQUEST['photo'] == NULL){
            output_error("请先上传头像");
        }
        $user_model = M('user');
        //先判断数据库中的头像字段和当前上传的是否一样
        $res = $user_model->where(array('id'=>$arr['userid']))->find();
        if($_REQUEST['photo'] == $res['headurl']){
            output_error("您已上传过该头像了哦");
        }else{
            $photo = "www.edeco.cc/Upload/" . $_REQUEST['photo'];
             //根据userid更新其对应的头像
            $result = $user_model->where(array('id'=>$arr['userid']))->save(array('headurl'=>$photo));
            if($result){
                //头像上传成功
                $data =array();
                $data['user_headpic'] = $photo;
                output_data($data);
            }else{
                output_error("头像上传失败");
            }
        }
       

    }


    /*
     *修改个人信息
     */
    public function editor_userinfo(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        if($_REQUEST['phone'] == NULL && $_REQUEST['username'] == NULL){
            output_error("没有需要修改的参数");
        }
        $arrOpt = array();
        $arrOpt['username'] = $_REQUEST['username'];
        $arrOpt['phone'] = $_REQUEST['phone'];
        
        $user_model = M("user");
        //先去数据库查询出该用户数据
        $result = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        if($arrOpt['username'] == NULL){
            $arrOpt['username'] = $result['username'];
        }
        if($arrOpt['phone'] == NULL){
            $arrOpt['phone'] = $result['phone'];
        }
        $res = $user_model->where(array('id'=>$_REQUEST['userid']))->save($arrOpt);
        if($res){
            $data =array();
            $data['userid'] = $_REQUEST['userid'];
            output_data($data);
        }else{
            output_error("用户信息更新失败");
        }
    }
    




    /*
     *添加收藏
     */
    public function add_shouchang(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        if($_REQUEST['goodsid'] == NULL){
            output_error("参数不全");
        } 
        //先根据goodsid获取到商品的信息
        $goods_model = M('servergoods');
        $goods_info = $goods_model->where(array('id'=>$_REQUEST['goodsid']))->find();
        $arrOpt = array();
        $arrOpt['userid'] = $_REQUEST['userid'];
        $arrOpt['goodsid'] = $_REQUEST['goodsid'];
        $arrOpt['goodsname'] = $goods_info['goodsname'];
        $arrOpt['picurl'] = $goods_info['faceurl'];
        $arrOpt['goodsprice'] = $goods_info['goodsprice1'];
        $store_model = M('store');
        $res = $store_model->add($arrOpt);
        if($res){
            $data =array();
            $data['goodsid'] = $_REQUEST['goodsid'];
            output_data($data);
        }else{
            output_error("添加收藏失败");
        }   
    }






    /*
     *加入购物车
     */
    public function add_shoppingcart(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        if($_REQUEST['goodsid'] == NULL || $_REQUEST['goodscount'] == NULL || $_REQUEST['acount'] == NULL){
            output_error("参数不全");
        } 
        //先根据goodsid获取到商品的信息
        $goods_model = M('servergoods');
        $goods_info = $goods_model->where(array('id'=>$_REQUEST['goodsid']))->find();
        $arrOpt = array();
        $arrOpt['userid'] = $_REQUEST['userid'];
        $arrOpt['areaid'] = $_REQUEST['areaid'];
        $arrOpt['goodsid'] = $_REQUEST['goodsid'];
        $arrOpt['servernum'] = $goods_info['servernum'];
        $arrOpt['goodsname'] = $goods_info['goodsname'];
        $arrOpt['goodspicurll'] = $goods_info['faceurl'];
        $arrOpt['goodsprice'] = $goods_info['goodsprice1'];
        $arrOpt['acount'] = $_REQUEST['acount'];
        $arrOpt['goodscount'] = $_REQUEST['goodscount'];
        $arrOpt['goodsprice2'] = $_REQUEST['goodsprice2'];
        $arrOpt['goodsprice3'] = $_REQUEST['goodsprice3'];
        $arrOpt['goodsprice4'] = $goods_info['goodsprice4'];
        $arrOpt['goodsprice5'] = $goods_info['goodsprice5'];
        //判断该条购物车数据的分类
        if($_REQUEST['goodsprice2'] != NULL && $_REQUEST['goodsprice3'] != NULL){
            //安装+配送
            $arrOpt['attr'] = 2;
        }elseif($_REQUEST['goodsprice2'] == NULL && $_REQUEST['goodsprice3'] != NULL){
            //仅配送
            $arrOpt['attr'] = 2;
        }elseif($_REQUEST['goodsprice2'] != NULL && $_REQUEST['goodsprice3'] == NULL){
            //仅安装
            $arrOpt['attr'] = 1;
        }else{
            //基础建材
            $arrOpt['attr'] = 0;
        }
        $arrOpt['specname'] = $_REQUEST['specname'];
        $arrOpt['colorname'] = $_REQUEST['colorname'];
        $shopping_cart = M('cart');
        $result = $shopping_cart->add($arrOpt);
        if($result){
            $data = array();
            $data['result'] = 1;
            output_data($data);
        }else{
            output_error('加入购物车失败');
        }
    }



    /*
     *取消收藏
     */
    public function cancel_shouchang(){
         if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        if($_REQUEST['goodsid'] == NULL){
            output_error("参数不全");
        } 
        $store_model = M('store');
        $condition = array();
        $condition['userid'] = $arr['userid'];
        $condition['goodsid'] = $_REQUEST['goodsid'];

        $result = $store_model->where($condition)->delete();
        if($result){
            $data =array();
            $data['result'] = true;
            output_data($data);
        }else{
             output_error('取消收藏失败');
        }
    }
   
    
}
   
    
    
    