<?php
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://jizhihuwai.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: @zhiwei
// +----------------------------------------------------------------------
namespace Org\ThinkSDK;
class WeiboConnect {

    private function get_access_token($appkey, $appsecretkey, $code, $callback, $state=null) {
        $url = "https://api.weibo.com/oauth2/access_token";
        $param = array(
            "grant_type"    =>    "authorization_code",
            "client_id"     =>    $appkey,
            "client_secret" =>    $appsecretkey,
            "code"          =>    $code,
            "redirect_uri"  =>    $callback
        );
        dump($param);
        $param = http_build_query($param);
        $response = post($url, $param);
        if($response == false) {
            return false;
        }
        $params = json_decode($response, true);
        return $params["access_token"];
    }

    private function get_openid($access_token) {
        $url = "https://api.weibo.com/oauth2/get_token_info"; 
        $param = array(
            "access_token"    => $access_token
        );

        $param = http_build_query($param);
        $response  = post($url, $param);
        if($response == false) {
            return false;
        }
        $params = json_decode($response, true);
        return $params['uid'];
    }

    public function get_user_info($token, $openid, $appkey=null, $format = "json") {
        $url = "https://api.weibo.com/2/users/show.json";
        $param = array(
            "access_token"      =>    $token,
            "uid"               =>    $openid
        );

        $response = get($url, $param);
        if($response == false) {
            return false;
        }

        $user = json_decode($response, true);
        return $user;
    }

    public function login($appkey, $callback, $scope='') {
        $login_url = "https://api.weibo.com/oauth2/authorize?response_type=code&client_id=" 
            . $appkey . "&scope=$scope&redirect_uri=" . urlencode($callback);
        redirect($login_url);
    }

    public function callback($appkey, $appsecretkey, $callback) {
        $code = $_GET['code'];
        $token = $this->get_access_token($appkey, $appsecretkey, $code, $callback);
        $openid = $this->get_openid($token);
        if(!$token || !$openid) {
            exit('get token or openid error!');
        }

        return array('openid' => $openid, 'token' => $token);
    }

}
