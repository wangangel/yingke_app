<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Upload;
class ChatController extends MobileController{
    

  
   function test(){
        $options = array();  
        $options ['client_id'] = "YXA6g0XYsF0UEeWWdZ-_YCKAPQ";
        $options ['client_secret'] = "YXA6b5b1LWFuxBLhZoprsDcyiznOJLY";
        $options ['org_name'] = "skyeyeslive";
        $options ['app_name'] = "skyeyeslive#skyeyeslive";

        $e = new \Api\Controller\EasemobController($options);
        $res = $e->chatGroups();
        var_dump($res);
   }






    
}
   
    
    
    