<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Controller;
class UmengController extends MobileController{
    
    protected $appkey  = NULL; 
    protected $appMasterSecret     = NULL;
    protected $timestamp        = NULL;
    protected $validation_token = NULL;
   
    function __construct($key, $secret) {
        vendor('Notification.android.AndroidBroadcast');
        vendor('Notification.android.AndroidFilecast');
        vendor('Notification.android.AndroidGroupcast');
        vendor('Notification.android.AndroidUnicast');
        vendor('Notification.android.AndroidCustomizedcast');
        vendor('Notification.ios.IOSBroadcast');
        vendor('Notification.ios.IOSFilecast');
        vendor('Notification.ios.IOSGroupcast');
        vendor('Notification.ios.IOSUnicast');
        vendor('Notification.ios.IOSCustomizedcast');
        $this->appkey = $key;
        $this->appMasterSecret = $secret;
        $this->timestamp = strval(time());
    }
   

    function sendAndroidBroadcast() {
        try {
            $brocast = new \AndroidBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey",           $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            $brocast->setPredefinedKeyValue("description",      "Android test3");
            $brocast->setPredefinedKeyValue("ticker",           "Android broadcast ticker");
            $brocast->setPredefinedKeyValue("title",            "映客");
            $brocast->setPredefinedKeyValue("text",             "Android broadcast 测试");
            $brocast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device. 
            // For how to register a test device, please see the developer doc.
            $brocast->setPredefinedKeyValue("production_mode", "false");
            // [optional]Set extra fields
            $brocast->setExtraField("test", "helloworld");
            print("Sending broadcast notification, please wait...\r\n");
            $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidUnicast() {
        try {
            $unicast = new \AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey",           $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens",    "xx"); 
            $unicast->setPredefinedKeyValue("ticker",           "Android unicast ticker");
            $unicast->setPredefinedKeyValue("title",            "Android unicast title");
            $unicast->setPredefinedKeyValue("text",             "Android unicast text");
            $unicast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device. 
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");
            // Set extra fields
            $unicast->setExtraField("test", "helloworld");
            print("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidFilecast() {
        try {
            $filecast = new \AndroidFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey",           $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            $filecast->setPredefinedKeyValue("ticker",           "Android filecast ticker");
            $filecast->setPredefinedKeyValue("title",            "Android filecast title");
            $filecast->setPredefinedKeyValue("text",             "Android filecast text");
            $filecast->setPredefinedKeyValue("after_open",       "go_app");  //go to app
            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents("aa"."\n"."bb");
            print("Sending filecast notification, please wait...\r\n");
            $filecast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidGroupcast() {
        try {
            /* 
             *  Construct the filter condition:
             *  "where": 
             *  {
             *      "and": 
             *      [
             *          {"tag":"test"},
             *          {"tag":"Test"}
             *      ]
             *  }
             */
            $filter =   array(
                            "where" =>  array(
                                            "and"   =>  array(
                                                            array(
                                                                "tag" => "test"
                                                            ),
                                                            array(
                                                                "tag" => "Test"
                                                            )
                                                        )
                                        )
                        );
                      
            $groupcast = new \AndroidGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter",           $filter);
            $groupcast->setPredefinedKeyValue("ticker",           "Android groupcast ticker");
            $groupcast->setPredefinedKeyValue("title",            "Android groupcast title");
            $groupcast->setPredefinedKeyValue("text",             "Android groupcast text");
            $groupcast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device. 
            // For how to register a test device, please see the developer doc.
            $groupcast->setPredefinedKeyValue("production_mode", "true");
            print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
    /**
    *根据别名进行推送ANDROID
    */
    function sendAndroidCustomizedcast($alias,$alias_type,$message) {
        try {
            $customizedcast = new \AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then 
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias",            $alias);
            // Set your alias_type here
            //$brocast->setPredefinedKeyValue("description",      "Android 描述");
            $customizedcast->setPredefinedKeyValue("alias_type",       $alias_type);
            $customizedcast->setPredefinedKeyValue("ticker",           "Android customizedcast ticker");
            $customizedcast->setPredefinedKeyValue("title",            "通知");
            $customizedcast->setPredefinedKeyValue("text",             $message);
            $customizedcast->setPredefinedKeyValue("after_open",       "go_app");
           // print("Sending customizedcast notification, please wait...\r\n");
            $customizedcast->send();
           // print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
           // print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSBroadcast() {
        try {
            $brocast = new \IOSBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey",           $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            $brocast->setPredefinedKeyValue("description",      "IOS 广播测试");
            $brocast->setPredefinedKeyValue("alert", "IOS 广播测试");
            $brocast->setPredefinedKeyValue("badge", 0);
            $brocast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $brocast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $brocast->setCustomizedField("test", "11");
            print("Sending broadcast notification, please wait...\r\n");
            $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
    /**
    *向ios单个设备进行推送
    */
    function sendIOSUnicast() {
        try {
            $unicast = new \IOSUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey",           $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens",    "XX"); 
            $unicast->setPredefinedKeyValue("alert", "IOS 单播测试");
            $unicast->setPredefinedKeyValue("badge", 0);
            $unicast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $unicast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $unicast->setCustomizedField("test", "helloworld");
            print("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSFilecast() {
        try {
            $filecast = new \IOSFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey",           $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);

            $filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
            $filecast->setPredefinedKeyValue("badge", 0);
            $filecast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $filecast->setPredefinedKeyValue("production_mode", "false");
            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents("aa"."\n"."bb");
            print("Sending filecast notification, please wait...\r\n");
            $filecast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSGroupcast() {
        try {
            /* 
             *  Construct the filter condition:
             *  "where": 
             *  {
             *      "and": 
             *      [
             *          {"tag":"iostest"}
             *      ]
             *  }
             */
            $filter =   array(
                            "where" =>  array(
                                            "and"   =>  array(
                                                            array(
                                                                "tag" => "iostest"
                                                            )
                                                        )
                                        )
                        );
                      
            $groupcast = new \IOSGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter",           $filter);
            $groupcast->setPredefinedKeyValue("alert", "IOS 组播测试");
            $groupcast->setPredefinedKeyValue("badge", 0);
            $groupcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $groupcast->setPredefinedKeyValue("production_mode", "false");
            print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
    /**
    *根据别名进行推送IOS
    */
    function sendIOSCustomizedcast($alias,$alias_type,$message) {
        try {
            $customizedcast = new \IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then 
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", $alias);
            // Set your alias_type here
            //$brocast->setPredefinedKeyValue("description",      "IOS 描述");
            //$customizedcast->setPredefinedKeyValue("title",            "通知");
            $customizedcast->setPredefinedKeyValue("alias_type", $alias_type);
            $customizedcast->setPredefinedKeyValue("alert", $message);
            $customizedcast->setPredefinedKeyValue("badge", 0);
            $customizedcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedcast->setPredefinedKeyValue("production_mode", "false");
            //print("Sending customizedcast notification, please wait...\r\n");
            $customizedcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            //print("Caught exception: " . $e->getMessage());
        }
    }

    public function ceshi(){
        $ios_appkey = "55e65c5be0f55a60dc0001f2";
        $ios_mastersecret = "iezbtuheko6jbvuofbxyzloc3e54bu2v";
        $message = "尊敬的会员，您的好友dada邀请您前往名为琅琊榜房间观看直播";
        $alias = 171;
        $alias_type = "SkyEyesLive_1.1";
        //$ios_push = new UmengController($ios_appkey, $ios_mastersecret);
        //$ios_push->sendIOSCustomizedcast($alias,$alias_type,$message);

        $android_appkey = "563ac69ce0f55abbca000cc1";
        $android_mastersecret = "lcqmehvkg4fj8tlc0eras1uro87oct28";
        $android_push = new UmengController($android_appkey, $android_mastersecret);
        $android_push->sendAndroidCustomizedcast($alias,$alias_type,$message);
    }

    // Set your appkey and master secret here
    //$demo = new UmengController("your appkey", "your app master secret");
    //$demo->sendAndroidUnicast();
    /* these methods are all available, just fill in some fields and do the test
     * $demo->sendAndroidBroadcast();
     * $demo->sendAndroidFilecast();
     * $demo->sendAndroidGroupcast();
     * $demo->sendAndroidCustomizedcast();
     *
     * $demo->sendIOSBroadcast();
     * $demo->sendIOSUnicast();
     * $demo->sendIOSFilecast();
     * $demo->sendIOSGroupcast();
     * $demo->sendIOSCustomizedcast();
     */
    /**
    * 邀请好友进行通知
    */
     public function request_push(){
        if($_REQUEST['visterid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        //验证key是否正确,这边需要设备唯一标识
        $token_model = M('usertoken');
        $arr = array();
        $arr['client_id'] = $_REQUEST['client_id'];
        $arr['userid'] = $_REQUEST['visterid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        if($_REQUEST["device"] == NULL){
            output_error('设备名称为空！');
        }
        if($_REQUEST["liveroom_id"] == NULL){
            output_error('房间id为空！');
        }
        if($_REQUEST["friend_id"] == NULL){
            output_error('好友id为空！');
        }
       /* if($_REQUEST["type"] == NULL || is_numeric($_REQUEST["type"]) == false){
            output_error("推送类型值错误！");
        }*/
        $data["id"] = $_REQUEST['visterid'];
        $user = M("user")->where($data)->find();
        if(!empty($user["ni_name"])){
            $vister_name = $user["ni_name"];
        }else{
            $vister_name = "x童鞋";
        }
        if($_REQUEST["liveroom_id"] != NULL){
            //为邀请推送
            $room["id"] = $_REQUEST["liveroom_id"];
            $live = M("live")->where($room)->find();
            $room_name = $live["room_name"];
            $message = "尊敬的会员，您的好友".$vister_name."邀请您前往名为".$room_name."房间观看直播";
        }elseif($_REQUEST["liveroom_id"] == NULL){
            output_error("房间id不为空！");
        }
        $alias_string = $_REQUEST["friend_id"];
        $alias_type = "SkyEyesLive_1.1";
        $device = $_REQUEST["device"];
        $ios_appkey = "55e65c5be0f55a60dc0001f2";
        $ios_mastersecret = "iezbtuheko6jbvuofbxyzloc3e54bu2v";
        $ios_push = new UmengController($ios_appkey, $ios_mastersecret);
        $android_appkey = "563ac69ce0f55abbca000cc1";
        $android_mastersecret = "lcqmehvkg4fj8tlc0eras1uro87oct28";
        $android_push = new UmengController($android_appkey, $android_mastersecret);
        $alias_arrary = split(',', $alias_string);
        $alias_length = count($alias_arrary);
        if($alias_length>1){
            for ($i=0; $i < count($alias_arrary)-1; $i++) { 
                $alias = $alias_arrary[$i];
                /*if($device == "IOS"){
                    $_push->sendIOSCustomizedcast($alias,$alias_type,$message);
                }else{
                    $_push->sendAndroidCustomizedcast($alias,$alias_type,$message);

                }*/
                //增加是否接收邀请
                if($user["is_invite"] == 0){
                    $ios_push->sendIOSCustomizedcast($alias,$alias_type,$message);    
                    $android_push->sendAndroidCustomizedcast($alias,$alias_type,$message);
                }
                //保存到信息表中
                $mess_data["m_content"] = $message;
                $mess_data["m_target"] = $alias;
                $mess_data["m_date"] = time();
                $mess_data["m_user"] = $vister_name;
                $mess_data["m_isdelete"] = "no";
                $mess_data["status"] = "user";
                M("message")->add($mess_data);
            } 
        }else{
                $alias = $alias_arrary[0];
                
                 //增加是否接收邀请
                if($user["is_invite"] == 0){
                    $ios_push->sendIOSCustomizedcast($alias,$alias_type,$message);    
                    $android_push->sendAndroidCustomizedcast($alias,$alias_type,$message);
                }
                   
                //保存到信息表中
                $mess_data["m_content"] = $message;
                $mess_data["m_target"] = $alias;
                $mess_data["m_date"] = time();
                $mess_data["m_user"] = $vister_name;
                $mess_data["m_isdelete"] = "no";
                $mess_data["status"] = "user";
                M("message")->add($mess_data);

        }
        
        $result["status"] = "success";
        output_data($result);

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
                //进行关注推送
                $vistor_info = M('user')->where(array('id'=>$_REQUEST['userid']))->find(); 
                $message = "尊敬的会员，您的粉丝".$vistor_info["ni_name"]."已经关注了您！";
                if($user_info["is_focus"] == 0){//可以推送
                    $alias_string = $_REQUEST['focus_user'];
                    $alias_type = "SkyEyesLive_1.1";
                    $ios_appkey = "55e65c5be0f55a60dc0001f2";
                    $ios_mastersecret = "iezbtuheko6jbvuofbxyzloc3e54bu2v";
                    $ios_push = new UmengController($ios_appkey, $ios_mastersecret);
                    $android_appkey = "563ac69ce0f55abbca000cc1";
                    $android_mastersecret = "lcqmehvkg4fj8tlc0eras1uro87oct28";
                    $android_push = new UmengController($android_appkey, $android_mastersecret);
                    $ios_push->sendIOSCustomizedcast($alias_string,$alias_type,$message);    
                    $android_push->sendAndroidCustomizedcast($alias_string,$alias_type,$message);

                }
                //保存到信息表中
                $mess_data["m_content"] = $message;
                $mess_data["m_target"] = $_REQUEST['focus_user'];
                $mess_data["m_date"] = time();
                $mess_data["m_user"] = $vistor_info["ni_name"];
                $mess_data["m_isdelete"] = "no";
                $mess_data["status"] = "user";
                M("message")->add($mess_data);
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
}
