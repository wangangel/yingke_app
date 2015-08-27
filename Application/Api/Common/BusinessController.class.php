<?php

namespace Api\Common;
use Think\Controller;

class BusinessController extends Controller{
    //输出正确的数据
    public function __construct() {
        parent::__construct();
        
        if($_POST['page_size'] == null ){
            $this->page_size = 1;
        }else{
            $this->page_size = $_POST['page_size'];
        }
        
        if($_POST['businessman_key'] != null && $_POST['businessman_id'] != null && $_POST['client'] != null){
            $model_mb_user_token = M('mb_businessman_token');
            //构建查询条件
            $array = array();
            $array['token'] = $_POST['businessman_key'];
            $array['member_id'] = $_POST['businessman_id'];
            $array['client_type'] = $_POST['client'];
        
            $model_token = $model_mb_user_token->where($array)->find();
        
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