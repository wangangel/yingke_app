<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
use Think\Upload;

/**
 * 系统配置信息管理
 */
class SystemController extends AdminController{
    protected $autoCheckFields =false;

    /**
     * 
     * @return [type] [description]
     */
    public function update(){
    	$model_config = M('sysconfig');  
      //执行查询
      //$system_config = $model_config->getField('id','statename','simplename','des','domains','repath','accesspr',1);  
      $system_config = $model_config->find();
      //为权限加上
      $actionName1["auth_a"]="saveorupdate";
      $saveorupdate = $this->checkAuth($actionName1);
      $this->assign('saveorupdate',$saveorupdate);
      $this->assign('system_config',$system_config);
    	$this->display();

    }
    /**更新
     * [saveorupdate description]
     * @return [type] [description]
     */
    public function saveorupdate(){
      $model_config = M('sysconfig');
      $data['id'] = $_POST['id'];
      $data['statename'] = $_POST['statename'];
      $data['simplename'] = $_POST['simplename'];
      $data['des'] = $_POST['des'];
      $data['domains'] = $_POST['domains'];
      $data['repath'] = $_POST['repath'];
      $data['tel'] = $_POST['tel'];
      $data['email'] = $_POST['email'];
      $data['record'] = $_POST['record'];
      $data['address'] = $_POST['address'];
      $data['accesspr'] = $_POST['accesspr'];
      $result = null;
      if ($data['id'] == '') {
        //执行保存
        $result = $model_config->add($_POST);
       }else{
          //执行更新
        $result = $model_config->save($data);
       }
      if($result){
          $this->success('操作成功',U("admin/system/update"));
      }else{
          $this->error('操作失败',U("admin/system/update"));
      }

    }

   
}