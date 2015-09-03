
<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
class AccountController extends AdminController{
    //管理员用户
    /*
     * 管理员展示
     */
    public function account_list() {
        $model_user = M('account');
        $arr = array();
        $arr['status'] = 'start';
        //获取总数
        $admin_count = $model_user->where($arr)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($admin_count,8);
        $page_class->setConfig('prev', '<<');
        $page_class->setConfig('next', '>>');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $admin_list = $model_user->where($arr)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //为权限加上
        $actionName1["auth_a"]="admin_add_show";
        $admin_add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="admin_edit_show";
        $admin_edit_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="admin_del";
        $admin_del = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="admin_role_show";
        $admin_role_show = $this->checkAuth($actionName4);

        $this->assign('admin_add_show',$admin_add_show);
        $this->assign('admin_edit_show',$admin_edit_show);
        $this->assign('admin_del',$admin_del);
        $this->assign('admin_role_show',$admin_role_show);
        $this->assign('page',$page);
        $this->assign('admin_list',$admin_list);
        $this->display();
    }
    /*
     * 管理员添加展示
     */
    public function account_add_show(){
        $this->display();
    }
    /*
     * 管理员添加动作
     */
    public function account_add(){
        $arr = array();
        $arr['username'] = $_POST['username'];
        $arr['attribute'] = 0;
        $arr['isdelete'] = 0;
        $model_admin = M('user');
        $result = $model_admin->where($arr)->find();
        if($result){
            $this->error('已存在该用户',U("admin/member/admin_add_show"));
        }
        $_POST['password'] = md5($_POST['password']);
        $_POST['attribute'] = 0;
        $_POST['isdelete'] = 0;
        $result = $model_admin->add($_POST);
        if($result){
            $this->success('添加成功',U("admin/member/admin_list"));
        }else{
            $this->error('添加失败',U("admin/member/admin_add_show"));
        }
    }
    /*
     * 管理员修改页面展示
     */
    public function account_edit_show(){
        $array = array();
        $array['id'] = $_GET['id'];
        $model_admin = M('user');
        $admin_info = $model_admin->where(array('id'=>$_GET['id']))->find();

        $this->assign('admin_info',$admin_info);
        $this->display();
    }
    /*
     * 管理员信息修改
     */
    public function account_edit(){
        $model_admin = M('user');
        $_POST['password'] = md5($_POST['password']);
        $result = $model_admin->save($_POST);
        if($result){
            $this->success('修改成功',U("admin/member/admin_list"));
        }else{
            $this->error('修改失败',"/anjuyi/index.php?m=admin&c=member&a=admin_edit_show&id=" . $_POST['admin_id']);
        }
    }
    /*
     * 管理员删除
     */
    public function account_del(){
        $model_admin = M('user');
        if($_GET['id'] == 1){
            $this->error('该账号无法删除！',U("admin/member/admin_list"));
            die;
        }
        $result = $model_admin->where(array('id'=>$_GET['id']))->save(array("isdelete"=>"1"));
        if($result){
            $this->success('操作成功！',U("admin/member/admin_list"));
        }else{
            $this->error('操作失败',U("admin/member/admin_list"));
        }
    }
   

    /*
     * 普通用户展示
     */
    public function member_list(){
        $model_member = M('user');
        $arr = array();
        $arr['attribute'] = 2;
        $arr['isdelete'] = 0;
        $member_count = $model_member->where($arr)->count();
        import('Think.Page');
        $page_class = new Page($member_count,10);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        
        $page = $page_class->show();
        $member_list = $model_member->where($arr)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        
        //为权限加上
        $actionName1["auth_a"]="member_del";
        $member_del = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="member_edit_show";
        $member_edit_show = $this->checkAuth($actionName2);

        $this->assign('member_del',$member_del);
        $this->assign('member_edit_show',$member_edit_show);
        $this->assign('page',$page);
        $this->assign('member_list',$member_list);
        $this->display();
    }
    /*
     * 普通用户删除 isdelete=1
     */
    public function member_del(){
        $model_member = M('user');
        $result = $model_member->where(array('id'=>$_GET['id']))->save(array("isdelete"=>"1"));
        if($result){
            $this->success('操作成功！',U("admin/member/member_list"));
        }else{
            $this->error('操作失败',U("admin/member/member_list"));
        }
    }
    /*
     * 普通用户修该展示
     */
    public function member_edit_show(){
        $array = array();
        $array['id'] = $_GET['id'];
        $model_member = M('user');
        $member_info = $model_member->where(array('id'=>$_GET['id']))->find();
        $this->assign('member_info',$member_info);
        $this->display();
    }
    /*
     * 用户修改
     */
    public function member_edit(){
        $model_member = M('user');
        $_POST['password'] = md5($_POST['password']);
        $result = $model_member->save($_POST);
        if($result){
            $this->success('修改成功',U("admin/member/member_list"));
        }else{
            $this->error('修改失败','admin/member/member_edit_show&id=' . $_POST['id']);
        }
    }
   
    /**
     * 授予角色前
     */
    public function account_role_show(){
        $member['id'] = $_GET['id'];
        $memberInfo = M("user")->where($member)->find();
        $roleInfo = M("role")->select();
        $this->assign('admin_info',$memberInfo);
        $this->assign('roleInfo',$roleInfo);
        $this->display();

    }

    /**
     * 正式授予角色
     */
    public function account_role(){
        $user['id'] = $_POST['id'];
        $user['roleid'] = $_POST['roleid'];
        $res = M('user')->save($user);
        if($res){
            $this->success('操作成功！',U("admin/member/admin_list"));
        }else{
            $this->error('操作失败！',U("admin/member/admin_list"));
        }
    }
    
}