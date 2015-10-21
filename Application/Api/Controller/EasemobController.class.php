<?php
namespace Api\Controller;
use Think\Controller;
class EasemobController extends Controller{
    private $client_id; //YXA6g0XYsF0UEeWWdZ-_YCKAPQ
    private $client_secret;//YXA6b5b1LWFuxBLhZoprsDcyiznOJLY
    private $org_name;//企业的唯一标识,开发者在环信开发者管理后台注册账号时填写的企业ID
    private $app_name;//skyeyeslive#skyeyeslive
    private $url;
   


    /**
     * 初始化参数
     *
     * @param array $options   
     * @param $options['client_id']     
     * @param $options['client_secret'] 
     * @param $options['org_name']      
     * @param $options['app_name']          
     */
    public function __construct($options) {
        $this->client_id = isset ( $options ['client_id'] ) ? $options ['client_id'] : '';
        $this->client_secret = isset ( $options ['client_secret'] ) ? $options ['client_secret'] : '';
        $this->org_name = isset ( $options ['org_name'] ) ? $options ['org_name'] : '';
        $this->app_name = isset ( $options ['app_name'] ) ? $options ['app_name'] : '';
        if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
            $this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
        }
    }
    /**
     * CURL Post
     */
    private function postCurl($url, $option, $header = 0, $type = 'POST') {
        $curl = curl_init (); // 启动一个CURL会话
        curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
        curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
        if (! empty ( $option )) {
            $options = json_encode ( $option );
            curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
        }
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
        curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
        $result = curl_exec ( $curl ); // 执行操作
        //$res = object_array ( json_decode ( $result ) );
        //$res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
        //pre ( $res );
        curl_close ( $curl ); // 关闭CURL会话
        return $result;
    }
    /**
     * 获取Token
     */
    public function getToken() {
        $option ['grant_type'] = "client_credentials";
        $option ['client_id'] = $this->client_id;
        $option ['client_secret'] = $this->client_secret;
        $url = $this->url;
       
        $fp = @fopen ( "easemob.txt", 'r' );
        if ($fp) {
            $arr = unserialize ( fgets ( $fp ) );
            if ($arr ['expires_in'] < time ()) {
                $result = $this->postCurl ($url, $option, $head = 0 );
                $result ['expires_in'] = $result ['expires_in'] + time ();
                @fwrite ( $fp, serialize ( $result ) );
                return $result ['access_token'];
                fclose ( $fp );
                exit ();
            }
            return $arr['access_token'];
            fclose ($fp);
            exit();
        }
        $result = $this->postCurl ( $url, $option, $head = 0 );
        $result = json_decode($result);
        $result['expires_in'] = $result['expires_in']+time();
        $fp = @fopen ( "easemob.txt", 'w' );
        @fwrite ( $fp, serialize ( $result ) );
        return $result ['access_token'];
        fclose ( $fp );
    }
    /**
     * 获取群组详情
     *
     * @param
     * $group_id
     */
    public function chatGroupsDetails($group_id) {
        $url = $this->url . "chatgroups/" . $group_id;
        $access_token = $this->getToken ();
        $header [] = 'Authorization: Bearer ' . $access_token;
        $result = $this->postCurl ( $url, '', $header, $type = "GET" );
        return $result;
    }
    /**
     * 获取app中所有的群组
     */
    public function chatGroups() {
        $url = $this->url . "chatgroups";
        $access_token = $this->getToken ();
        $header [] = 'Authorization: Bearer ' . $access_token;
        $result = $this->postCurl ( $url, '', $header, $type = "GET" );
        //var_dump($result);
        return $result;
    }
}
   
    
    
    