<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Controller;

class LoginController extends AdminController{
    
    /*
     * 登录
     */
    public function login(){
        
        if($_POST){
            if(empty($_POST['username'])||empty($_POST['password'])){
               $this->error('账号或者密码不能为空！'); 
            }else{
                $array['username'] = $_POST['username'];
                $array['password'] = md5($_POST['password']);
                $array['attribute']=0;
                $array['isdel'] = 0;
                $model_user = M('user');
                $user_info = $model_user->where($array)->find();
                //dump($array);
                //$sql = "select * from ajy_user where username = '".$array['username']."' and password = '".$array['password']."'";
                
               // $result = $model_user->query($sql);
              
                if($user_info){
                    $arr = array();
                    $arr['id'] = $user_info['id'];
                    $arr['logintime'] = time();
                    $result = $model_user->save($arr);
                    $_SESSION['admin_name'] = $user_info['username'];
                    $_SESSION['admin_id'] = $user_info['id'];
                    //增添的角色
                    $_SESSION['role_id'] = $user_info['roleid'];
                    $_SESSION['login_time'] = date("Y-m-d H:i:s", time()) ; ;
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