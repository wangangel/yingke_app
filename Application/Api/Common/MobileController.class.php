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
    
    
    
}