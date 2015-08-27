<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class DataController extends AdminController{
	protected $autoCheckFields =false;
	  /*
     * 数据字典展示
     */
    public function data_list() {
        $model_data = M('datadictionary');        
        //获取总数
        $data_count = $model_data->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($data_count,8);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $data_list = $model_data->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="add_show";
        $add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="editor_show";
        $editor_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="data_del";
        $data_del = $this->checkAuth($actionName3);
        $this->assign('editor_show',$editor_show);
        $this->assign('data_del',$data_del);
        $this->assign('add_show',$add_show);
        $this->assign('page',$page);
        $this->assign('data_list',$data_list);
      	$this->display();
    }
    /**
     * 添加前展示
     */
    public function add_show(){
      $model_dictype_list = M('dictype');
      $dictype_list = $model_dictype_list->limit($page_class->firstRow.','.$page_class->listRows)->select();
      $this->assign('dictype_list',$dictype_list);
      $this->display();
    }

    /**
     * 进行保存
     */
    public function data_add(){
      $model_data = M('datadictionary');
      $result = $model_data->add($_POST);
        if($result){
            //添加日志
            $type = 1;
            $title = "添加";
            $viewurl = "/admin/data/data_add";
            $username =  $_SESSION['admin_name'];
            $res = $this->log_add($type, $title, $viewurl, $username);
            $this->success('添加成功',U("admin/data/data_list"));
        }else{
            $this->error('添加失败',U("admin/data/add_show"));
        }

    }

    /**
     * 修改前
     */
    public function editor_show(){
      $model_dictype_list = M('dictype');
      $dictype_list = $model_dictype_list->select();
      $this->assign('dictype_list',$dictype_list);
      $model_dictype = M('datadictionary');
      $array = array();
      $array['id'] = $_GET['id'];
      $data_info = $model_dictype->where(array('id'=>$_GET['id']))->find();
      $this->assign('data_info',$data_info);
      $this->display();
    }
    /**
     * 更新、保存
     */
    public function data_update(){
      $model_data = M('datadictionary');
      $result = $model_data->save($_POST);

      if($result){
            $this->success('修改成功',U("admin/data/data_list"));
        }else{
            $this->error('修改失败',U("admin/data/editor_show",array('id'=>$_POST['id'])));
        }
    }

   /**
    * 根据id进行删除
    */
   public function data_del(){

   		$model_data = M('datadictionary');
        
        $result = $model_data->where(array('id'=>$_GET['id']))->delete();
        if($result){
            $this->success('操作成功！',U("admin/data/data_list"));
        }else{
            $this->error('操作失败',U("admin/data/data_list"));
        }
   }


}