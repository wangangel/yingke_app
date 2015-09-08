<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Controller;

class LoginController extends AdminController{
  
    /*
     * 登录
     */
    public function login(){
        //dump($_POST);
        if($_POST){
            if(empty($_POST['username'])||empty($_POST['password'])){
               $this->error('账号或者密码不能为空！'); 
            }else{
                $array['account'] = $_POST['username'];
                $array['password'] = md5($_POST['password']);
                $array['status']='start';             
                $model_account = M('account');
                $user_info = $model_account->where($array)->find();
                if($user_info){
                    $_SESSION['admin_name'] = $user_info['account'];
                    $_SESSION['admin_id'] = $user_info['id'];
                    $_SESSION['employee'] = $user_info['employee'];
                    //增添的角色
                    $_SESSION['role_id'] = $user_info['role'];
                    //$_SESSION['login_time'] = date("Y-m-d H:i:s", time()) ; 
                    $this->success('登录成功！',U('admin/index/index'));
                    //添加日志
                    $type = 0;
                    $title = "登录";
                    $viewurl = "/login";
                    $username =  $_SESSION['admin_name'];
                    $res = $this->log_add($type, $title, $viewurl, $username);
                    die;
                }else{
                    $this->error('账号或者密码错误！');
                }
            }
            
        }
        
        $this->display();
        
    }
    
    /*
     * 注销
     */
    public function logout(){
        $_SESSION = array();
        $this->success('退出成功！！',U('admin/login/login'));
    }
    
    
    
}