<?php
//命名空间
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
use Think\Controller;

class RoleController extends AdminController{
     //列表展示
    public function role_list(){
       
        $role_count = M('role')->count();
        import('Think.Page');
        $page_class = new Page($role_count,8);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        $info = M('role')->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="role_add_show";
        $role_add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="role_edit_show";
        $role_edit_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="role_del";
        $role_del = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="distribute";
        $distribute = $this->checkAuth($actionName4);

        $this->assign('role_add_show',$role_add_show);
        $this->assign('role_edit_show',$role_edit_show);
        $this->assign('role_del',$role_del);
        $this->assign('distribute',$distribute);
        
        $this->assign('page',$page);
        $this -> assign('info', $info);
        $this -> display();
    }
    //添加角色展示
    public function role_add_show(){
        $this->display();
    }
    //保存角色
    public function role_add(){
        $data['role_name'] = $_POST["role_name"];
        $data["role_status"] = $_POST["role_status"];
        $data["role_remark"] = $_POST["role_remark"];
        $data["role_time"] = date("Y-m-d H:i:s");
        $rst = M("role")->add($data);
        if($rst){
            $this -> success('添加角色成功!',U("admin/role/role_list"));
        }else {
            $this -> error('添加角色失败!',U("admin/role/role_add_show"));
        }

    }
    //编辑角色
    public function role_edit_show(){
        $role_id = $_GET["id"];
        $role_info = M("role") ->find($role_id);
        $this -> assign('role_info', $role_info);
        $this -> display();
    }
    //更新角色
    public function role_update(){
        $res = M("role") ->save($_POST);
        if($res){
            $this -> success('更新操作成功!',U("admin/role/role_list"));
        }else{
            $this -> error('更新操作失败!',U("admin/role/role_list"));
        }
    }

    //分配权限
    public function distribute(){
            //两个逻辑：展示表单、收集表单
            $role_id = $_GET["id"];
            //获得被分配权限的角色信息
            $role_info = M("role") ->find($role_id);
            
            //获得已经拥有的权限信息并转化为数组
            $have_auth_arr = explode(',',$role_info['role_auth_ids']);
            $this -> assign('have_auth_arr',$have_auth_arr);
            
            //获得可以被分配的权限信息
            $auth_infoA = M('auth')->where('auth_level=0')->select();
            $auth_infoB = M('auth')->where('auth_level=1')->select();
            $this -> assign('auth_infoA',$auth_infoA);
            $this -> assign('auth_infoB',$auth_infoB);
            $this -> assign('role_info',$role_info);
            $this -> display();
      
    }
    /**授予角色
     * [distribute_role description]
     * @return [type] [description]
     */
    public function distribute_role(){
         if(!empty($_POST)){

                $rst = $this->saveAuth($_POST['authids'],$_POST['role_id']);
            if($rst){
                $this -> success('分配权限成功！',U("admin/role/role_list"));
            }else{
                $this -> success('权限已分配！',U("admin/role/role_list"));
            }
        } 
    }
    //存储权限信息
    public function saveAuth($authinfo, $roleid){
        //② 获得权限对应的记录信息
        $auth = M('auth')->select($authinfo);

        //③ 从权限信息里边获得“控制器-操作方法”信息
        $s = "";
        foreach($auth as $v){
            if(!empty($v['auth_c']) && $v['auth_a']){
                $s .= $v['auth_c']."-".$v['auth_a'].",";
            }
        }
        $s = rtrim($s,',');
        //echo $s;//Goods-showlist,Goods-tianjia,Goods-brand
        //④ 更新角色信息
        $sql = "update yk_role set role_auth_ids='$authinfo',role_auth_ac='$s' where role_id='$roleid'";
        return M("role") -> execute($sql);
    }

    /**
     * 删除权限
     */
    public function role_del(){
        $date['roleid'] = $_GET["id"];
        $redata = M("user")->where($date)->select();
        if(count($redata)>0){
            //还有用户在使用此角色
            $this ->error('此角色仍在使用，请确认用户重新授权！',U("admin/role/role_list"));
        }else{
           $res = M("role")->delete($_GET["id"]);
           if($res){
                $this ->success('操作成功！',U("admin/role/role_list"));
           }else{
                $this ->error('操作失败！',U("admin/role/role_list"));
           }
        }
    }
}

