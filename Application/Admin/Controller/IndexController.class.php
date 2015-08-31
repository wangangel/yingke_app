<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
class IndexController extends  AdminController{
    
    public function left(){
        //获得当前管理员的权限信息
        //① 获得管理员的记录信息
        $data["id"] = $_SESSION['admin_id'];
        $user =  M('account')->where($data)->find();
        $role_id = $user['role']; //角色id信息
        //② 获得角色记录信息
        $roledata['role_id'] = $role_id;
        $roledata['role_status'] = 1;
        $role = M('role')->where($roledata)->select();
        $auth_ids = $role[0]['role_auth_ids'];
        //③ 获得对应的权限信息
        
        if($_SESSION['admin_name']=='admin'){
            //超级管理员admin获得全部的权限
            $auth_infoA = M('auth')->where("auth_level=0 and is_menu = 1")->order('auth_order asc')->select(); //顶级
            $auth_infoB = M('auth')->where("auth_level=1 and is_menu = 1")->order('auth_order asc')->select(); //次顶  
        }elseif($auth_ids == null){
           //没有分配角色
            $auth_infoA = '';
            $auth_infoB = '';
            echo "请联系管理员给您授权！";

        }else{
            $auth_infoA = M('auth')->where("auth_level=0 and is_menu = 1 and auth_id in ($auth_ids)")->order('auth_order asc')->select(); //顶级
            $auth_infoB = M('auth')->where("auth_level=1 and is_menu = 1 and auth_id in ($auth_ids)")->order('auth_order asc')->select(); //次顶 
        }
        $this -> assign('auth_infoA',$auth_infoA);
        $this -> assign('auth_infoB',$auth_infoB);
        $this->display();
    }

    public function index(){
        $this->display();
    }
    /*public function sidebar(){
        $this->display();
    }*/
    /**
     * [top description]获得顶部的权限
     * @return [type] [description]
     */
    public function top(){
       /* $actionName1["auth_a"]="servers_list";
        $conName1["auth_c"] = "Servers";
        $servers_list = $this->getAuth($actionName1,$conName1);
        $actionName2["auth_a"]="order_list";
        $conName2["auth_c"] = "Order";
        $order_list = $this->getAuth($actionName2,$conName2);
        $actionName3["auth_a"]="content_list";
        $conName3["auth_c"] = "Content";
        $content_list = $this->getAuth($actionName3,$conName3);
        $actionName4["auth_a"]="update";
        $conName4["auth_c"] = "System";
        $update = $this->getAuth($actionName4,$conName4);
        $this->assign('servers_list',$servers_list);
        $this->assign('order_list',$order_list);
        $this->assign('content_list',$content_list);
        $this->assign('update',$update);*/
        $this->display();
    }
    public function footer(){
        $this->display();
    }
    /**
     * [getAuth description]获取权限
     * @param  [type] $auth_aname [description]
     * @param  [type] $auth_cname [description]
     * @return [type]             [description]
     */
    public function getAuth($auth_aname,$auth_cname){
        $auth = M("auth")->where($auth_cname)->where($auth_aname)->select();
        return $auth[0]["auth_id"];
    }
}