<?php
//命名空间
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
use Think\Controller;

class AuthController extends AdminController{
    //列表展示
    public function auth_list(){
        $data['auth_level'] = array('gt' => 0,);
        //获得权限的全部信息
        //$info = M('auth')->where($data)->order('auth_path asc')->select();
        //倒入分页类
        $auth_count = M('auth')->count();
        import('Think.Page');
        $page_class = new Page($auth_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();

        $info = M('auth')->limit($page_class->firstRow.','.$page_class->listRows)->order('auth_path asc')->select();

        //为权限加上
        $actionName1["auth_a"]="auth_add_show";
        $auth_add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="auth_edit_show";
        $auth_edit_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="auth_del";
        $auth_del = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="auth_search";
        $auth_search = $this->checkAuth($actionName4);

        $this->assign('auth_add_show',$auth_add_show);
        $this->assign('auth_edit_show',$auth_edit_show);
        $this->assign('auth_del',$auth_del);
        $this->assign('auth_search',$auth_search);
        $this->assign('page',$page);
        $this->assign('info',$info);
        $this->display();
    }
    //添加展示
    public function auth_add_show(){
        //循环出所有的权限名称
        $info = M('auth')->where("auth_level = 0")->select();
        $this -> assign('info',$info);
        $this->display();
    }
    //添加权限
    public function auth_add(){
        //两个逻辑(展示、收集)
        if(!empty($_POST)){

            $rst = $this -> saveInfo($_POST);
            if($rst){
                $this ->success('添加权限成功!',U("admin/auth/auth_list"));
            }else {
                $this ->success('添加权限失败!',U("admin/auth/auth_list"));
            }
        } 
    }
    
    //编辑展示
    public function auth_edit_show(){
        $data["auth_id"] = $_GET["id"];
        $authinfo = M('auth')->where($data)->find();
        $info = M('auth')->where("auth_level = 0")->select();
        $this->assign('authinfo',$authinfo);
        $this->assign('info',$info);
        $this->display();
    }
  
    //更新权限
    public function auth_update(){
        $auth_id = $_POST['auth_id'];
        $is_menu = $_POST['is_menu'];
        $data["auth_id"] = $_POST['auth_id'];
        $data["auth_name"] = $_POST['auth_name'];
        $data["auth_c"] = $_POST['auth_c'];
        $data["auth_a"] = $_POST['auth_a'];
        $data["auth_pid"] = $_POST['auth_pid'];
        $data["auth_order"] = $_POST['auth_order'];
        if(!empty($_POST)){
            $rst = M("auth")->save($data);
            //② 处理全路径
            //A 顶级权限
            if($_POST['auth_pid']==0){
                $path = $auth_id ;
            } else {
                 //B 非顶级权限
                //父级全路径-本身id
                $p_info = M('auth')->find($_POST['auth_pid']);//父级权限信息
                $p_path = $p_info['auth_path'];//父级全路径
                $path = $p_path."-".$auth_id ;
            }

            //③ 处理等级
            $level = substr_count($path,'-');
            //④ 更新语句
            $sql = "update base_auth set auth_path='$path',auth_level='$level',is_menu='$is_menu' where auth_id='$auth_id'";
            $rst = M('auth') -> execute($sql);
            $this -> success('更新权限成功!',U("admin/auth/auth_list"));
            /*if($rst){
                $this -> success('更新权限成功!',U("admin/auth/auth_list"));
            }else {
                $this -> error('更新权限失败!',U("admin/auth/auth_list"));
            }*/
        } 
    }
    //添加保存权限信息
    public function saveInfo($info){
        //① 先根据已有信息生成一个新记录insert
        $newid = M('auth') -> add($info);
        //② 处理全路径
        //A 顶级权限
        if($info['auth_pid']==0){
            $path = $newid;
        } else {
            //B 非顶级权限
            //父级全路径-本身id
            $p_info = M('auth')->find($info['auth_pid']);//父级权限信息
            $p_path = $p_info['auth_path'];//父级全路径
            $path = $p_path."-".$newid;
        }
        //③ 处理等级
        $level = substr_count($path,'-');
        //④ 更新语句
        $sql = "update ajy_auth set auth_path='$path',auth_level='$level' where auth_id='$newid'";
        return M('auth') -> execute($sql);
    }

    //删除权限->1、判断子集权限删除->2、判断是否在角色删除???->暂且不考虑
    public function auth_del(){
        $data["auth_pid"] = $_GET["id"];
        $child_auth = M("auth")->where($data)->select();
        if(count($child_auth)>0){
            $this -> error('操作失败!',U("admin/auth/auth_list"));
        }else{
             $res = M("auth")->delete($_GET["id"]);
           if($res){
                $this ->success('操作成功！',U("admin/auth/auth_list"));
           }else{
                $this ->error('操作失败！',U("admin/auth/auth_list"));
           }
        }

    }
    //模糊搜索
    public function auth_search(){
        $auth_name = $_POST["auth_name"];
        $data["auth_name"] = array('like', '%'.$_POST["auth_name"].'%');
        import('Think.Page');
        $page_class = new Page($auth_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        $info = M('auth')->limit($page_class->firstRow.','.$page_class->listRows)->where($data)->order('auth_path asc')->select();
        $this->assign('auth_name',$auth_name);
        $this->assign('page',$page);
        $this->assign('info',$info);
        $this->display();
    }


}

