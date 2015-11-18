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
    function object_array($array){
       if(is_object($array)){
        $array = (array)$array;
       }
       if(is_array($array)){
        foreach($array as $key=>$value)
        {
         $array[$key] = object_array($value);
        }
       }
       return $array;
    }
    /**
 * 淘宝IP地址库 Reset API
 * @author Chunice <hrb@usa.com>
 * @param  [string] $ip [IP地址]
 * @return [type]     [只返回获取成功的ip数据]
 */
    public function getLocation($ip) {
        if (empty($ip)) $ip = get_client_ip();
        $taobaoUrl = "http://ip.taobao.com/service/getIpInfo.php?ip=";
        $url       = $taobaoUrl . $ip;
        $data      = self::httpRequest($url);
        $data      = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data);
        $data      = json_decode($data, true);
        return $data[data];
    }

    Static Private function httpRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output === FALSE) {
            return "cURL Error: " . curl_error($ch);
        }
        return $output;
    }
    /**云直播直播转发通知
     * [accpet_params description]
     * @return [type] [description]
     */
    function accpet_params(){
       /* if($_REQUEST['opaque'] == NULL || $_REQUEST['hash_id'] == NULL || $_REQUEST['url'] == NULL){
            output_error('参数不全');
        }else{*/
            $data["id"] = $_REQUEST['opaque'];//房间id
            $data["task_id"] = $_REQUEST['hashed_id'];//任务id
            $data["live_url"] = $_REQUEST['url'];//直播地址
            $data["status"] = "in";
            $data["remark"] = time();
            $result = M("live")->save($data);
    }
    /**
     * 注册生成token,一个账号和设备一个token
     */
    private function _get_token($id,$phone,$client_id) {
        $user_token = M('usertoken');
        //重新登陆后以前的令牌失效,删除之前的token
        $condition = array();
        $condition['userid'] = $id;
        //下面这句可以开启多设备登录
        $condition['client_id'] = $client_id;
        $user_token->where($condition)->delete();

        //生成新的token
        $mb_user_token_info = array();
        $token = md5($phone . strval(NOW_TIME) . strval(rand(0,999999)));
        $mb_user_token_info['userid'] = $id;
        $mb_user_token_info['phone'] = $phone;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['client_id'] = $client_id;
        $mb_user_token_info['effect_time'] = NOW_TIME;
        $result = $user_token->add($mb_user_token_info);
        
        if($result) {
            return $token;
        } else {
            return null;
        }
    }


    /*
     * 用户注册
     */
	public function register(){
		$user_model	= M('user');
        if($_REQUEST['type'] != NULL){
            if ($_REQUEST['uid'] == NULL) {
               output_error('参数不全');
            }
            //判断用户是否绑定过
            $bind_model = M('userbind');
            $bind_info = $bind_model ->where(array("uid"=>$_REQUEST['uid']))->find();
             if(count($bind_info)>0){
                    //根据绑定信息找到用户信息
                    $bind_user = M('user') ->where(array('id'=>$bind_info['userid']))->find();
                    if(!empty($bind_user)){
                      $token = $this->_get_token($bind_user['id'],$bind_user['phone_num'],$_REQUEST['client_id']);  
                        if($token){
                            $register_info['phone_num'] =$bind_user['phone_num'];
                            $register_info['password'] = $bind_user['password'];
                            $register_info['user_id'] = $bind_user['phone_num'];
                            //判断注册类型
                            $register_info['reg_type'] = $bind_user['reg_type'];
                            $register_info['ni_name'] = $bind_user['ni_name'];
                            $register_info['user_name'] = $bind_user['user_name'];
                            $register_info['head_url'] = $bind_user['head_url'];
                            $register_info['sex'] = $bind_user['sex'];
                            $register_info['birth_date'] = $bind_user['birth_date'];
                            $register_info['email'] = $bind_user['email'];
                            $register_info['reg_date']=$bind_user['reg_date'];
                            $user_info = $bind_user['id'];
                            //根据id获取关注我的粉丝数量
                            $focus_model = M('friends_focus');
                            $opt['user_id'] = $bind_user['id'];
                            $opt['status'] = 'yes';
                            $focus_info = $focus_model->where($opt)->select();
                            $focus_num = count($focus_info);
                            $register_info['focus_num'] = $focus_num;
                            //根据id获取关注我的粉丝数量
                            $opt['focus_user'] = $bind_user['id'];
                            $opt['status'] = 'yes';
                            $focused_info = $focus_model->where($opt)->select();
                            $focused_num = count($focused_info);
                            $register_info['fans_num'] = $focused_num;
                        }else{
                            output_error('error');
                        }
                    }
                    
                }else{
                    //重新生成    
                    $str = uniqid(mt_rand(), true);
                    $str = substr($str,0,9);
                    $register_info['phone_num'] = "18" . $str;
                    $register_info['password'] = md5("18" . $str);
                    $register_info['user_id'] = "180" . $str;
                    //判断注册类型
                    if("weibo" == $_REQUEST['type']){
                        $register_info['reg_type'] = '微博';
                    }else if('weixin' == $_REQUEST['type']){
                        $register_info['reg_type'] = '微信';
                    }
                    $register_info['ni_name'] = $_REQUEST['ni_name'];
                    $register_info['user_name'] = $_REQUEST['user_name'];
                    
                    $register_info['head_url'] = $_REQUEST['head_url'];
                    $register_info['sex'] = $_REQUEST['sex'];
                    $register_info['birth_date'] = $_REQUEST['birth_date'];
                    $register_info['email'] = $_REQUEST['email'];
                    $register_info['reg_date']=time();
                    $user_info = $user_model->add($register_info);
                    //把生成的对应信息存储到userbind表中
                    $bind_data['uid'] = $_REQUEST['uid'];
                    $bind_data['userid'] = $user_info;
                    $bind_data['type'] = $_REQUEST['type'];
                    $bind_data['add_date'] = time();
                    $bind_add = M('userbind')->add($bind_data);
                    $register_info['fans_num'] = 0;
                    $register_info['focus_num'] = 0;
            }
            /**---------------------------------------**/ 
        }else{
            if($_REQUEST['password'] == NULL || $_REQUEST['phonenumber'] == NULL || $_REQUEST['client_id'] == NULL){
                 output_error('参数不全');
            }
            $result = $user_model->where(array('phone_num'=>$_REQUEST['phonenumber']))->find();
            if($result != NULL){
                output_error('已经存在该手机用户了!');
            }

            //接收数据
            $register_info = array();
            $register_info['password'] = md5($_REQUEST['password']);
            $register_info['phone_num'] = $_REQUEST['phonenumber'];
            //注册后的用户名为手机号码
            $register_info['ni_name'] = $_REQUEST['phonenumber'];
            $register_info['reg_date'] = time();
            $register_info['status'] = "start";
            $register_info['reg_type'] = "注册";
            $register_info['server_code'] = "QXSJSP";
            $register_info['fans_num'] = 0;
            $register_info['focus_num'] = 0;
            //生成指定规则的userID
            $str = uniqid(mt_rand(), true);
            $str = substr($str,0,9);
            $arr['focus_user'] = "18" . $str;
            //插入数据库之需要查询数据库中是否存在
            $res = $user_model->where($arr)->select();
            $count = count($res);
            if($count){
                //数据库中该userid已经存在,需要重新生成      
                $str = uniqid(mt_rand(), true);
                $str = substr($str,0,9);
                $register_info['user_id'] = "18" . $str;
            }else{
                //数据库中没有
                $register_info['user_id'] = $arr['user_id'];
            }
            $user_info = $user_model->add($register_info);
          
        }

        if($user_info) {
            $token = $this->_get_token($user_info,$register_info['phone_num'],$_REQUEST['client_id']);
            if($token) {
                    //用户注册成功后,同时注册环信
                    if($_REQUEST['type'] != NULL){
                        $hx_opt['username'] = $register_info['phone_num'];
                        $hx_opt['password'] = $register_info['password'];
                    }else{
                        $hx_opt['username'] = $_REQUEST['phonenumber'];
                        $hx_opt['password'] = md5($_REQUEST['password']);
                    }

                    $HX = new \Api\Common\HxController;
                    $hx_info = $HX->openRegister($hx_opt);
                    $hx_a = json_decode($hx_info,true);
                    $hx_save['id'] = $user_info;
                    $hx_save['hx_user'] = $hx_opt['username'];
                    $hx_save['hx_password'] = $hx_opt['password'];
                    $hx_info = $user_model ->save($hx_save);
                        output_data(array(
                        'ID' => $user_info,
                        'userid' => $register_info['phone_num'],
                        'phone'=>$register_info['phone_num'],
                        'nickname' => $register_info['ni_name'],
                        'server_code'=>'QXSJSP',
                        'password'=>$register_info['password'],
                        'key' => $token,
                        'hx_user' => $hx_save['hx_user'],
                        'hx_password' => $hx_save['hx_password'],
                        'headurl'=>$register_info['head_url'],
                        'fans_num' =>$register_info['fans_num'],
                        'focus_num' => $register_info['focus_num']
                        ));
            } else {
                output_error('祝贺您成功注册映客，请尝试登录');
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
        $strSql = "select * from yk_phonecode where phonenum=$intPhone and optype=$intOptype and status='0' and dateline>'".(time()-60)."'";
        $phonecode_model =  M('phonecode');
        $phonecode_info = $phonecode_model->query($strSql);
        $count = count($phonecode_info);
        if($count>0){
            //数据库中有未失效的验证码,有效期为60秒
            output_error("您的验证码已经发送，请不要重复发送");
           
        }else{
            $arr = array();
            $arr['phonenum'] = $intPhone;
            $arr['code'] = $intCode;
            $arr['optype'] = $intOptype;
            $arr['dateline'] = time();
            $arr['status'] = 0;
            $result = $phonecode_model->add($arr);
            if($result>0){
                //向手机发送验证码
                $post_data = "action=send&userid=&account=tianyanhudong&password=152747&mobile=".$intPhone."&sendTime=&content=".rawurlencode("您的验证码为".$intCode.",如非本人操作请忽略,验证码有效时间:1分钟.【天眼互动】");
                $target = "http://sms.chanzor.com:8001/sms.aspx";
                $arrResu = $this->Post_1($post_data,$target);
                output_data(array(
                'phonenum' => $intPhone,
                'code'=>$intCode,
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
     *验证验证码,验证后手机验证码失效
     */
    public function checkphonecode(){
        if($_REQUEST['phone'] == NULL || $_REQUEST['code'] == NULL){
            output_error('参数不全');
        }
        $intPhone =$_REQUEST['phone'];
        $intCode = $_REQUEST['code'];
        $strSql = "update yk_phonecode set status='1' where phonenum='$intPhone' and code='$intCode'";
        $phonecode_model =  M('phonecode');
        $result = $phonecode_model->execute($strSql);
        if($result == 0){
            //验证失败
            output_error("验证码验证失败");
          
        }else{
            //验证成功
             output_data(array(
                'ID' => $result
                
                
                ));
            
        }
    }
     function strToHex($string){ //字符串转十六进制都
        $hex="";
        for($i=0;$i<strlen($string);$i++)
        $hex.=dechex(ord($string[$i]));
        $hex=strtoupper($hex);
        return $hex;
    }  
    /**
     * 鉴权机制
     */
    public function woan_auth(){
       $user_model = M('user');
       if($_REQUEST['username'] == null || $_REQUEST['password'] == null ) {
            $data["ret"] = 1;
            return_data($data);
        }  
        //拿着手机号登录的情况
        $arr = array();
        $arr['phone_num'] = $_REQUEST['username'];
        $arr['password'] = $_REQUEST['password'];
        //$arr['password']  = md5($arr['password']);
        $user_info1 = $user_model->where($arr)->find();
        if(count($user_info1)!=0){
            $re_arr["id"] = $user_info1["id"];
            $re_arr["remark"] =  date('y-m-d h:i:s',time());
            $user_model->save($re_arr);
            $data["ret"] = 0;
            return_data($data);
        }else{
            $data["ret"] = 1;
            return_data($data);
        }
    }
    /*
     *登录--->
     */
    public function login(){
        if($_REQUEST['phone'] == null || $_REQUEST['password'] == null || $_REQUEST['client_id'] == null) {
            output_error('参数不全！');
        }
        $user_model = M('user');
            //拿着手机号登录的情况
            $arr = array();
            $arr['phone_num'] = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
            $arr['password'] = htmlspecialchars($_REQUEST['password'],ENT_QUOTES);
            $arr['password']  = md5($arr['password']);
            $user_info1 = $user_model->where($arr)->select();
            $type = $_REQUEST['type'];
            $openid = $_REQUEST['token'];
            if(!empty($type)){
                if(empty($openid)){
                    output_error('第三方key为空!');
                }
            }
            //第三方绑定
            if('' != $type){
                $aa[$type.'openid'] = $openid;
                $user_info1 = $user_model->where($aa)->select();
            }
            if(!empty($user_info1)){
                $token = $this->_get_token($user_info1[0]['id'], $user_info1[0]['phone_num'], $_REQUEST['client_id']);
                if($token){
                    $data = array();
                    $data['ID'] = $user_info1[0]['id'];
                    $data['userid'] = $user_info1[0]['user_id'];
                    $data['phone'] = $user_info1[0]['phone_num'];
                    $data['nickname'] = $user_info1[0]['ni_name'];
                    $data['headurl'] = $user_info1[0]['head_url'];
                    $data['key'] = $token;
                    $data["password"] = $user_info1[0]['password'];
                    $data["server_code"] = $user_info1[0]['server_code'];
                    //增加环信返回
                    $data["hx_user"] = $user_info1[0]['hx_user'];
                    $data["hx_password"] = $user_info1[0]['hx_password'];
                    //根据id获取我关注的人的数量
                    $focus_model = M('friends_focus');
                    $opt['user_id'] = $user_info1[0]['id'];
                    $opt['status'] = 'yes';
                    $focus_info = $focus_model->where($opt)->select();
                    $focus_num = count($focus_info);
                    $data['focus_num'] = $focus_num;
                    //根据id获取关注我的粉丝数量
                    $opt['focus_user'] = $user_info1[0]['id'];
                    $opt['status'] = 'yes';
                    $focused_info = $focus_model->where($opt)->select();
                    $focused_num = count($focused_info);
                    $data['fans_num'] = $focused_num;
                    output_data($data); 
                }else{
                    output_error('登陆失败');
                }
              
            }else{
                 output_error('登陆失败,账号或密码错误');
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
        $array['client_id'] = $_REQUEST['client_id'];
        $model_user_token = M('usertoken');
        $result = $model_user_token->where($array)->delete();
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
        if($_REQUEST['phonenum'] == null || $_REQUEST['password'] == null){
            output_error('参数不全');
        }

        $user_model = M('user');
        $user_info = $user_model->where(array('phone_num'=>$_REQUEST['phonenum']))->find();
        if($user_info == null){
            output_error('不存在该用户！');
        }
        //如果原密码和现在的修改密码相同则直接提示密码修改失败
        if($user_info['password'] == md5($_REQUEST['password'])){
            $datas = array();
            $datas['msg'] = '和原密码相同,修改失败！';
            output_data($datas);
        }
       
        $array = array();
        $array['password'] = md5($_REQUEST['password']);
        //将密码更新
        $result = $user_model->where(array('phone_num'=>$_REQUEST['phonenum']))->save($array);
        if($result){
                //修改环信密码
                $hx_opt['password']=$user_info['password'];
                $hx_opt['username']=$_REQUEST['phonenumber'];
                $hx_opt['newpassword']= md5($_REQUEST['password']);
                $HX = new \Api\Common\HxController;
                $hx_info = $HX->editPassword($hx_opt);
                $hx_a = json_decode($hx_info,true);
                $hx_save['hx_password']=md5($_REQUEST['password']);
                $hx_info = $user_model-> where(array('phone_num'=>$_REQUEST['phonenum']))->save($hx_save);
            $datas['msg'] = '修改成功，请登陆！';
            output_data($datas);
        }else{
            output_error('修改失败，请稍后重试！');
        }
        
        
    }




     /*
     * 获取个人信息
     */
    public function user_info(){
        /* if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
             output_error('请先登录');
        }
        //验证秘钥是否正确
       $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }*/
        if($_REQUEST['userid'] == NULL){
             output_error('参数用户id不能为null');
        }
        $array = array();
        $array['id'] = $_REQUEST['userid'];
        $model_user = M('user');
        $result = $model_user->where($array)->find();
        if($result != NULL){
            $datas = array();
            $datas['userid'] = $result['id'];
            $datas['user_id'] = $result['user_id'];
            $datas['ni_name'] = $result['ni_name'];
            $datas['sex'] = $result['sex'];
            $datas['birth_date'] = $result['birth_date'];
            $datas['lable'] = $result['lable'];
            $datas['head_url'] = $result['head_url'];
            $datas['profession'] = $result['profession'];
            $datas['per_sign'] = $result['per_sign'];
            //获取当前用户关注的人数
            $focus_model = M('friends_focus');
            $opt['user_id'] = $_REQUEST['userid'];
            $opt['status'] = 'yes';
            $focus_num = $focus_model->where($opt)->count();
            $datas['focus_num'] = $focus_num;
            //获取当前用户的粉丝人数
            $opt1['focus_user'] = $_REQUEST['userid'];
            $opt1['status'] = 'yes';
            $fans_num = $focus_model->where($opt1)->count();
            $datas['fans_num'] = $fans_num;
            //获取评分
            $live_model = M('live');
            $live_info = $live_model->where(array('room_user'=>$_REQUEST['userid']))->select();
            $total_score = 0;
            $total_usernum = 0;
            foreach ($live_info as $k => $v) {
                $total_score += $v['score'];
                $total_usernum += $v['score_usernum'];
            }
            $datas['score'] = round($total_score/$total_usernum);
            output_data($datas);
        }else{
            output_error('没有该用户信息');
        }
    }


    /*
     *用户绑定
     */
    public function user_bind(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        if($_REQUEST['email'] == NULL){
            output_error('邮箱绑定参数错误');
        }
        //验证key是否正确,这边需要设备唯一标识
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $userbind_model = M('user');
        //先判断该用户是否已经绑定
        $opt['id'] = $_REQUEST['userid'];
        $opt['email'] = $_REQUEST['email'];
        $bind_info = $userbind_model->where($opt)->find();
        if(empty($bind_info)){
            //没有绑定过
            $arr['email'] = $_REQUEST['email'];
            $arr['id'] = $_REQUEST['userid'];
            $res = $userbind_model->save($arr);
            if($res){
                output_data(array('ID'=>$res));
            }else{
                output_error('绑定失败');
            }
        }else{
            output_error('重复绑定');
        }
    }





    /*
     *获取我关注的人的信息
     */
    public function my_focus(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        //验证key是否正确,这边需要设备唯一标识
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $focus_model = M('friends_focus');
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $opt['user_id'] = $_REQUEST['userid'];
        $opt['status'] = 'yes';
        $focus_info = $focus_model->where($opt)->limit($start,$arrOpt['ps'])->select();
        foreach ($focus_info as $k => $v) {
            //根据focus_user获取被关注人的信息
            $user_model = M('user');
            $tiaojian['id'] = $v['focus_user'];
            $foucs_userinfo = $user_model->where($tiaojian)->find();
            $data['focus_userinfo'][$k]['userid'] = $foucs_userinfo['id'];
            $data['focus_userinfo'][$k]['ni_name'] = $foucs_userinfo['ni_name'];
            $data['focus_userinfo'][$k]['head_url'] = $foucs_userinfo['head_url'];
            $data['focus_userinfo'][$k]['sex'] = $foucs_userinfo['sex'];
            //根据生日获取年龄
            $brithday = date('Y',$foucs_userinfo['birth_date']);
            $now_year = date('Y',time());

            $data['focus_userinfo'][$k]['age'] = $now_year-$brithday;
        }
        output_data($data);
    }






     /*
     *获取我的粉丝的信息
     */
    public function my_fans(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        //验证key是否正确,这边需要设备唯一标识
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $focus_model = M('friends_focus');
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $opt['focus_user'] = $_REQUEST['userid'];
        $opt['status'] = 'yes';
        $fans_info = $focus_model->where($opt)->limit($start,$arrOpt['ps'])->select();
        foreach ($fans_info as $k => $v) {
            //根据user_id获取我的粉丝信息
            $user_model = M('user');
            $tiaojian['id'] = $v['user_id'];
            $fans_userinfo = $user_model->where($tiaojian)->find();
            $data['fans_userinfo'][$k]['userid'] = $fans_userinfo['id'];
            $data['fans_userinfo'][$k]['ni_name'] = $fans_userinfo['ni_name'];
            $data['fans_userinfo'][$k]['head_url'] = $fans_userinfo['head_url'];
            $data['fans_userinfo'][$k]['sex'] = $fans_userinfo['sex'];
            //判断当前用户是否关注过该粉丝
            $tiaojian['user_id'] = $_REQUEST['userid'];
            $tiaojian['focus_user'] = $fans_userinfo['id'];
            $tiaojian['status'] = "yes";
            $is_focus = $focus_model->where($tiaojian)->find();
            if(empty($is_focus)){
                //没有关注过
                $data['fans_userinfo'][$k]['is_focus'] = 0;
            }else{
                $data['fans_userinfo'][$k]['is_focus'] = 1;
            }
        }
        output_data($data);
    }




    /*
     *关注用户
     */
    public function focus_user(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        //验证key是否正确,这边需要设备唯一标识
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        //先判断被关注人是否存在
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['focus_user']))->find();
        if(empty($user_info)){
            output_error('被关注人不存在');
        }else{
            $focus_model = M('friends_focus');
            $opt['user_id'] = $_REQUEST['userid'];
            $opt['focus_user'] = $_REQUEST['focus_user'];
            $opt['status'] = "yes";
            //判断当前用户是否已经关注过该用户
            $res = $focus_model->where($opt)->find();
            if(empty($res)){
                //没有关注过,判断曾经是否取消过对该用户的关注
                $tiaojian['user_id'] = $_REQUEST['userid'];
                $tiaojian['focus_user'] = $_REQUEST['focus_user'];
                $tiaojian['status'] = "no";
                $jieguo = $focus_model->where($tiaojian)->find();
                if(empty($jieguo)){
                    //没有,执行add
                    $array['user_id'] = $_REQUEST['userid'];
                    $array['focus_user'] = $_REQUEST['focus_user'];
                    $array['status'] = "yes";
                    $array['focus_date'] = time();
                    $result = $focus_model->add($array);
                    if($result){
                        output_data(array('result'=>$result));
                    }else{
                         output_error("添加关注失败");
                    }
                }else{
                    //执行save
                    $condition['user_id'] = $_REQUEST['userid'];
                    $condition['focus_user'] = $_REQUEST['focus_user'];
                    $array['status'] = "yes";
                    $array['focus_date'] = time();
                    $result = $focus_model->where($condition)->save($array);
                    if($result){
                        output_data(array('result'=>$result));
                    }else{
                        output_error("添加关注失败");
                    }
                }
            }else{
                output_error("已经关注过该用户");
            }
        }
        
    }









    /*
     *取消关注
     */
    public function cancel_focus(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        //验证key是否正确,这边需要设备唯一标识
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }

        if($_REQUEST['focus_user'] == NULL){
            output_error('参数不全');
        }
        $friendsfocus_model = M('friends_focus');
        $con['userid'] = $_REQUEST['userid'];
        $con['focus_user'] = $_REQUEST['focus_user'];
        $con['status'] = "yes";
        $res = $friendsfocus_model->where($con)->save(array('status'=>'no'));
        if($res){
            $data['status'] = 'no';
            output_data($data);
        }else{
            output_error('取消关注失败');
        }
    }








    /*
     *获取我的财富信息
     */
    public function my_moneyinfo(){
        if($_REQUEST['userid'] == NULL){
            output_error('请先登录');
        }
        //验证key是否正确,这边需要设备唯一标识
        /*$token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }*/
        //根据用户id获取用户信息 
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        $data['userid'] = $user_info['id'];
        $data['income'] = $user_info['income'];
        $data['yue'] = $user_info['income'] - $user_info['cost'];
        $data['card_num'] = $user_info['card_num'];
        output_data($data);
    }









    /*
     *获取我的发布信息
     */
    public function mypublic_info(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
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
        //判断用户是买家还是卖家
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        if($user_info['attr'] == 0){
            //买家
            //获取我的发布,是在热卖商品订单里面
            $hotorder_model = M('hotorder');
            $hotorder_info = $hotorder_model->where(array('buyer_id'=>$_REQUEST['userid']))->select();
            //获取我的发布的个数
            $count = count($hotorder_info);
            foreach ($hotorder_info as $k => $v) {
                //再根据goodsid获取热卖商品详情
                $hotgoods_model = M('hotgoods');
                $hotgoods_info = $hotgoods_model->where(array('id'=>$v['goodsid']))->find();
                $data = array();
                $data['goodsinfo'][$k]['goodsid'] = $hotgoods_info['id'];
                $data['goodsinfo'][$k]['goodsname'] = $hotgoods_info['goodsname'];
                $data['goodsinfo'][$k]['goodprice'] = $hotgoods_info['goodprice'];
                //根据服务区域的id获取国旗展示
                $area_model = M('servicearea');
                $arr['id'] = $hotgoods_info['area'];
                $area_info = $area_model->where($arr)->find();
                $data['goodsinfo'][$k]['country_pic'] = $area_info['country_picurl'];

            }
            $data['public_count'] = $count;
            output_data($data);
        }elseif($user_info['attr'] == 1){
            //卖家
            //获取我的发布,是在计时购商品中
            $timegoods_model = M('timegoods');
            $timegoods_info = $timegoods_model->where(array('userid'=>$_REQUEST['userid']))->select();
            $data = array();
            $count = count($timegoods_info);
            foreach ($timegoods_info as $k => $v) {
                $data['goodsinfo'][$k]['goodsid'] = $v['id'];
                $data['goodsinfo'][$k]['goodsname'] = $v['goodsname'];
                $data['goodsinfo'][$k]['goodsprice'] = $v['goodsprice'];
                //根据服务区域的id获取国旗展示
                $area_model = M('servicearea');
                $arr['id'] = $v['area'];
                $area_info = $area_model->where($arr)->find();
                $data['goodsinfo'][$k]['country_pic'] = $area_info['country_picurl'];
            }
            $data['public_count'] = $count;
            output_data($data);
        }
    }




    /*
     *获取用户可提现金额
     */
    public function user_tixian(){
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
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        //用户可提现金额=收入-消费
        $tixian = $user_info['income'] - $user_info['cost'];
        output_data(array('tixian'=>$tixian));
    }




    /*
     *获取用户绑定银行卡的信息
     *
     */
    public function user_bindcard(){
        if($_REQUEST['userid'] == NULL){
            output_error('请先登录');
        }
        /* //验证key是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }*/
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->find();
        if($user_info['card_num'] == NULL){
            //该用户没有绑定银行卡信息
            output_error("无绑定银行卡信息");
        }else{

            $data['userid'] = $user_info['id'];
            $data['card_bank'] = $user_info['card_bank'];
            $data['card_name'] = $user_info['card_name'];
            $data['card_num'] = $user_info['card_num'];
            output_data($data);
        }

    }




    /*
     * 用户绑定银行卡
     * 
     */
    public function bind_bankcard(){
        if($_REQUEST['userid'] == NULL){
            output_error('请先登录');
        }
         //验证key是否正确
        /*$token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }*/

        if($_REQUEST['card_username'] == NULL || $_REQUEST['bankname'] == NULL || $_REQUEST['card_num'] == NULL){
            output_error('参数不全');
        }
        //先去查询该用户是否已经绑定过银行卡
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->find();
        $opt['card_name'] = $_REQUEST['card_username'];
        $opt['card_bank'] = $_REQUEST['bankname'];
        $opt['card_num'] = $_REQUEST['card_num'];
        if($user_info['card_num'] == NULL){
            //该用户没有绑定银行卡信息
            $res = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->save($opt);
            if($res){
                $opt['ID'] = $res;
                //绑定成功
                output_data($opt);
            }else{
                output_error('银行卡绑定失败');
            }
        }else{
            //该用户已经绑定银行卡了,执行更新操作
            $res = $user_model->where(array('id'=>$_REQUEST['userid']))->save($opt);
            if($res){
                //绑定成功
                $opt['ID'] = $res;
                output_data($opt);
            }else{
                output_error('银行卡绑定失败');
            }
        }


    }


    /*
     *余额提现
     */
    public function cash_tixian(){
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
        if($_REQUEST['money'] == NULL){
            output_error('参数不全');
        }
        //判断用户的提现金额是否大于余额
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        //用户可提现金额=收入-消费
        $tixian = $user_info['income'] - $user_info['cost'];
        if($_REQUEST['money'] > $tixian){
            output_error('提现金额大于余额');
        }else{
            $opt['apply_date'] = time();
            $opt['apply_user'] = $user_info['ni_name'];
            $opt['apply_phone'] = $user_info['phone_num'];
            $opt['wd_money'] = $_REQUEST['money'];
            $opt['card_bank'] = $user_info['card_bank'];
            $opt['card_name'] = $user_info['card_name'];
            $opt['card_num'] = $user_info['card_num'];
            $opt['status'] = "no";
            $withdrawals_model = M('withdrawals');
            $res = $withdrawals_model->add($opt);
            //将用户的收入对应的减去提现金额
            $res1 = $user_model->where(array('id'=>$_REQUEST['userid']))->setInc('cost',$_REQUEST['money']);
            if($res1){
                output_data(array('ID'=>$res));
            }else{
                output_error('提现申请失败');
            }
        }

    }



    /*
     * 获取当前用户提现记录表
     */
    public function tixian_record(){
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
        //1.现获取当前用户的手机号
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->find();
        $user_phone = $user_info['phone_num'];
        //2.根据手机号码获取当前用户的提现记录
        $withdrawals_model = M('withdrawals');
        $tixian_info = $withdrawals_model->where(array('apply_phone'=>$user_phone))->select();
        foreach ($tixian_info as $k => $v) {
            $data['tixian'][$k]['ID'] = $v['id'];
            $data['tixian'][$k]['apply_date'] = $v['apply_date'];
            $data['tixian'][$k]['wd_money'] = $v['wd_money'];
            if($v['status'] == "start"){
                //提现完成
                $data['tixian'][$k]['status'] = "已完成";
            }else{
                $data['tixian'][$k]['status'] = "已申请";
            }
        }
        output_data($data);
    }

    /*
     *获取我的店铺礼物--房主店铺也用
     */
    public function myshop_gift(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
         //验证key是否正确
       /* $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }*/
        $gift_model = M('gift');
        //1.获取用户自定义的礼物
        $gift_info = $gift_model->where(array('userid'=>$_REQUEST['userid']))->where(array('gift_sign'=>"user"))->where(array('status'=>"start"))->select();
        if(empty($gift_info)){
            $data['user_gift'] = NULL;
        }else{
            foreach ($gift_info as $k => $v) {
                $data['user_gift'][$k]['gift_id'] = $v['id'];
                $data['user_gift'][$k]['gift_name'] = $v['gift_name'];
                $data['user_gift'][$k]['gift_pic_url'] = $v['gift_pic_url'];
                $data['user_gift'][$k]['gift_price'] = $v['gift_price'];
                $data['user_gift'][$k]['gift_sign'] = $v['gift_sign'];
            }
        }
       
        //获取系统礼物
        $sysgift_info = $gift_model->where(array('gift_sign'=>"system"))->where(array('status'=>"start"))->select();
        if(empty($sysgift_info)){
            $data['system_gift'] = NULL;
        }else{
            foreach ($sysgift_info as $k => $v) {
                $data['system_gift'][$k]['gift_id'] = $v['id'];
                $data['system_gift'][$k]['gift_name'] = $v['gift_name'];
                $data['system_gift'][$k]['gift_pic_url'] = $v['gift_pic_url'];
                $data['system_gift'][$k]['gift_price'] = $v['gift_price'];
                $data['system_gift'][$k]['gift_sign'] = $v['gift_sign'];
            }
        }
       

        output_data($data);
    }



    /*
     *用户添加自定义礼物
     */
    public function add_gift(){
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
        if($_REQUEST['gift_name']==NULL || $_REQUEST['gift_price']==NULL){
            output_error('参数不全');
        }
        //根据userid获取用户的手机号码
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        $user_phone = $user_info['phone_num'];
        $condition['userid'] = $_REQUEST['userid'];
        $condition['user_phone'] = $user_phone;
        $condition['gift_name'] = $_REQUEST['gift_name'];
        $condition['gift_price'] = $_REQUEST['gift_price'];
        $condition['gift_pic_url'] = $_REQUEST['gift_pic_url'];
        $condition['add_date'] = time();
        $condition['gift_sign'] = "user";
        $gift_model = M('gift');
        $res = $gift_model->add($condition);
        if($res){
            output_data(array('ID'=>$res));
        }else{
            output_error('礼物添加失败');
        }
    }





    /*
     *用户删除自定义礼物
     */
    public function del_gift(){
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
        if($_REQUEST['gift_id']==NULL){
            output_error('参数不全');
        }
        $gift_model = M('gift');
        $con['id'] = $_REQUEST['gift_id'];
        $con['userid'] = $_REQUEST['userid'];
        $res = $gift_model->where($con)->delete();
        if($res){
            output_data(array('result'=>'true'));
        }else{
            output_error('礼物删除失败');
        }
    }







    /*
     *用户获取我的标签
     */
    public function mylabel(){
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
        $user_model = M('user');
        $con['id'] = $_REQUEST['userid'];
        $con['status'] = "start";
        $user_info = $user_model->where($con)->find();
        if($user_info['lable'] == NULL){
            $data['label'] = NULL;
            output_data($data);
        }else{
            $label_info = explode(',',$user_info['lable']);
            $data['label'] = $label_info;
            output_data($data);
        }
    }




    /*
     *添加我的标签
     */
    public function add_mylabel(){
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

        if($_REQUEST['lable'] == NULL){
            output_error('参数不全');
        }

        $user_model = M('user');
        //获取当前用户的label
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->find();
        $lable_info = $user_info['lable'];
        if($lable_info == NULL){
            //之前没有标签
            $lable = $_REQUEST['lable'];
        }else{
            $lable = $lable_info . "," . $_REQUEST['lable'];
        }
        $opt['lable'] = $lable;
        $res = $user_model->where(array('id'=>$_REQUEST['userid']))->save($opt);
        if($res){
            output_data(array('ID'=>$res));
        }else{
            output_error('添加标签失败');
        }
    }



    /*
     *添加意见反馈
     */
    public function add_feedback(){
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
        if($_REQUEST['feedback'] == NULL){
            output_error('参数不全');
        }
        //获取用户信息
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->find();
        $opt['f_name'] = $user_info['ni_name'];
        $opt['f_phone'] = $user_info['phone_num'];
        $opt['f_date'] = time();
        $opt['f_isdelete'] = "no";
        $opt['f_content'] = $_REQUEST['feedback'];
        $opt['f_classify'] = '系统反馈';
        $feedback_model = M('feedback');
        $res = $feedback_model->add($opt);
        if($res){
            output_data(array('ID'=>$res));
        }else{
            output_error('反馈消息失败');
        }

    }




    /*
     *获取系统推荐好友
     */
    public function recommend_info(){
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

        $recommend_model = M('recommend');
        //$sql = "select * from yk_recommend where re_batch=(select max(re_batch) from yk_recommend)";
        //$recommend_info = $recommend_model->query($sql);
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $max_batch = $recommend_model->max('re_batch');
        $opt["re_batch"] = $max_batch;
        $recommend_info = $recommend_model->where($opt)->limit($start,$arrOpt['ps'])->select();
        //根据用户id获取推荐用户的信息
        foreach ($recommend_info as $k => $v) {
            $user_model = M('user');
            $con['id'] = $v['user_id'];
            $user_info = $user_model->where($con)->find();
            $data['recommend'][$k]['userid'] = $user_info['id'];
            $data['recommend'][$k]['head_url'] = $user_info['head_url'];
            $data['recommend'][$k]['ni_name'] = $user_info['ni_name'];
            $data['recommend'][$k]['sex'] = $user_info['sex'];
            //再根据推荐好友的id与当前用户的id获取好友之前的关注状态
            $opt['userid'] = $_REQUEST['userid'];
            $opt['focus_user'] = $user_info['id'];
            $opt['status'] = "yes";
            $focus_model = M('friends_focus');
            $focus_info = $focus_model->where($opt)->find();
            if(empty($focus_info)){
                //没有好友关系
                $data['recommend'][$k]['is_focus'] = "no";
            }else{
                $data['recommend'][$k]['is_focus'] = "yes";
            }

        }
        output_data($data);

    }



    /*
     *条件搜素
     */
    public function tiaojian_search(){
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
        if($_REQUEST['tiaojian'] == NULL){
            output_error("参数不全");
        } 
        //1.先拿条件做昵称模糊查询
        $user_model = M('user');
        $tiaojian = "%".$_REQUEST['tiaojian']."%";
        $con['ni_name'] = array('like',$tiaojian);
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $user_info = $user_model->where($con)->limit($start,$arrOpt['ps'])->select();
        if(!empty($user_info)){
            foreach ($user_info as $k => $v) {
               $data['tiaojian_search'][$k]['userid'] = $v['id'];
               $data['tiaojian_search'][$k]['head_url'] = $v['head_url'];
               $data['tiaojian_search'][$k]['ni_name'] = $v['ni_name'];
               $data['tiaojian_search'][$k]['sex'] = $v['sex'];
                //再根据推荐好友的id与当前用户的id获取好友之前的关注状态
                $opt['userid'] = $_REQUEST['userid'];
                $opt['focus_user'] = $v['id'];
                $opt['status'] = "yes";
                $focus_model = M('friends_focus');
                $focus_info = $focus_model->where($opt)->find();
                if(empty($focus_info)){
                    //没有好友关系
                    $data['tiaojian_search'][$k]['is_focus'] = "no";
                }else{
                    $data['tiaojian_search'][$k]['is_focus'] = "yes";
                }

            }
        }else{
            //为空,条件做userID查询
            $user_info = $user_model->where(array('user_id'=>$_REQUEST['tiaojian']))->find();
            if(empty($user_info)){
                //也没有查询到用户
                $data['tiaojian_search'] = NULL;
            }else{
                 $data['tiaojian_search'][$k]['userid'] = $user_info['id'];
               $data['tiaojian_search'][$k]['head_url'] = $user_info['head_url'];
               $data['tiaojian_search'][$k]['ni_name'] = $user_info['ni_name'];
               $data['tiaojian_search'][$k]['sex'] = $user_info['sex'];
                //再根据推荐好友的id与当前用户的id获取好友之前的关注状态
                $opt['userid'] = $_REQUEST['userid'];
                $opt['focus_user'] = $user_info['id'];
                $opt['status'] = "yes";
                $focus_model = M('friends_focus');
                $focus_info = $focus_model->where($opt)->find();
                if(empty($focus_info)){
                    //没有好友关系
                    $data['tiaojian_search'][$k]['is_focus'] = "no";
                }else{
                    $data['tiaojian_search'][$k]['is_focus'] = "yes";
                }

            }
        }
        output_data($data);
    }



    /*
     *获取正在直播的房间(当前房间人数,还未完成)---以及72小时
     */
    public function live_room(){
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
            /*$live_ids = $this->live_list();
                var_dump($live_ids);
            */
            //获取72小时在直播
            $ps = intval($_REQUEST['ps'])>15?intval($_REQUEST['ps']):30;
            $page = intval($_REQUEST['page'])>1?intval($_REQUEST['page']):0;
            $past_live = $this->getlive_list($ps,$page);
            $time = strtotime("-3 day");
            $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
            $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
            $start = ($arrOpt['page']-1)*$arrOpt['ps'];
            $live_model = M('live');
            $map["status"] = "in";
            $map["task_id"] = array('neq',"");
            $map['add_date'] = array('egt',$time);
            $liveroom_counts = $live_model->where($map)->count();
            //var_dump($liveroom_counts);
            $con_arr['status']  = array(array('eq',"in"),array('eq',"success"),'or');
            $con_arr['add_date'] = array('egt',$time);
            $con_arr["task_id"] = array('neq',"");
            $liveroom_info = $live_model->where($con_arr)->order('status asc,add_date desc')->limit($start,$arrOpt['ps'])->select();
            //echo $live_model->getlastsql();
                foreach ($liveroom_info as $k => $v) {
                    $data['liveroom_info'][$k]['room_id'] = $v['id'];
                    $data['liveroom_info'][$k]['room_name'] = $v['room_name'];
                    $data['liveroom_info'][$k]['room_pic_url'] = $v['room_pic_url'];
                    $data['liveroom_info'][$k]['isopen'] = $v['isopen'];
                    $data['liveroom_info'][$k]['fees'] = $v['fees'];
                    $data['liveroom_info'][$k]['praise'] = $v['praise'];
                    $data['liveroom_info'][$k]['share_num'] = $v['share_num'];
                    $data['liveroom_info'][$k]['add_date'] = $v['add_date'];
                    $data['liveroom_info'][$k]['live_url'] = $v['live_url'];
                    //根据标签id获取标签信息
                    $tiaojian['id'] = array('in',$v['tags']);
                    $tags_model = M('tags');
                    $tag_info = $tags_model->where($tiaojian)->select();
                    $tags = "";
                    foreach ($tag_info as $key => $value) {
                        $tags .= $value['tag'] . " ";
                    }
                    $data['liveroom_info'][$k]['tags'] = $tags;
                    //根据房主id获取用户的信息
                    $con['id'] = $v['room_user'];
                    $con['status'] = "start";
                    $user_model = M('user');
                    $user_info = $user_model->where($con)->find();
                    $data['liveroom_info'][$k]['user_info']['userid'] = $user_info['id'];
                    $data['liveroom_info'][$k]['user_info']['head_url'] = $user_info['head_url'];
                    $data['liveroom_info'][$k]['user_info']['ni_name'] = $user_info['ni_name'];
                    //获取主播的关注数
                    $focus_model = M('friends_focus');
                    $opt['focus_user'] = $v['room_user'];
                    $opt['status'] = "yes";
                    $focus_cound = $focus_model->where($opt)->count();
                    $data['liveroom_info'][$k]['user_info']['focus_num'] = $focus_cound;
                    //获取当前用户是否关注过该主播
                    $opt1['focus_user'] = $v['room_user'];
                    $opt1['user_id'] = $_REQUEST['userid'];
                   // $opt1['status'] = "yes";
                    $is_focus = $focus_model->where($opt1)->select();
                    if(!empty($is_focus)){
                            //没有关注过
                        $data['liveroom_info'][$k]['user_info']['is_focus'] = "yes";
                        }else{
                        $data['liveroom_info'][$k]['user_info']['is_focus'] = "no";
                    }

                    //获取当前直播间的观众人数
                    $userroom_model = M('user_room');
                    $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$v['id']))->select();
                    $data['liveroom_info'][$k]['user_num'] = count($guanzhong_info);
                    if($k == $liveroom_counts -1){
                        $data['liveroom_info'][$k]["type"] = 0;
                    }else{
                        $data['liveroom_info'][$k]["type"] = 1;
                    }
                    
                }
                output_data($data);
            /*else{

                //获取72小时在直播
                $ps = intval($_REQUEST['ps'])>15?intval($_REQUEST['ps']):30;
                $page = intval($_REQUEST['page'])>1?intval($_REQUEST['page']):0;
                $past_live = $this->getlive_list($ps,$page);
                //var_dump($past_live);
                if($past_live != false){

                    //获得72小时前的时间戳
                    $time = strtotime("-3 day");
                    $cond['status'] = "success";
                    $cond['add_date'] = array('egt',$time);
                    $cond['task_id'] = array('in',$past_live);
                    $live_model = M('live');
                    $live_info = $live_model->where($cond)->order("add_date desc")->limit($start,$arrOpt['ps'])->select();
                    
                    if($live_info != null){
                        foreach ($live_info as $k => $v) {
                            $data['liveroom_info'][$k]['room_id'] = $v['id'];
                            $data['liveroom_info'][$k]['room_name'] = $v['room_name'];
                            $data['liveroom_info'][$k]['room_pic_url'] = $v['room_pic_url'];
                            $data['liveroom_info'][$k]['isopen'] = $v['isopen'];
                            $data['liveroom_info'][$k]['fees'] = $v['fees'];
                            $data['liveroom_info'][$k]['praise'] = $v['praise'];
                            $data['liveroom_info'][$k]['share_num'] = $v['share_num'];
                            $data['liveroom_info'][$k]['add_date'] = $v['add_date'];
                            $data['liveroom_info'][$k]['live_url'] = $v['live_url'];
                            //根据标签id获取标签信息
                            $tiaojian['id'] = array('in',$v['tags']);
                            $tags_model = M('tags');
                            $tag_info = $tags_model->where($tiaojian)->select();
                            $tags = "";
                            foreach ($tag_info as $key => $value) {
                                $tags .= $value['tag'] . " ";
                            }
                            $data['liveroom_info'][$k]['tags'] = $tags;
                            //根据房主id获取用户的信息
                            $con['id'] = $v['room_user'];
                            $con['status'] = "start";
                            $user_model = M('user');
                            $user_info = $user_model->where($con)->find();
                            $data['liveroom_info'][$k]['user_info']['userid'] = $user_info['id'];
                            $data['liveroom_info'][$k]['user_info']['head_url'] = $user_info['head_url'];
                            $data['liveroom_info'][$k]['user_info']['ni_name'] = $user_info['ni_name'];
                            //获取主播的关注数
                            $focus_model = M('friends_focus');
                            $opt['focus_user'] = $v['room_user'];
                            $opt['status'] = "yes";
                            $focus_info = $focus_model->where($opt)->select();
                            $focus_cound = count($focus_info);
                            $data['liveroom_info'][$k]['user_info']['focus_num'] = $focus_cound;
                            //获取当前用户是否关注过该主播
                             $opt['focus_user'] = $v['room_user'];
                             $opt['user_id'] = $_REQUEST['userid'];
                             $opt['status'] = "yes";
                             $is_focus = $focus_model->where($opt)->select();
                             if(empty($is_focus)){
                                //没有关注过
                                $data['liveroom_info'][$k]['user_info']['is_focus'] = "yes";
                             }else{
                                $data['liveroom_info'][$k]['user_info']['is_focus'] = "no";
                             }

                             //获取当前直播间的观众人数
                             $userroom_model = M('user_room');
                             $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$v['id']))->select();
                             $data['liveroom_info'][$k]['user_num'] = count($guanzhong_info);
                             $data['liveroom_info'][$k]["type"] = 1;
                        }
                        output_data($data);
                    }else{
                        //output_error("对不起请浏览正在直播列表!");
                    }
                }else{
                    //output_error("对不起还没有直播房间!");
                }
                
            
            }*/
    }


    /*
     *获取过去72小时的直播房间
     */
    public function past_room(){
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
        $ps = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):30;
        $page = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):0;
        $past_live = $this->getlive_list($ps,$page);
        //var_dump($past_live);
        if($past_live != false){
            $arrOpt = array();
            $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):1;
            $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
            $start = ($arrOpt['page']-1)*$arrOpt['ps'];
            $from_time = date("Y-m-d",strtotime("-3 day"));
            $to_time = date("Y-m-d",strtotime("-1 day"));
            //获得72小时前的时间戳
            $time = strtotime("-3 day");
            $cond['add_date'] = array('egt',$time);
            $cond['task_id'] = array('in',$past_live);

            $live_model = M('live');
            $live_info = $live_model->where($cond)->order("add_date desc")->limit($start,$arrOpt['ps'])->select();
            if($live_info != null){
                foreach ($live_info as $k => $v) {
                    $data['pastroom_info'][$k]['room_id'] = $v['id'];
                    $data['pastroom_info'][$k]['room_name'] = $v['room_name'];
                    $data['pastroom_info'][$k]['room_pic_url'] = $v['room_pic_url'];
                    $data['pastroom_info'][$k]['isopen'] = $v['isopen'];
                    $data['pastroom_info'][$k]['fees'] = $v['fees'];
                    $data['pastroom_info'][$k]['praise'] = $v['praise'];
                    $data['pastroom_info'][$k]['share_num'] = $v['share_num'];
                    $data['pastroom_info'][$k]['add_date'] = $v['add_date'];
                    $data['pastroom_info'][$k]['live_url'] = $v['live_url'];
                    //根据标签id获取标签信息
                    $tiaojian['id'] = array('in',$v['tags']);
                    $tags_model = M('tags');
                    $tag_info = $tags_model->where($tiaojian)->select();
                    $tags = "";
                    foreach ($tag_info as $key => $value) {
                        $tags .= $value['tag'] . " ";
                    }
                    $data['pastroom_info'][$k]['tags'] = $tags;
                    //根据房主id获取用户的信息
                    $con['id'] = $v['room_user'];
                    $con['status'] = "start";
                    $user_model = M('user');
                    $user_info = $user_model->where($con)->find();
                    $data['pastroom_info'][$k]['user_info']['userid'] = $user_info['id'];
                    $data['pastroom_info'][$k]['user_info']['head_url'] = $user_info['head_url'];
                    $data['pastroom_info'][$k]['user_info']['ni_name'] = $user_info['ni_name'];
                    //获取主播的关注数
                    $focus_model = M('friends_focus');
                    $opt['focus_user'] = $v['room_user'];
                    $opt['status'] = "yes";
                    $focus_info = $focus_model->where($opt)->select();
                    $focus_cound = count($focus_info);
                    $data['pastroom_info'][$k]['user_info']['focus_num'] = $focus_cound;
                    //获取当前用户是否关注过该主播
                     $opt['focus_user'] = $v['room_user'];
                     $opt['user_id'] = $_REQUEST['userid'];
                     $opt['status'] = "yes";
                     $is_focus = $focus_model->where($opt)->select();
                     if(empty($is_focus)){
                        //没有关注过
                        $data['pastroom_info'][$k]['user_info']['is_focus'] = "yes";
                     }else{
                        $data['pastroom_info'][$k]['user_info']['is_focus'] = "no";
                     }

                     //获取当前直播间的观众人数
                     $userroom_model = M('user_room');
                     $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$v['id']))->select();
                     $data['pastroom_info'][$k]['user_num'] = count($guanzhong_info);


                }
                output_data($data);
            }else{
                output_error("对不起请浏览正在直播列表!");
            }
        }else{
            output_error("对不起请浏览别处，数据还未加载!");
        }
        
    }









    /**
     * 获取所有直播的房间
     */
    public function all_room(){
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
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $live_model = M('live');
        //1.获取正在直播的房间
        $liveroom_info = $live_model->where(array('status'=>'in'))->limit($start,$arrOpt['ps'])->order('add_date desc')->select();
        
        if(empty($liveroom_info)){
            $data['liveroom_info'] = NULL;
        }else{
            foreach ($liveroom_info as $k => $v) {
                $data['liveroom_info'][$k]['room_id'] = $v['id'];
                $data['liveroom_info'][$k]['room_name'] = $v['room_name'];
                $data['liveroom_info'][$k]['room_pic_url'] = $v['room_pic_url'];
                $data['liveroom_info'][$k]['isopen'] = $v['isopen'];
                $data['liveroom_info'][$k]['fees'] = $v['fees'];
                $data['liveroom_info'][$k]['praise'] = $v['praise'];
                $data['liveroom_info'][$k]['share_num'] = $v['share_num'];
                $data['liveroom_info'][$k]['add_date'] = $v['add_date'];
                $data['liveroom_info'][$k]['groupid'] = $v['groupid'];
                 //根据标签id获取标签信息
                $tiaojian['id'] = array('in',$v['tags']);
                $tags_model = M('tags');
                $tag_info = $tags_model->where($tiaojian)->select();
                $tags = "";
                foreach ($tag_info as $key => $value) {
                    $tags .= $value['tag'] . " ";
                }
                $data['liveroom_info'][$k]['tags'] = $tags;
                //根据房主id获取用户的信息
                $con['id'] = $v['room_user'];
                $con['status'] = "start";
                $user_model = M('user');
                $user_info = $user_model->where($con)->find();
                $data['liveroom_info'][$k]['user_info']['userid'] = $user_info['id'];
                $data['liveroom_info'][$k]['user_info']['head_url'] = $user_info['head_url'];
                $data['liveroom_info'][$k]['user_info']['ni_name'] = $user_info['ni_name'];
                //获取主播的关注数
                $focus_model = M('friends_focus');
                $opt['focus_user'] = $v['room_user'];
                $opt['status'] = "yes";
                $focus_info = $focus_model->where($opt)->select();
                $focus_cound = count($focus_info);
                $data['liveroom_info'][$k]['user_info']['focus_num'] = $focus_cound;
                //获取当前用户是否关注过该主播
                 $opt['focus_user'] = $v['room_user'];
                 $opt['user_id'] = $_REQUEST['userid'];
                 $opt['status'] = "yes";
                 $is_focus = $focus_model->where($opt)->select();
                 if(empty($is_focus)){
                    //没有关注过
                    $data['liveroom_info'][$k]['user_info']['is_focus'] = "yes";
                 }else{
                    $data['liveroom_info'][$k]['user_info']['is_focus'] = "no";
                 }

                 //获取当前直播间的观众人数
                 $userroom_model = M('user_room');
                 $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$v['id']))->select();
                 $data['liveroom_info'][$k]['user_num'] = count($guanzhong_info);
            }
        }
            
        //2.获取过去72小时的直播间
        //获得72小时前的时间戳
        $time = strtotime("-3 day");
        $cond['add_date'] = array('egt',$time);
        $live_info = $live_model->where($cond)->limit($start,$arrOpt['ps'])->select();
        if(empty($live_info)){
            $data['pastroom_info'] = NULL;
        }else{
            foreach ($live_info as $key => $val) {
                $data['pastroom_info'][$key]['room_id'] = $val['id'];
                $data['pastroom_info'][$key]['room_name'] = $val['room_name'];
                $data['pastroom_info'][$key]['room_pic_url'] = $val['room_pic_url'];
                $data['pastroom_info'][$key]['isopen'] = $val['isopen'];
                $data['pastroom_info'][$key]['fees'] = $val['fees'];
                $data['pastroom_info'][$key]['praise'] = $val['praise'];
                $data['pastroom_info'][$key]['share_num'] = $val['share_num'];
                $data['pastroom_info'][$key]['add_date'] = $val['add_date'];
                //根据标签id获取标签信息
                $tiaojian['id'] = array('in',$val['tags']);
                $tags_model = M('tags');
                $tag_info = $tags_model->where($tiaojian)->select();
                $tags = "";
                foreach ($tag_info as $ke => $value) {
                    $tags .= $value['tag'] . " ";
                }
                $data['pastroom_info'][$key]['tags'] = $tags;
                //根据房主id获取用户的信息
                $con['id'] = $val['room_user'];
                $con['status'] = "start";
                $user_model = M('user');
                $user_info = $user_model->where($con)->find();
                $data['pastroom_info'][$key]['user_info']['userid'] = $user_info['id'];
                $data['pastroom_info'][$key]['user_info']['head_url'] = $user_info['head_url'];
                $data['pastroom_info'][$key]['user_info']['ni_name'] = $user_info['ni_name'];
                //获取主播的关注数
                $focus_model = M('friends_focus');
                $opt['focus_user'] = $val['room_user'];
                $opt['status'] = "yes";
                $focus_info = $focus_model->where($opt)->select();
                $focus_cound = count($focus_info);
                $data['pastroom_info'][$key]['user_info']['focus_num'] = $focus_cound;
                //获取当前用户是否关注过该主播
                 $opt['focus_user'] = $val['room_user'];
                 $opt['user_id'] = $_REQUEST['userid'];
                 $opt['status'] = "yes";
                 $is_focus = $focus_model->where($opt)->select();
                 if(empty($is_focus)){
                    //没有关注过
                    $data['pastroom_info'][$key]['user_info']['is_focus'] = "yes";
                 }else{
                    $data['pastroom_info'][$key]['user_info']['is_focus'] = "no";
                 }

                 //获取当前直播间的观众人数
                 $userroom_model = M('user_room');
                 $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$val['id']))->select();
                 $data['pastroom_info'][$key]['user_num'] = count($guanzhong_info);


            }
        }
        
        output_data($data);
    }



    /*
     *创建直播间
     */
    public function add_liveroom(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录！');
        }
         //验证key是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确！！');
        }
        if($_REQUEST['room_name'] == NULL || $_REQUEST['room_pic_url'] == NULL || $_REQUEST['tags'] == NULL || $_REQUEST['isopen'] == NULL){
             output_error('参数不全');
        }
        $live_model = M('live');
        $opt['room_name'] = $_REQUEST['room_name'];
        $opt['room_pic_url'] = $_REQUEST['room_pic_url'];
        $opt['room_user'] = $_REQUEST['userid'];
        $opt['isopen'] = $_REQUEST['isopen'];
        $opt['fees'] = $_REQUEST['fees'];
        //$opt['status'] = "in";
        $opt['add_date'] = time();

        //获取环信组id
        //群组名称
        $hx_opt['groupname']=$_REQUEST['room_name'];
        //是否公开
        $hx_opt['public']=true;
        //加入群组是否需要审核
        $hx_opt['approval']=false;
        //群组管理员,默认房主
        //根据userid获取该用户的环信帐号密码
        $hx_udata['id'] = $_REQUEST['userid'];
        $hx_ui = M('user')->where($hx_udata)->find();
        $hx_opt['owner']= $hx_ui['hx_user'];
        //群组描述
        $hx_opt['desc']=$_REQUEST['room_name'];
        $HX = new \Api\Common\HxController;
        $hx_info = $HX->createGroups($hx_opt);
        $hx_a = json_decode($hx_info,true);
        //dump($hx_a);
        $data['groupid'] = $hx_a['data']['groupid'];
        $opt['groupid'] = $hx_a['data']['groupid'];
        $tags = explode(',', $_REQUEST['tags']);
        if(empty($tags)){
            $opt['tags'] = NULL;
        }else{
            $tag = '';
            foreach ($tags as $k => $v) {
                $tag .= "#" . $v . "," ;
            }
        }
        $opt['tags'] = $tag;
        //添加定位
        $opt["add_city"] = $this->getLocation($_SERVER['REMOTE_ADDR']);
        $res = $live_model->add($opt);
        //dump($res);
        if($res){
            $data['ID'] = $res;
            output_data($data);
        }else{
            output_data('创建直播间失败!');
        }

    }




    /*
     * 添加标签
     */
    public function add_tags(){
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
        if($_REQUEST['tag'] == NULL){
            output_error('参数不全');
        }
        $tags_model = M('tags');
        //先判断数据库中是否存在了该标签
        $opt['tag'] = "#" . $_REQUEST['tag'];
        $tags_info = $tags_model->where($opt)->find();
        if(empty($tags_info)){
            //数据库中没有该标签
            $cond['tag'] = "#" . $_REQUEST['tag'];
            $cond['add_date'] = time();
            $res = $tags_model->add($cond);
            if($res){
                output_data(array('ID'=>$res));
            }else{
                output_error('添加标签失败');
            }
        }else{
            //数据库中已经存在,则该标签的添加次数加一
            $res = $tags_model->where($opt)->setInc('add_num',1);
            if($res){
                $data['ID'] = $tags_info['id'];
                output_data($data);
            }else{
                output_error('添加标签失败');
            }
        }
    }



    /*
     *根据热度获取前五个标签展示
     */
    public function show_tags(){
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
        $tags_model = M('tags');
        $tag_info = $tags_model->where()->order('add_num desc')->limit(5)->select();
        foreach ($tag_info as $k => $v) {
            $data['tags_info'][$k]['tag_id'] = $v['id'];
            $data['tags_info'][$k]['tag_name'] = $v['tag'];
        }
        output_data($data);
    }



    /*
     * 获取排行首页展示
     */
    public function show_paihang(){
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
        //1.根据热度取出前五的热门标签展示
        $tags_model = M('tags');
        $tag_info = $tags_model->where()->order('add_num desc')->limit(5)->select();
        foreach ($tag_info as $k => $v) {
            $data['tags_info'][$k]['tag_id'] = $v['id'];
            $data['tags_info'][$k]['tag_name'] = $v['tag'];
        }
        //根据排行第一的标签,取出使用该标签的直播间展示
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):5;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $tag = "%" . $tag_info[0]['id'] . "%";
        $opt['tags'] = array('like',$tag);
       // $opt['status'] = "in";
        $live_model = M('live');
        $live_info = $live_model->where($opt)->limit($start,$arrOpt['ps'])->select();
        foreach ($live_info as $k => $v) {
             $data['liveroom_info'][$k]['room_id'] = $v['id'];
                $data['liveroom_info'][$k]['room_name'] = $v['room_name'];
                $data['liveroom_info'][$k]['room_pic_url'] = $v['room_pic_url'];
                $data['liveroom_info'][$k]['isopen'] = $v['isopen'];
                $data['liveroom_info'][$k]['fees'] = $v['fees'];
                $data['liveroom_info'][$k]['praise'] = $v['praise'];
                $data['liveroom_info'][$k]['share_num'] = $v['share_num'];
                $data['liveroom_info'][$k]['add_date'] = $v['add_date'];
                 //根据标签id获取标签信息
                $tiaojian['id'] = array('in',$v['tags']);
                $tags_model = M('tags');
                $tag_info = $tags_model->where($tiaojian)->select();
                $tags = "";
                foreach ($tag_info as $key => $value) {
                    $tags .= $value['tag'] . " ";
                }
                $data['liveroom_info'][$k]['tags'] = $tags;
                //根据房主id获取用户的信息
                $con['id'] = $v['room_user'];
                $con['status'] = "start";
                $user_model = M('user');
                $user_info = $user_model->where($con)->find();
                $data['liveroom_info'][$k]['user_info']['userid'] = $user_info['id'];
                $data['liveroom_info'][$k]['user_info']['head_url'] = $user_info['head_url'];
                $data['liveroom_info'][$k]['user_info']['ni_name'] = $user_info['ni_name'];
                //获取主播的关注数
                $focus_model = M('friends_focus');
                $opt['focus_user'] = $v['room_user'];
                $opt['status'] = "yes";
                $focus_info = $focus_model->where($opt)->select();
                $focus_cound = count($focus_info);
                $data['liveroom_info'][$k]['user_info']['focus_num'] = $focus_cound;
                //获取当前用户是否关注过该主播
                 $opt['focus_user'] = $v['room_user'];
                 $opt['user_id'] = $_REQUEST['userid'];
                 $opt['status'] = "yes";
                 $is_focus = $focus_model->where($opt)->select();
                 if(empty($is_focus)){
                    //没有关注过
                    $data['liveroom_info'][$k]['user_info']['is_focus'] = "yes";
                 }else{
                    $data['liveroom_info'][$k]['user_info']['is_focus'] = "no";
                 }
                  //获取当前直播间的观众人数
                 $userroom_model = M('user_room');
                 $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$v['id']))->select();
                 $data['liveroom_info'][$k]['user_num'] = count($guanzhong_info);

        }

        output_data($data);
    }




    /*
     *根据标签获取直播放假
     */
    public function liveroom_bytag(){
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
        if($_REQUEST['tagid'] == NULL){
            output_error("参数不全");
        }
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):5;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $tag = "%" . $_REQUEST['tagid'] . "%";
        $opt['tags'] = array('like',$tag);
        //$opt['status'] = "in";
        $live_model = M('live');
        $live_info = $live_model->where($opt)->limit($start,$arrOpt['ps'])->select();
        foreach ($live_info as $k => $v) {
             $data['liveroom_info'][$k]['room_id'] = $v['id'];
                $data['liveroom_info'][$k]['room_name'] = $v['room_name'];
                $data['liveroom_info'][$k]['room_pic_url'] = $v['room_pic_url'];
                $data['liveroom_info'][$k]['isopen'] = $v['isopen'];
                $data['liveroom_info'][$k]['fees'] = $v['fees'];
                $data['liveroom_info'][$k]['praise'] = $v['praise'];
                $data['liveroom_info'][$k]['share_num'] = $v['share_num'];
                $data['liveroom_info'][$k]['add_date'] = $v['add_date'];
                 //根据标签id获取标签信息
                $tiaojian['id'] = array('in',$v['tags']);
                $tags_model = M('tags');
                $tag_info = $tags_model->where($tiaojian)->select();
                $tags = "";
                foreach ($tag_info as $key => $value) {
                    $tags .= $value['tag'] . " ";
                }
                $data['liveroom_info'][$k]['tags'] = $tags;
                //根据房主id获取用户的信息
                $con['id'] = $v['room_user'];
                $con['status'] = "start";
                $user_model = M('user');
                $user_info = $user_model->where($con)->find();
                $data['liveroom_info'][$k]['user_info']['userid'] = $user_info['id'];
                $data['liveroom_info'][$k]['user_info']['head_url'] = $user_info['head_url'];
                $data['liveroom_info'][$k]['user_info']['ni_name'] = $user_info['ni_name'];
                //获取主播的关注数
                $focus_model = M('friends_focus');
                $opt['focus_user'] = $v['room_user'];
                $opt['status'] = "yes";
                $focus_info = $focus_model->where($opt)->select();
                $focus_cound = count($focus_info);
                $data['liveroom_info'][$k]['user_info']['focus_num'] = $focus_cound;
                //获取当前用户是否关注过该主播
                 $opt['focus_user'] = $v['room_user'];
                 $opt['user_id'] = $_REQUEST['userid'];
                 $opt['status'] = "yes";
                 $is_focus = $focus_model->where($opt)->select();
                 if(empty($is_focus)){
                    //没有关注过
                    $data['liveroom_info'][$k]['user_info']['is_focus'] = "yes";
                 }else{
                    $data['liveroom_info'][$k]['user_info']['is_focus'] = "no";
                 }
                  //获取当前直播间的观众人数
                 $userroom_model = M('user_room');
                 $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$v['id']))->select();
                 $data['liveroom_info'][$k]['user_num'] = count($guanzhong_info);

        }

        output_data($data);

    }





    /*
     *购买礼物
     */
    public function buy_gift(){
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
        if($_REQUEST['giftname'] == NULL || $_REQUEST['giftprice'] == NULL || $_REQUEST['number'] == NULL || $_REQUEST['paytype'] == NULL || $_REQUEST['roomuserid'] == NULL){
            output_error("参数不全");
        }
        $opt['trade_date'] = time();
        $opt['trade_type'] = "礼物";
        $opt['trade_name'] = $_REQUEST['giftname'];
        $opt['trade_num'] = $_REQUEST['number'];
        $opt['trade_total'] = $_REQUEST['number'] * $_REQUEST['giftprice'];
        $opt['pay_type'] = $_REQUEST['paytype'];
        //根据房主id,获取卖家信息
        $user_model = M('user');
        $saler_info = $user_model->where(array('id'=>$_REQUEST['roomuserid']))->find();
        $opt['seller_name'] = $saler_info['ni_name'];
        $opt['seller_phone'] = $saler_info['phone_num'];
        //根据userid获取买家的信息
        $buyer_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        $opt['buyers_name'] = $buyer_info['ni_name'];
        $opt['buyers_phone'] = $buyer_info['phone_num'];
        $trade_model = M('trade');
        $res = $trade_model->add($opt);
        if($res){
            //交易信息添加成功
             //交易编号,根据交易编号生成
            $con['trade_no'] = sprintf('%08s', $res);
            $result = $trade_model->where(array('id'=>$res))->save($con);
            if($result){
                //1.对应买家的消费增加
                $res = $user_model->where(array('id'=>$_REQUEST['userid']))->setInc('cost',$opt['trade_total']);
                //2.对应卖家的收入增加
                $saler_income = $opt['trade_total'] * 0.7;
                $res = $user_model->where(array('id'=>$_REQUEST['roomuserid']))->setInc('income',$saler_income);
                output_data(array('ID'=>$res));
            }else{
                output_error('交易编号生成失败');
            }
        }else{
            output_error('购买礼物失败');
        }
       
    }




    /*
     *付费观看
     */
    public function buy_room(){
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
        if($_REQUEST['roomname'] == NULL || $_REQUEST['roomprice'] == NULL  || $_REQUEST['paytype'] == NULL || $_REQUEST['roomuserid'] == NULL){
            output_error("参数不全");
        }
        $opt['trade_date'] = time();
        $opt['trade_type'] = "房间费";
        $opt['trade_name'] = $_REQUEST['roomname'];
        $opt['trade_num'] = "1";
        $opt['trade_total'] = $_REQUEST['roomprice'];
        $opt['pay_type'] = $_REQUEST['paytype'];
        //根据房主id,获取卖家信息
        $user_model = M('user');
        $saler_info = $user_model->where(array('id'=>$_REQUEST['roomuserid']))->find();
        $opt['seller_name'] = $saler_info['ni_name'];
        $opt['seller_phone'] = $saler_info['phone_num'];
        //根据userid获取买家的信息
        $buyer_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        $opt['buyers_name'] = $buyer_info['ni_name'];
        $opt['buyers_phone'] = $buyer_info['phone_num'];
        $trade_model = M('trade');
        $res = $trade_model->add($opt);
        if($res){
            //交易信息添加成功
             //交易编号,根据交易编号生成
            $con['trade_no'] = sprintf('%08s', $res);
            $result = $trade_model->where(array('id'=>$res))->save($con);
            if($result){
                 //1.对应买家的消费增加
                $res = $user_model->where(array('id'=>$_REQUEST['userid']))->setInc('cost',$_REQUEST['roomprice']);
                //2.对应卖家的收入增加
                $saler_income = $opt['trade_total'] * 0.7;
                $res = $user_model->where(array('id'=>$_REQUEST['roomuserid']))->setInc('income',$saler_income);
                output_data(array('ID'=>$res));
            }else{
                output_error('交易编号生成失败');
            }
        }else{
            output_error('付费观看失败');
        }

    }



    /*
     *用户对直播房间点赞
     */
    public function room_dianzan(){
        /*if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
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
        if($_REQUEST['roomid'] == NULL){
            output_error('参数不全');
        }*/
        $dianzan_model = M('roomdianzan');
        //先判断该用户是否对该直播房间点过赞
        $con['userid'] = $_REQUEST['userid'];
        $con['roomid'] = $_REQUEST['roomid'];
        $dianzan_info = $dianzan_model->where($con)->find();
        if(empty($dianzan_info)){
            //之前没有点过赞
            $opt['userid'] = $_REQUEST['userid'];
            $opt['roomid'] = $_REQUEST['roomid'];
            $opt['dateline'] = time();
            $res = $dianzan_model->add($opt);
            if($res){
                //根据房间的id进行保存赞数
                $live["id"] = $_REQUEST['roomid'];
                $live_info = M("live")->where($live)->find();
                $live["praise"] = $live_info['$praise']+1;
                $live_info = M("live")->save($live);
                $HX = new \Api\Common\HxController;
                //根据userid获取环信的
                $hx_udata['id'] = $_REQUEST['userid'];
                $hx_ui = M('user')->where($hx_udata)->find();
                $from_user=$hx_ui['hx_user'];
                $hx_rdata['id'] = $_REQUEST['liveroom_id'];
                $hx_ri = M('live')->where($hx_rdata)->find();
                $username = array($hx_ri['groupid']);
                $content="【系统】".$hx_ui['ni_name'].":点了个赞!";
                $hx_info = $HX->yy_hxSend($from_user, $username, $content, $target_type = "chatgroups", $ext);
                $hx_a = json_decode($hx_info,true);
                output_data($live["praise"]);
            }else{
                output_error("点赞失败");
            }
        }else{
            output_error("已经点过赞了");
        }
    }

    /*
        房间中获取点赞数
    */
    public function getroom_priase(){
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
        if($_REQUEST['roomid'] == NULL){
            output_error('参数不全');
        }
        $live = M('live');
        $con['id'] = $_REQUEST['roomid'];
        $live_info = $live->where($con)->find();
        if(empty($live_info)){
            //s说明错误
            output_error("参数错误!");         
        }else{
            if($live_info["praise"] == ""|| $live_info["praise"]== NULL){
                $data["count"] = 0;  
            }else{
                $data["count"] = $live_info["praise"];
            }
            
            output_data($data);
        }
    }
    /**
    *获取房间粉丝个数
    */
    public function getroom_fans(){
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
        if($_REQUEST['roomid'] == NULL){
            output_error('参数不全');
        }
        $userroom_model = M('user_room');
        $con['liveroom_id'] = $_REQUEST['roomid'];
        $info = $userroom_model->where($con)->count();
        $data["count"] = $info;
        output_data($data);
    }
    /*
     *举报房间
     */

    public function add_jubao(){
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
        if($_REQUEST['roomid'] == NULL || $_REQUEST['content'] == NULL || $_REQUEST['roomusername'] == NULL){
            output_error('参数不全');
        }
        $user_model = M('user');
        //获取举报人信息
        $userinfo = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        $con['re_person'] = $userinfo['ni_name'];
        $con['re_phone'] = $userinfo['phone_num'];
        $con['room_name'] = $_REQUEST['roomusername'];
        //根据直播房间id获取直播房间名
        $live_model = M('live');
        $room_info = $live_model->where(array('id'=>$_REQUEST['roomid']))->find();
        $con['re_room'] = $room_info['room_name'];
        $con['re_date'] = time();
        $con['re_reason'] = htmlspecialchars($_REQUEST['content']);
        $con['room_id'] = $_REQUEST['roomid'];
        $report_model = M('report');
        $res = $report_model->add($con);
        if($res){
            output_data(array('ID'=>$res));
        }else{
            output_error('举报失败');
        }
    }





    /*
     * 对房间进行反馈
     */
    Public function add_roomfeedback(){
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
        if($_REQUEST['feedback'] == NULL){
             output_error('参数不全');
        }
         //获取用户信息
        $user_model = M('user');
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->where(array('status'=>'start'))->find();
        $opt['f_name'] = $user_info['ni_name'];
        $opt['f_phone'] = $user_info['phone_num'];
        $opt['f_date'] = time();
        $opt['f_isdelete'] = "no";
        $opt['f_content'] = $_REQUEST['feedback'];
        $opt['f_classify'] = '直播间反馈';
        $feedback_model = M('feedback');
        $res = $feedback_model->add($opt);
        if($res){
            output_data(array('ID'=>$res));
        }else{
            output_error('反馈消息失败');
        }

    }






    /*
     * 修改密码
     *
     */
    public function edit_password(){
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
        if($_REQUEST['password'] == NULL){
            output_error('密码不能为空');
        }
        $user_model = M('user');
        $password = md5($_REQUEST['password']);
        //先判断之前的密码和现在的密码是否相同
        $user_info = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        if($user_info['password'] == $password){
            //直接提示密码修改成功
            output_data(array('userid'=>$_REQUEST['userid']));
        }else{
            $res = $user_model->where(array('id'=>$_REQUEST['userid']))->save(array('password'=>$password));
            if($res){
                output_data(array('userid'=>$_REQUEST['userid']));
            }else{
                output_error('密码修改失败');
            }
        }
        
    }







    /*
     * 获取用户绑定的信息
     */
    public function band_info(){
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
        $userbind_model = M('userbind');
        $userbind_info = $userbind_model->where(array('userid'=>$_REQUEST['userid']))->find();
        $data['userbind_info']['qq'] = $userbind_info['qq'];
        $data['userbind_info']['weixin'] = $userbind_info['weixin'];
        $data['userbind_info']['weibo'] = $userbind_info['weibo'];
        $data['userbind_info']['renren'] = $userbind_info['renren'];
        output_data($data);
    }



  


    /*
     *我的消息(分为系统消息和提醒通知)
     */
    public function my_message(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
        }
        
        //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }

        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        //先获取系统发送给用户的消息
        $message_model = M('message');
        $sys_message = $message_model->where(array('m_isdelete'=>'no'))->where(array('status'=>'user'))->select();
        //判断系统消息的接收者是否包含当前用户
        $data = array();
        foreach ($sys_message as $k => $v) {
            if($v['m_target'] == "all"){
                //此条消息的接收者为全部用户
                $data['system_message'][$k]['ID'] = $v['id'];
                $data['system_message'][$k]['m_content'] = $v['m_content'];
                $data['system_message'][$k]['m_date'] = $v['m_date'];
                $data['system_message'][$k]['m_user'] = $v['m_user'];
            }else{
                $receive_info = explode(',',$v['m_target']);
                if(in_array($_REQUEST['userid'],$receive_info)){
                    //当前用户在接收者行列
                    $data['system_message'][$k]['ID'] = $v['id'];
                    $data['system_message'][$k]['m_content'] = $v['m_content'];
                    $data['system_message'][$k]['m_date'] = $v['m_date'];
                    $data['system_message'][$k]['m_user'] = $v['m_user'];
                }else{
                    $data['system_message'] = NULL;
                }
            }
        }
        //再获取提醒的消息
        $array['user_id'] = $_REQUEST['userid'];
        $array['isuser'] = "yes";
        $remind_model = M('remind');
        $result = $remind_model->where($array)->order('id desc')->limit($start,$arrOpt['ps'])->select();
        if($result[0] == NULL){
             $data['remind_message'] = NULL;
        }else{
            
            foreach ($result as $k => $v) {
                $data['remind_message'][$k]['ID'] = $v['id'];
                $data['remind_message'][$k]['re_name'] = $v['re_name'];
                $data['remind_message'][$k]['re_content'] = $v['re_content'];
                $data['remind_message'][$k]['re_date'] = $v['re_date'];
            }
        }   

        output_data($data);
        
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
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $arrOpt = array();
        $arrOpt['ni_name'] = $_REQUEST['ni_name'];
        $arrOpt['birth_date'] = strtotime($_REQUEST['birth_date']);
        $arrOpt['sex'] = $_REQUEST['sex'];
        $arrOpt['head_url'] = $_REQUEST['head_url'];
        $arrOpt['profession'] = $_REQUEST['profession'];
        $arrOpt['per_sign'] = $_REQUEST['per_sign'];
        $arrOpt['lable'] = $_REQUEST['lable'];
        $user_model = M("user");
        //先去数据库中查询是否一样昵称存在
        
        //先去数据库查询出该用户数据
        $result = $user_model->where(array('id'=>$_REQUEST['userid']))->find();
        if($arrOpt['ni_name'] == NULL){
            $arrOpt['ni_name'] = $result['ni_name'];
        }
        if($arrOpt['birth_date'] == NULL){
            $arrOpt['birth_date'] = $result['birth_date'];
        }
        if($arrOpt['sex'] == NULL){
            $arrOpt['sex'] = $result['sex'];
        }
        if($arrOpt['head_url'] == NULL){
            $arrOpt['head_url'] = $result['head_url'];
        }
         if($arrOpt['profession'] == NULL){
            $arrOpt['profession'] = $result['profession'];
        }
         if($arrOpt['per_sign'] == NULL){
            $arrOpt['per_sign'] = $result['per_sign'];
        }
        if($arrOpt['lable'] == NULL){
            $arrOpt['lable'] = $result['lable'];
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
    * 用户进入直播间（包括收费直播间）
    */
   public function into_publicroom(){
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

        if($_REQUEST['liveroom_id'] == NULL || $_REQUEST['user_name'] == NULL || $_REQUEST['head_pic'] == NULL){
             output_error('参数不全');
        }
        
        $userroom_model = M('user_room');
        //先带着userid和房间id去查看当前用户是否在直播间内
        $con['userid'] = $_REQUEST['userid'];
        $con['liveroom_id'] = $_REQUEST['liveroom_id'];
        $info = $userroom_model->where($con)->find();
        $arr["id"] = $_REQUEST['liveroom_id'];
        $live = M("live")->where($arr)->find();
        //var_dump($info["id"]);
        if(empty($info)){
            if($live["room_user"] != $_REQUEST['userid']){//区分房主与观众
                //则用户可以进入直播间
                $conf['userid'] = $_REQUEST['userid'];
                $conf['liveroom_id'] = $_REQUEST['liveroom_id'];
                $conf['username'] = $_REQUEST['user_name'];
                if($_REQUEST['head_pic'] =="" || $_REQUEST['head_pic']==null){
                    $conf['head_pic'] = "http://ua.tdimg.com:8080/picture/4167/4167";
                }else{
                    $conf['head_pic'] = $_REQUEST['head_pic'];
                }
                $city = $this->getLocation($_SERVER['REMOTE_ADDR']);
                $conf['city'] = $city;
                $res = $userroom_model->add($conf);
            }
            $data["isin"] = "false";
            //$res = true;
            //获取直播间的直播url
            //根据房间类型,来判断用户是否支付过 
            $da['id'] = $_REQUEST['liveroom_id'];
             $info_sf = M('live')->where($da)->find();
            if($info_sf['isopen'] == '收费'){
                $pay['pay_userid'] = $_REQUEST['userid'];
                $pay['liveroom_id'] = $_REQUEST['liveroom_id'];
                $pay_info = M('pay')->where($pay)->select();
                if(count($pay_info) == 0){
                    //if($pay_info['pay_status'] == 'yes'){
                        $data['pay_status'] = 'no';
                        $data['isin'] ='pay';
                        //$da['isin'] ='pay';
                        $data['room_price'] =$info_sf["fees"];
                    //}
                }
            }
            $data["live_url"] = $live["live_url"];
            $data["live_id"] = $live["id"];
            $data["groupid"] = $live["groupid"];
            $data["praise"] = $live["praise"];
            $data["score"] =  intval($live["score"])/intval($live["score_usernum"]);
            //用户进入直播间,根据liveroom_id来查询环信组,并发送消息
            $HX = new \Api\Common\HxController;
            //根据userid获取环信的
            $hx_udata['id'] = $_REQUEST['userid'];
            $hx_ui = M('user')->where($hx_udata)->find();
            $from_user=$hx_ui['hx_user'];
            $hx_rdata['id'] = $_REQUEST['liveroom_id'];
            $hx_ri = M('live')->where($hx_rdata)->find();
            $username = array($hx_ri['groupid']);
            $content="【系统】".$_REQUEST['user_name'].":进入了房间";
            $hx_info = $HX->yy_hxSend($from_user, $username, $content, $target_type = "chatgroups", $ext);
            $hx_a = json_decode($hx_info,true);
            $data['groupid'] = $hx_ri['groupid'];
            output_data($data);
            
        }else{
            //返回进入打分的数据
            $da["pay_status"] = "yes";
            $da["isin"] = "true";
            //根据房主id获取用户的信息
            $con['id'] = $live['room_user'];
            $con['status'] = "start";
            $user_info = M('user')->where($con)->find();
            $da['userid'] = $user_info['id'];
            $da['head_pic'] = $user_info['head_url'];
            $da['ni_name'] = $user_info['ni_name'];
            //获取当前直播间的观众人数
            $userroom_model = M('user_room');
            $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$_REQUEST['liveroom_id']))->select();
            $da['user_num'] = count($guanzhong_info);
            $da["liveroom_id"] = $_REQUEST['liveroom_id'];
            $da["room_name"] = $live["room_name"];
            $da["add_city"] = $live["add_city"];
            $da['note'] = '该返回值,是用户非正常/未评分退出!';
            output_data($da);

        }
    }





    /*
     * 用户进入邀请好友直播间
     */
    public function into_friendsroom(){
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
        if($_REQUEST['roomuserid'] == NULL){
            output_error('房主不能空');
        }
        //1.判断当前用户和房主是不是还有关系,即双方是相互关注的状态
        $friends_model = M('friends_focus');
        $info1 = $friends_model->where(array('user_id'=>$_REQUEST['userid']))->where(array('focus_user'=>$_REQUEST['roomuserid']))->find();
        $info2 = $friends_model->where(array('user_id'=>$_REQUEST['roomuserid']))->where(array('focus_user'=>$_REQUEST['userid']))->find();
        if(!empty($info1) && !empty($info2)){
            //必须双方关注才是好友哦
            if($_REQUEST['liveroom_id'] == NULL ||  $_REQUEST['user_name'] == NULL || $_REQUEST['head_pic'] == NULL){
                output_error('参数不全');
            }else{
                $con['userid'] = $_REQUEST['userid'];
                $con['liveroom_id'] = $_REQUEST['liveroom_id'];
                $con['username'] = $_REQUEST['user_name'];
                $con['head_pic'] = $_REQUEST['head_pic'];
                $city = $this->getLocation($_SERVER['REMOTE_ADDR']);
                $conf['city'] = $city['city'];
                $userroom_model = M('user_room');
                $res = $userroom_model->add($con);
                if($res){
                    $live_data["id"] = $_REQUEST['liveroom_id'];
                    $live_detail = M("live")->where($live_data)->find();
                    $back_result["live_url"] = $live_detail["live_url"];
                    $back_result["result"] = 'true';
                    output_data($back_result);
                }else{
                    output_error('进入好友直播间失败');
                }

            }

        }else{
            output_error('不是好友关系哦');
        }

    }





    /*
     * 直播间详情
     */
    public function into_liveroom(){
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
        if($_REQUEST['liveroom_id'] == NULL){
            output_error('参数不全');
        }
        $live_model = M('live');
        $liveroom_info = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->find();
        if(empty($liveroom_info)){
            $data['liveroom_info'] = NULL;
        }else{
                $data['liveroom_info']['room_id'] = $liveroom_info['id'];
                $data['liveroom_info']['room_name'] = $liveroom_info['room_name'];
                $data['liveroom_info']['room_pic_url'] = $liveroom_info['room_pic_url'];
                $data['liveroom_info']['praise'] = $liveroom_info['praise'];
                $data['liveroom_info']['groupid'] = $liveroom_info['groupid'];
                //根据房主id获取用户的信息
                $con['id'] = $liveroom_info['room_user'];
                $con['status'] = "start";
                $user_model = M('user');
                $user_info = $user_model->where($con)->find();
                $data['liveroom_info']['roomuser_info']['userid'] = $user_info['id'];
                $data['liveroom_info']['roomuser_info']['head_url'] = $user_info['head_url'];
                $data['liveroom_info']['roomuser_info']['ni_name'] = $user_info['ni_name'];
                 //获取当前直播间的观众人数
                 $userroom_model = M('user_room');
                 $guanzhong_info = $userroom_model->where(array('liveroom_id'=>$liveroom_info['id']))->select();
                 $data['liveroom_info']['user_num'] = count($guanzhong_info);
                 //判断当前用户是否对此房间点过赞
                 $roomdianzan_model = M('roomdianzan');
                 $dianzan_info = $roomdianzan_model->where(array('userid'=>$_REQUEST['userid']))->where(array('roomid'=>$_REQUEST['liveroom_id']))->find();
                 if(empty($dianzan_info)){
                    //没有点过赞
                    $data['liveroom_info']['is_dianzan'] = "no";
                 }else{
                    $data['liveroom_info']['is_dianzan'] = "yes";
                 }
                 //获取当前房间的评分
                 $data['liveroom_info']['pingfen'] = round($liveroom_info['score']/$liveroom_info['score_usernum']);
                //获取当前直播间的观众头像
                $arrOpt = array();
                $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
                $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
                $start = ($arrOpt['page']-1)*$arrOpt['ps'];
                $userroom_model = M('user_room');
                $userinfo = $userroom_model->where(array('liveroom_id'=>$_REQUEST['liveroom_id']))->limit($start,$arrOpt['ps'])->select(); 
                foreach ($userinfo as $k => $v) {
                   $data['liveroom_info']['guanzong_info'][$k]['ID'] = $v['userid'];
                   $data['liveroom_info']['guanzong_info'][$k]['ni_name'] = $v['username'];
                   $data['liveroom_info']['guanzong_info'][$k]['head_pic'] = $v['head_pic'];
                }
                
        }
        output_data($data);

    }
    /**用户退出直播间---要分房主和观众
     * [out_live description]
     * @return [type] [description]
     */
    public function out_live(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error("请先登录");
        } 
        //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $arr['client_id'] = $_REQUEST['client_id'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        if($_REQUEST['liveroom_id'] == NULL){
            output_error('参数不全');
        }
        //根据userid来判断用户是房主还是观众;
        $data["room_user"] = $_REQUEST['userid'];
        $data["id"] = $_REQUEST['liveroom_id'];
        $live_model = M('live');
        $live = $live_model->where($data)->find();
        //如果$live为空证明就是观众,否则就为房主
        if(!empty($live)){
            //是主播就更改房间状态
            $stop["status"] = "success";
            $stop["id"] = $_REQUEST['liveroom_id'];
            M("live")->save($stop);
            $da_1['note'] = "房主退出成功!房间已关闭!";
            $da_1['result'] = 'success';
            output_data($da_1);
        }else{
            //用户退出需要进入评分
            if($_REQUEST['score'] == NULL || $_REQUEST['tag'] == NULL){
                 output_error('参数不全');
              }
            $res = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->setInc('score',$_REQUEST['score']);
            if($res){
                 //打分成功
                 //添加标签
                 $tags_model = M('tags');
                 $tag = explode(',',$_REQUEST['tag']);
                 foreach ($tag as $k => $v) {
                     //每个$v是一个标签,判断该标签是否存在
                     $tag =  $v;
                     $res1 = $tags_model->where(array('tag'=>$tag))->find();
                     if(empty($res1)){
                        //没有该标签
                        $conf1['tag'] = $tag;
                        $conf1['add_date'] = time();
                        $conf1['add_num'] = '0';
                        $jieguo2 = $tags_model->add($conf1);
                        if($jieguo2){
                            $live_info = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->find();
                            $live_tags = $live_info['tags'];
                            $live_tags2 = $live_tags . "," . $tagid;
                            $jiegou3 = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->save(array('tags'=>$live_tags2));
                        }
                     }else{
                        //已经有该标签
                        $res2 = $tags_model->where(array('tag'=>$tag))->setInc('add_num');
                        $tagid = $res1['id'];
                        //先去查看当前房间的tag
                        $live_info = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->find();
                        $live_tags = $live_info['tags'];
                        $pos = strrpos($live_tags,$tagid);
                        if($pos == false){
                            //说明没有
                           $live_tags1 = $live_tags . "," . $tagid;
                           $jiegou1 = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->save(array('tags'=>$live_tags1));
                        }
                     }
                     
                 }
                 $HX = new \Api\Common\HxController;
                //根据userid获取环信的
                $hx_udata['id'] = $_REQUEST['userid'];
                $hx_ui = M('user')->where($hx_udata)->find();
                $from_user=$hx_ui['hx_user'];
                $hx_rdata['id'] = $_REQUEST['liveroom_id'];
                $hx_ri = M('live')->where($hx_rdata)->find();
                $username = array($hx_ri['groupid']);
                $content="【系统】".$hx_ui['ni_name'].":添加标签[".$_REQUEST['tag']."]";
                $hx_info = $HX->yy_hxSend($from_user, $username, $content, $target_type = "chatgroups", $ext);
                $hx_a = json_decode($hx_info,true);
                $da_3['note'] = '观众评分成功,返回广场!';
                $dat_1["userid"] = $_REQUEST['userid'];
                $dat_1["liveroom_id"] = $_REQUEST['liveroom_id'];
                $rs = M('user_room')->where($dat_1)->delete();
                output_data($da_3);
            }else{
                $da_2['note'] = '观众评分失败,需要重新评分!';
                $da_2['liveroom_id'] = $_REQUEST['liveroom_id'];
                output_data($da_2);
            }
        }
        
    }

    /*  
     *  观众分享房间
     */
    public function share_room(){
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
        if($_REQUEST['liveroom_id'] == NULL){
            output_error('参数不全');
        }
        $live_model = M('live');
        
        $res = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->setInc('share_num');
        if($res){
            $live_info = $live_model->where(array('id'=>$_REQUEST['liveroom_id']))->find();
            output_data(array('share_num' =>$live_info['share_num']));
        }else{
            output_error('分享次数累加失败');
        }
    }




    /**
     * 获取当前直播间的关注头像
     */
    public function guanzhong_headpic(){
        if($_REQUEST['userid'] == NULL){
            output_error("请先登录");
        }
        /*//验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }*/
        if($_REQUEST['liveroom_id'] == NULL){
            output_error('参数不全');
        }else{
             //获取当前直播间的观众头像
            $arrOpt = array();
            $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
            $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
            $start = ($arrOpt['page']-1)*$arrOpt['ps'];
            $userroom_model = M('user_room');
            $userinfo = $userroom_model->where(array('liveroom_id'=>$_REQUEST['liveroom_id']))->limit($start,$arrOpt['ps'])->select(); 
            foreach ($userinfo as $k => $v) {
               $data['liveroom_info']['guanzong_info'][$k]['ID'] = $v['userid'];
               $u_data['id'] = $v['userid'];
               $info = M('user')->where($u_data)->find();
               $data['liveroom_info']['guanzong_info'][$k]['per_sign'] = $info['per_sign'];
               $data['liveroom_info']['guanzong_info'][$k]['sex'] = $info['sex'];
               //计算年龄
               $year_1 = (date('Y',time()));
               if(empty($info['birth_date'])){
                $year_2 = (date('Y',time()));
               }else{
                $year_2 = (date('Y',$info['birth_date']));
               }
               $age = intval($year_1)-intval($year_2);
               $f_data['user_id']=$v['userid'];
               $f_count = M('friends_focus')->where($f_data)->count();
               $data['liveroom_info']['guanzong_info'][$k]['ni_name'] = $v['username'];
               $data['liveroom_info']['guanzong_info'][$k]['head_pic'] = $v['head_pic'];
               $data['liveroom_info']['guanzong_info'][$k]['city'] = $v['city'];
               $data['liveroom_info']['guanzong_info'][$k]['userid'] = $v['userid'];
               $data['liveroom_info']['guanzong_info'][$k]['age'] = $age;
               $data['liveroom_info']['guanzong_info'][$k]['focus_count'] =  $f_count;
            }
        }
        //判断数据是否为空
        if(empty($data)){
            //为空给结果为0
            $data['result']=0;
            output_data($data);
        }else{
            output_data($data);
        }
        //dump($data);
        
       
    }

    /*
     *获取房主店铺礼物
     */
    public function roomuser_gift(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        if($_REQUEST['liveroom_id'] == NULL){
            output_error('参数不全');
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
        //根据liveroom_id来获取房主id
        $r_data['id'] = $_REQUEST['liveroom_id'];
        $r_info = M('live')->where($r_data)->find();
        $gift_model = M('gift');
        //1.获取用户自定义的礼物
        $u_data['userid']=$r_info['user_id'];
        $gift_info = $gift_model->where($u_data)->where(array('gift_sign'=>"user"))->where(array('status'=>"start"))->select();
        if(empty($gift_info)){
            $data['user_gift'] = NULL;
        }else{
            foreach ($gift_info as $k => $v) {
                $data['user_gift'][$k]['gift_id'] = $v['id'];
                $data['user_gift'][$k]['gift_name'] = $v['gift_name'];
                $data['user_gift'][$k]['gift_pic_url'] = $v['gift_pic_url'];
                $data['user_gift'][$k]['gift_price'] = $v['gift_price'];
                $data['user_gift'][$k]['gift_sign'] = $v['gift_sign'];
            }
        }
       
        //获取系统礼物
        $sysgift_info = $gift_model->where(array('gift_sign'=>"system"))->where(array('status'=>"start"))->select();
        if(empty($sysgift_info)){
            $data['system_gift'] = NULL;
        }else{
            foreach ($sysgift_info as $k => $v) {
                $data['system_gift'][$k]['gift_id'] = $v['id'];
                $data['system_gift'][$k]['gift_name'] = $v['gift_name'];
                $data['system_gift'][$k]['gift_pic_url'] = $v['gift_pic_url'];
                $data['system_gift'][$k]['gift_price'] = $v['gift_price'];
                $data['system_gift'][$k]['gift_sign'] = $v['gift_sign'];
            }
        }
        output_data($data);
    }



        /**
         * 非正常退出房间,再次进入需要先评分后再进入
         */
    public function anomaly_exit(){
        if($_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        if($_REQUEST['liveroom_id'] == NULL){
            output_error('房间id为空!');
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
        //根据userid和liveroom_id来判断用户是否正常评分退出
        $opt['userid'] = $_REQUEST['userid'];
        $opt['liveroom_id'] = $_REQUEST['liveroom_id'];
        $r_info = M('user_room')->where($opt)->find();
        if(empty($r_info)){
            output_error('无未评分房间!');
        }else{
            output_data($_REQUEST['liveroom_id']); 
        }

    }


       /**
         * 获取系统热门标签以及数量
         */
    public function hot_tags_num(){
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
        //根据userid和liveroom_id来判断用户是否正常评分退出
        $tags_info = M('tags')->order('add_num desc')->limit(3)->select();
        if(empty($tags_info)){
            output_error('标签库没有标签!');
        }else{
            output_data($tags_info); 
        }

    }
    /**
      * 好友列表接口
    */
    public function friend_list(){
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
        $data["user_id"] = $_REQUEST['userid'];
        $data["status"] = "yes";
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $friend_model = M('friends_focus');
        $focus_list = $friend_model->where($data)->getField('focus_user',true); 
        //SELECT `focus_user` FROM `yk_friends_focus` WHERE `user_id`=162 AND `status`="yes"
        $friend["focus_user"] = $_REQUEST['userid'];
        $friend["user_id"] = array('in',$focus_list);
        $friend_list = $friend_model->where($friend)->limit($start,$arrOpt['ps'])->getField('user_id',true); 
        //SELECT `user_id` FROM `yk_friends_focus` WHERE `focus_user` = 162 AND `user_id` IN ('161','163','165','171') LIMIT 0,10
     /*   $sql = $friend_model->getlastsql();
        var_dump($sql);
        var_dump($friend_list);*/

        if(count($friend_list) > 0){
            $userinfo_list = M("user")->select($friend_list);
            foreach ($userinfo_list as $k => $v) {
                $return_data["user_info"][$k]["userid"] = $v["id"];
                $return_data["user_info"][$k]["ni_name"] = $v["ni_name"];
                $return_data["user_info"][$k]["sex"] = $v["sex"];
                $return_data["user_info"][$k]["head_url"] = $v["head_url"];
            }
        }
        output_data($return_data);
    }


    public function hx_chatrecord(){
        $hx = new \Api\Common\HxController;
       // $ql ="select+*+where+groupId+=+";
        $record = $hx->chatRecord('','', 50);
        $data = json_decode($record, true);
        //根据参数判断显示循环聊天记录
        $chat_data= $data['entities'];
        $groupid = '1442494790144';
        for($i=0;$i<count($chat_data);$i++){
            if($chat_data[$i]['groupId']==$groupid){
              $opt .= $chat_data[$i]['from'].":".$chat_data[$i]['payload']['bodies'][0]['msg']."\n";
            }
        }
        //获取聊天记录
    }
      
        /**
         * 判断用户第三方是否绑定
         */
    public function bind_show(){
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
        $opt['id'] = $_REQUEST['userid'];
        //根据userid和liveroom_id来判断用户是否正常评分退出
        $user_info = M('user')->where($opt)->find();
        if(empty($user_info)){
            output_error('该用户不存在!');
        }else{
            if(empty($user_info['weixintoken'])){
                $data['weixin']='no';
            }else{
                $data['weixin']='yes';
            }
            if(empty($user_info['email'])){
                $data['email']='no';
            }else{
                $data['email']='yes';
            }
            if(empty($user_info['weibo'])){
                $data['weibo']='no';
            }else{
                $data['weibo']='yes';
            }
            output_data($data); 
        }
    }
    
    /**
     * [get_pay_status 获取支付状态]
     * @return [type] [description]
     * 1.判断支付类型
     * 2.微信支付直接根据传递参数直接返回
     * 3.支付宝支付，需要进行参数拼接，查询返回
     */
    public function get_pay_status(){
        //判断两种支付类型必传参数
           if($_REQUEST['userid'] == null || $_REQUEST['liveroom_id'] == null){
               output_error('参数不全');     
            }
     //根据支付类型来处理不同的支付方式
            if($_REQUEST['shop_name']==null){
                output_error('商品名称不能为空!');
            }else{
                $weixin_data['pay_userid'] = $_REQUEST['userid'];
                $weixin_data['liveroom_id'] = $_REQUEST['liveroom_id'];
                $weixin_data['shop_name'] = $_REQUEST['shop_name'];
                $weixin_data['shop_type'] = $_REQUEST['shop_type'];
                $pay_info = M('pay') ->where($weixin_data)->find();
                //判断该用户有没有购买该商品
                //dump($pay_info);
                if(count($pay_info)>0){
                    $pay_status = $pay_info['pay_status'];
                    if($pay_status == 1){
                        output_data("已支付");
                    }else{
                        output_error("未支付");
                    }
                }else{
                    output_error('未购买该商品');
                }
            }
    }




}
   
    
    
