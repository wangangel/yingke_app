<?php

namespace Api\Common;
use Think\Controller;

class MobileController extends Controller{
    //输出正确的数据
    public function __construct() {
        parent::__construct();
        
        if($_POST['page_size'] == null ){
            $this->page_size = 1;
        }else{
            $this->page_size = $_POST['page_size'];
        }
    
        if($_REQUEST['key'] != null && $_REQUEST['member_id'] != null && $_REQUEST['client'] != null){
            $model_mb_user_token = D('MbUserToken');
            //构建查询条件
            $array = array();
            $array['token'] = $_REQUEST['key'];
            $array['member_id'] = $_REQUEST['member_id'];
            $array['client_type'] = $_REQUEST['client'];
    
            $model_token = $model_mb_user_token->getMbUserTokenInfo($array);
            
            if(empty($model_token)){
                output_error('请重新登陆');
            }

        }
        
        
        
        if($_POST['limit_page'] == null ){
            $this->limit_page = 5;
        }else{
            $this->limit_page = $_POST['limit_page'];
        }
    }
    
    
    protected $limit_page;
    protected $page_size;

    /**获取13位UNIX时间戳
     * [getMillisecond description]
     * @return [type] [description]
     */
    function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return $t2.ceil( ($t1 * 1000) );
        
    }
    /**
     * 直播签约加密
     * XVS-TIMESTAMP
     * XVS-SIGNATURE
     */
    public function signature($header_timestamp,$url,$data,$key){
        /*$url = "/api/20140928/task_list"; 
        $data = "service_code=QXSJSP";
        $key = "0ec08fd5";*/
        $signature = hash_hmac("sha256", utf8_encode($url.$data.$header_timestamp), utf8_encode($key));
        return $signature;
    }
    //请求地址
    function get($url, $param=array(),$signature,$header_timestamp){
        if(!is_array($param)){
            throw new Exception("参数必须为array");
        }
        $p='';
        foreach($param as $key => $value){
            $p=$p.$key.'='.$value.'&';
        }
        if(preg_match('/\?[\d\D]+/',$url)){//matched ?c
            $p='&'.$p;
        }else if(preg_match('/\?$/',$url)){//matched ?$
            $p=$p;
        }else{
            $p='?'.$p;
        }
        $p=preg_replace('/&$/','',$p);
        $header = $this->FormatHeader($url,$p,$signature,$header_timestamp);
        //var_dump($header);
        $url=$url.$p;
        $httph =curl_init($url);
        curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($httph, CURLOPT_HTTPHEADER, $header);//设置请求头信息
        curl_setopt($httph, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        curl_setopt($httph, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($httph, CURLOPT_HEADER,0);//取得头返回信息
        $rst=curl_exec($httph);
        curl_close($httph);
        return $rst;
    }
    /**拼接请求头
     * [FormatHeader description]
     * @param [type] $url              [description]
     * @param [type] $p                [description]
     * @param [type] $signature        [description]
     * @param [type] $header_timestamp [description]
     */
    function FormatHeader($url,$p,$signature,$header_timestamp){
        // 解悉url
        $temp = $url.$p;
        $header = array (
            "GET'$temp' HTTP/1.1",
            "Host: $url",
            "Content-Type: text/xml; charset=utf-8",
            'Accept: */*',
            "Referer: http://$url/",
            'User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1)',
            //"X-Forwarded-For: {$myIp}",
            "xvs-timestamp:$header_timestamp",
            "xvs-signature:$signature",
            "Content-length: 380",
            "Connection: Close"
        ); 
        return $header;
    } 

    /**获取所有的直播列表
     * [ceshi description]
     * @return [type] [description]
     */
   function live_list() {
       
        $inte_url = "/api/20140928/task_list"; 
        $data = "service_code=QXSJSP";
        $key = "0ec08fd5";
        $header_timestamp = $this->getMillisecond();
        $signature = $this->signature($header_timestamp,$inte_url,$data,$key);
        $url = "c.zhiboyun.com/api/20140928/task_list";
        $params["service_code"] = "QXSJSP";
        $res = $this->get($url,$params,$signature,$header_timestamp);
        $ceshi_params = json_decode($res,TRUE);
        //模拟测试数据
        /*$postArray = '{"ret":0,"user_list":[{"vs_id":"aws-cn_north_1-5","service_code":"QXSJSP","user_name":"001","client_version":"445","device_type":"10"},{"vs_id":"aws-cn_north_1-5","service_code":"QXSJSP","user_name":"admin","client_version":"800","device_type":"20"}],"task_list":[{"id":"aws-cn_north_1-3-d66f8636fe1c1c11","serial":13488215,"sequence":171,"progress":0,"vs_id":"aws-cn_north_1-5","service_code":"QXSJSP","outputs":[{"file_name":"aws-cn_north_1-3-d66f8636fe1c1c11.flv","tag":"tcp_output","audio_codec_name":"aac","video_codec_name":"libx264","format":"flv","width":640,"height":360,"relative_dir":"QXSJSP/20150922/00/28/flv/","http_output_bytes":3226626,"http_connections_num":0,"streams":[{"index":0,"codec_type":1,"codec_id":86018,"copy":0,"width":0,"height":0,"bit_rate":128000},{"index":1,"codec_type":0,"codec_id":28,"copy":1,"width":640,"height":360,"bit_rate":0}]}],"inputs":[{"url":"","service_code":"QXSJSP","user_name":"admin","device_type":20,"device_version":"800"}],"http_live_url":"http://xvs-5.zhiboyun.com:80/live/id/","input_bytes":22176849,"opaque":"73"},{"id":"aws-cn_north_1-3-c56970235a5db4d3","serial":13488214,"sequence":172,"progress":0,"vs_id":"aws-cn_north_1-5","service_code":"QXSJSP","outputs":[{"file_name":"aws-cn_north_1-3-c56970235a5db4d3.flv","tag":"tcp_output","audio_codec_name":"aac","video_codec_name":"libx264","format":"flv","width":1920,"height":1080,"relative_dir":"QXSJSP/20150922/00/29/flv/","http_output_bytes":7488378,"http_connections_num":0,"streams":[{"index":0,"codec_type":1,"codec_id":86018,"copy":0,"width":0,"height":0,"bit_rate":128000},{"index":1,"codec_type":0,"codec_id":28,"copy":0,"width":1920,"height":1080,"bit_rate":0}]}],"inputs":[{"url":"","service_code":"QXSJSP","user_name":"001","device_type":10,"device_version":"445"}],"http_live_url":"http://xvs-5.zhiboyun.com:80/live/id/","input_bytes":18732218,"opaque":"74"}]}'; 
        $ceshi_params = json_decode($postArray,true);*/
        $count_json = count($ceshi_params['user_list']);
        if($count_json != 0){
            for ($i = 0; $i < $count_json; $i++){
                $live_list[$i] = $ceshi_params["task_list"][$i]["opaque"];
                $task_list[$i]["task_id"] = $ceshi_params["task_list"][$i]["id"];
                $live_url[$i]["http_live_url"] = $ceshi_params["task_list"][$i]["http_live_url"].$ceshi_params["task_list"][$i]["outputs"][0]["file_name"];
            }
            //var_dump($task_list);
           /* var_dump($live_list);
            var_dump($task_list);
            var_dump($live_url);*/
            //去直播列表查正在直播的,封装成json，返回userID
            $live = M("live");
            // /var_dump($task_list);
            for ($i=0; $i <count($live_list) ; $i++) { 
                $save_data["id"] = $live_list[$i];
                $save_data["task_id"] = $task_list[$i]["task_id"];
                $save_data["live_url"] = $live_url[$i]["http_live_url"];
                $save_data["status"] = "in";
                $result = $live->save($save_data);
                if(!$result){
                    //未对插入不进去的数据进行收录    
                    break;
                }
            }
            return $live_list;
        }else{
            //报错，或者没人直播的时候
            return false;
        }
        
    }
    /**
     * 停止直播
     */
   /* function stop_record(){
        $task_id = "aws-cn_north_1-3-f2e96a68959e9e4a";
        $inte_url = "/api/20140928/cancel_task"; 
        $data = "service_code=QXSJSP&task_id=".$task_id;
        $key = "0ec08fd5";
        $header_timestamp = $this->getMillisecond();
        $signature = $this->signature($header_timestamp,$inte_url,$data,$key);
        $url = "c.zhiboyun.com/api/20140928/cancel_task";
        $params["service_code"] = "QXSJSP";
        $params["task_id"] = $task_id;
        $res = $this->get($url,$params,$signature,$header_timestamp);
        $back_res = json_decode($res,TRUE);
        var_dump($res);
    }*/

    /**
     * 批量更新数据
     */
    public function saveAll($saveWhere,&$saveData,$tableName){
        if($saveWhere==null||$tableName==null){
            return false;
        }
        //获取更新的主键id名称
        $key = array_keys($saveWhere[0]);
        //获取更新列表的长度
        $len = count($saveWhere[$key]);
        $flag=true;
        $model = isset($model)?$model:M($tableName);
        //开启事务处理机制
        $model->startTrans();
        //记录更新失败ID
        $error["id"];
        for($i=0;$i<$len;$i++){
            //预处理sql语句
            $isRight=$model->where($key.'='.$saveWhere[$key][$i])->save($saveData[$i]);
            if($isRight==0){
                //将更新失败的记录下来
                $error["id"]=$i;
                $flag=false;
            }
            //$flag=$flag&&$isRight;
        }
        if($flag ){
            //如果都成立就提交
            $model->commit();
            return $saveWhere;
        }elseif(count($error)>0&count($error)<$len){
            //先将原先的预处理进行回滚
            $model->rollback();
            for($i=0;$i<count($error);$i++){
                //删除更新失败的ID和Data
                unset($saveWhere[$key][$error[$i]]);
                unset($saveData[$error[$i]]);
            }
            //重新将数组下标进行排序
            $saveWhere[$key]=array_merge($saveWhere[$key]);
            $saveData=array_merge($saveData);
            //进行第二次递归更新
            $this->saveAll($saveWhere,$saveData,$tableName);
            return $saveWhere;
        }
        else{
            //如果都更新就回滚
            $model->rollback();
            return false;
        }
    }
}