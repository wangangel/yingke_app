aa<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Upload;
class ActivityController extends MobileController{
    public function __construct(){
        parent::__construct();
    }


    /*
     *获取安居活动列表
     */
    public function activity_list(){
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):8;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $content_model = M('content');
        $result = $content_model->where(array('channelid'=>6))->order('id desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有安居活动信息');
        }else{
            foreach ($result as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['channelid'] = $v['channelid'];
                $data[$k]['channelname'] = $v['channelname'];
                $data[$k]['title'] = $v['title'];
                $data[$k]['picurl'] =  $v['picurl'];
                $data[$k]['author'] =  $v['author'];
                $data[$k]['releasedate'] =  $v['releasedate'];
                  //先将数据库中的图片src地址替换
                $str = str_replace('src="','src="www.edeco.cc',$v['content']);
                 $str = str_replace('"',"'",$str);
                //$str = str_replace('"',"'",$v['content']);
                $data[$k]['content'] =  htmlspecialchars($str);
              
            }
           
            output_data($data);
        }
    }
   





    /*
     *根据id获取活动详情
     */
    public function activity_detail(){
        $arrOpt = array();
        if($_REQUEST['act_id'] == NULL){
            output_error('参数缺失');
        }
        $arrOpt['id'] = $_REQUEST['act_id'];
        $content_model = M('content');
        $result = $content_model->where($arrOpt)->find();
        $data = array();
        if($result == NULL){
            output_error('没有该安居活动信息');
        }else{
            $data['id'] = $result['id'];
            $data['channelid'] = $result['channelid'];
            $data['channelname'] = $result['channelname'];
            $data['title'] = $result['title'];
            $data['picurl'] =  $result['picurl'];
            $data['author'] =  $result['author'];
            $data['releasedate'] =  $result['releasedate'];
            //先将数据库中的图片src地址替换
             $str = str_replace('src="','src="www.edeco.cc',$result['content']);
            $str = str_replace('"',"'",$str);
            //$str = str_replace('"',"'",$result['content']);
            $data['content'] =  htmlspecialchars($str);
        }
           
        output_data($data);
        
    }


  
   






    
}
   
    
    
    