<?php
use Org\ThinkSDK\ThinkOauth;
class WeixinSDK extends ThinkOauth
{
    /**
     * ��ȡrequestCode��api�ӿ�
     * @var string
     */
    protected $GetRequestCodeURL = 'https://open.weixin.qq.com/connect/qrconnect';
    
    /**
     * ��ȡaccess_token��api�ӿ�
     * @var string
     */
    protected $GetAccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    
    /**
     * API��·��
     * @var string
     */
    protected $ApiBase = 'https://api.weixin.qq.com/';
    
    public function getRequestCodeURL()
    {
        $this->config();
        $params = array(
                'appid' => $this->AppKey,          
                'redirect_uri'=>$this->Callback,
                'response_type'=>'code',
                'scope'=>'snsapi_login'
        );
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }
    
    /**
     * ��ȡaccess_token
     * @param string $code ��һ�����󵽵�code
     */
    public function getAccessToken($code, $extend = null){
        $this->config();
        $params = array(
                'appid'     => $this->AppKey,
                'secret'    => $this->AppSecret,
                'grant_type'    => $this->GrantType,
                'code'          => $code,
        );

        $data = $this->http($this->GetAccessTokenURL, $params, 'POST');
        $this->Token = $this->parseToken($data, $extend);
        return $this->Token;
    }

    
    /**
     * ��װ�ӿڵ��ò��� �����ýӿ�
     * @param  string $api    ΢��API
     * @param  string $param  ����API�Ķ������
     * @param  string $method HTTP���󷽷� Ĭ��ΪGET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false){
        /* ��Ѷ΢�����ù������� */
        $params = array(
            'access_token'       => $this->Token['access_token'],
            'openid'             => $this->openid(),
        );

        $vars = $this->param($params, $param);
        $data = $this->http($this->url($api), $vars, $method, array(), $multi);
        return json_decode($data, true);
    }
    
    
    /**
     * ����access_token���������ķ���ֵ
     */
    protected function parseToken($result, $extend)
    {
        $data = json_decode($result,true);
        //parse_str($result, $data);
        if($data['access_token'] && $data['expires_in']){
            $this->Token    = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new Exception("��ȡ΢�� ACCESS_TOKEN ����{$result}");
    }
    
    /**
     * ��ȡ��ǰ��ȨӦ�õ�openid
     */
    public function openid()
    {
        $data = $this->Token;
        if(!empty($data['openid']))
            return $data['openid'];
        else
            exit('û�л�ȡ��΢���û�ID��');
    }
}

?>