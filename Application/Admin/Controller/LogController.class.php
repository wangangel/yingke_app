<?php

namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
use Think\Upload;
class LogController extends AdminController{
    protected $autoCheckFields =false;
    /*
     * 日志列表
     */
    public function log_list(){
        $model_syslog = M('syslog');
        //获取总数
        $syslog_count = $model_syslog->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($syslog_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $syslog_list = $model_syslog->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="log_del";
        $log_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="log_select_del";
        $log_select_del = $this->checkAuth($actionName2);

        $this->assign('log_del',$log_del);
        $this->assign('log_select_del',$log_select_del);
        $this->assign('page',$page);
        $this->assign('syslog_list',$syslog_list);
        
        $this->display();
    }
   
    /* 
     * 删除
     */
      public function log_del(){
        $array = array();
        $array['id'] = $_GET['id'];
        $model_syslog = M('syslog');
        $result = $model_syslog->where(array('id'=>$_GET['id']))->delete();
        if($result){
            //添加日志
            $type = 2;
            $title = "删除";
            $viewurl = "admin/log/log_del";
            $username =  $_SESSION['admin_name'];
            $res = $this->log_add($type, $title, $viewurl, $username);
            
            $this->success('操作成功！',U("admin/log/log_list"));

        }else{
           $this->error('操作失败',U("admin/log/log_list"));
        }
      }
      /**
       * 选择性的删除
       */
      public function log_select_del(){

        $data =  $_GET['selectAll'];
        $model_syslog = M('syslog');
        $result = $model_syslog->delete($data);
        if($result){
             //添加日志
            $type = 2;
            $title = "删除";
            $viewurl = "admin/log/log_select_del";
            $username =  $_SESSION['admin_name'];
            $res = $this->log_add($type, $title, $viewurl, $username);
            $this->success('操作成功！',U("admin/log/log_list"));
        }else{
            $this->error('操作失败',U("admin/log/log_list"));
        }
      }

  
}