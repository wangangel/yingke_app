<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class AdviceController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 反馈展示
     */

    public function advice_list() {
        $model_advice = M('advice');        
        //获取总数
        $advice_count = $model_advice->count();

        //倒入分页类
        import('Think.Page');
        $page_class = new Page($advice_count,8);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $advice_list = $model_advice->limit($page_class->firstRow.','.$page_class->listRows)->select();

         //为权限加上
        $actionName1["auth_a"]="advice_del";
        $advice_del = $this->checkAuth($actionName1);

        $this->assign('advice_del',$advice_del);
        $this->assign('page',$page);
        $this->assign('advice_list',$advice_list);
      	$this->display();
    }
   /**
    * 根据id进行删除
    */
   
   public function advice_del(){

   		$model_advice = M('advice');
        
        $result = $model_advice->where(array('id'=>$_GET['id']))->delete();
        if($result){
            $this->success('操作成功！',U("admin/advice/advice_list"));
        }else{
            $this->error('操作失败',U("admin/advice/advice_list"));
        }


   }


}