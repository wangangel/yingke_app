<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class DictionaryController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 反馈展示
     */

    public function dictionary_list() {
        $model_dictype = M('dictype');        
        //获取总数
        $dictype_count = $model_dictype->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($dictype_count,8);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $dictype_list = $model_dictype->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="add_show";
        $add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="edit_show";
        $edit_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="dictionary_del";
        $dictionary_del = $this->checkAuth($actionName3);

        $this->assign('add_show',$add_show);
        $this->assign('edit_show',$edit_show);
        $this->assign('dictionary_del',$dictionary_del);
        $this->assign('page',$page);
        $this->assign('dictype_list',$dictype_list);
      	$this->display();
    }
    /**
     * 添加前展示
     */
    public function add_show(){
      $this->display();
    }

    /**
     * 进行保存
     */
    public function dictionary_add(){
      $model_dictype = M('dictype');
     /* $arr = array();
      $arr['dictionary'] = $_POST['dictionary'];*/
      $result = $model_dictype->add($_POST);
        if($result){
            $this->success('添加成功',U("admin/dictionary/dictionary_list"));
        }else{
            $this->error('添加失败',U("admin/dictionary/add_show"));
        }

    }

    /**
     * 编辑前
     */
    public function edit_show(){
      $model_dictype = M('dictype');
      $array = array();
      $array['id'] = $_GET['id'];
      $dictype_info = $model_dictype->where(array('id'=>$_GET['id']))->find();
      $this->assign('dictionary_info',$dictype_info);
      $this->display();
    }
    /**
     * 更新、保存
     */
    public function dictionary_update(){
      $model_dictype = M('dictype');
      $result = $model_dictype->save($_POST);

      if($result){
            $this->success('修改成功',U("admin/dictionary/dictionary_list"));
        }else{
            $this->error('修改失败',U("admin/dictionary/edit_show",array('id'=>$_POST['id'])));
        }
    }




   /**
    * 根据id进行删除
    */
   public function dictionary_del(){

   		$model_dictype = M('dictype');
        
        $result = $model_dictype->where(array('id'=>$_GET['id']))->delete();
        if($result){
            $this->success('操作成功！',U("admin/dictionary/dictionary_list"));
        }else{
            $this->error('操作失败',U("admin/dictionary/dictionary_list"));
        }


   }


}