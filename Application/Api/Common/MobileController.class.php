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
        //$header_timestamp = date("Y-m-d\TH:i:s",time());
        //$header_timestamp = "2015-02-05T10:27:42";
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
}