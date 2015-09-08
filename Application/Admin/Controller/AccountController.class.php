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
        $page_class = new Page($admin_count,15);
        $page_class->setConfig('prev', '<<');
        $page_class->setConfig('next', '>>');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $admin_list = $model_user->where($arr)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        //查询角色名称
        $rolelist = array();
        $role_model = M("role");
        for ($i=0; $i < count($admin_list) ; $i++) { 
            $data["role_id"] = $admin_list[$i]["role"];
            $rolelist[$i] = $role_model->where($data)->find();
        }
        //为权限加上
        $actionName1["auth_a"]="account_add_show";
        $account_add_show = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="account_edit_show";
        $account_edit_show = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="account_del";
        $account_del = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="account_role_show";
        $account_role_show = $this->checkAuth($actionName4);
        $this->assign('rolelist',$rolelist);
        $this->assign('account_add_show',$account_add_show);
        $this->assign('account_edit_show',$account_edit_show);
        $this->assign('account_del',$account_del);
        $this->assign('account_role_show',$account_role_show);
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
     * 账户添加动作
     */
    public function account_add(){
        $arr = array();
        $arr['account'] = $_POST['account'];
        $arr['password'] = md5($_POST['password']);
        $model_account = M('account');
        $result = $model_account->where($arr)->find();
        if($result){
            $this->error('已存在该账户',U("admin/account/account_add_show"));
        }
        $arr['password'] = md5($_POST['password']);
        $arr['employee'] = $_POST['employee'];
        $arr['add_person'] = $_POST['add_person'];
        $arr['status'] = $_POST['status'];
        $arr['add_date'] = time();
        $result = $model_account->add($arr);
        if($result){
            $this->success('添加成功',U("admin/account/account_list"));
        }else{
            $this->error('添加失败',U("admin/account/account_add_show"));
        }
    }
    /*
     * 管理员修改页面展示
     */
    public function account_edit_show(){
        $array = array();
        $array['id'] = $_GET['id'];
        $model_account = M('account');
        $account_info = $model_account->where(array('id'=>$_GET['id']))->find();
        $this->assign('account_info',$account_info);
        $this->display();
    }
    /*
     * 管理员信息修改
     */
    public function account_edit(){
        $model_account = M('account');
        $arr['id'] = $_POST['id'];
        $arr['password'] = md5($_POST['password']);
        $arr['employee'] = $_POST['employee'];
        $arr['add_person'] = $_POST['add_person'];
        $arr['status'] = $_POST['status'];
        $arr['add_date'] = time();
        $result = $model_account->save($arr);
        if($result){
            $this->success('修改成功',U("admin/account/account_list"));
        }else{
            $this->error('修改失败',"/anjuyi/index.php?m=admin&c=account&a=account_edit_show&id=" . $_POST['id']);
        }
    }
    /*
     * 管理员删除
     */
    public function account_del(){
        $model_account = M('account');
        if($_GET['id'] == 1){
            $this->error('该账号无法删除！',U("admin/account/account_list"));
            die;
        }
        $result = $model_account->where(array('id'=>$_GET['id']))->delete();
        if($result){
            $this->success('操作成功！',U("admin/account/account_list"));
        }else{
            $this->error('操作失败',U("admin/account/account_list"));
        }
    }
    /*
     * 当前推荐_搜索当天推荐的信息 以及历史推荐
     */
    public function newrecomment_list(){
        $model_reco = M('recommend');
        $arr = array();
        if($_GET["flag"] == '1'){
           //这个是历史推荐
           $result = 1;
        }else{
            //获取当前时间 
           $arr['re_date'] = date("Y-m-d",time()); 
           $result = 0;
        }
        $arr['status'] = 'yes';
        $reco_count = $model_reco->where($arr)->count();
        import('Think.Page');
        $page_class = new Page($reco_count,15);
        $page_class->setConfig('prev', '<<');
        $page_class->setConfig('next', '>>');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        $reco_list = $model_reco->where($arr)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        $user_list =  array();
        $friends_list = array();
        $model_user = M("user");
        $model_friends_focus = M("friends_focus");
        for ($i=0; $i < count($reco_list); $i++) { 
            $data["id"] = $reco_list[$i]['user_id'];
            $user_list[$i] = $model_user->where($data)->find();
            $friends["user_id"] = $reco_list[$i]['user_id'];
            $friends_list[$i] = $model_friends_focus->where($friends)->count();
        }
        //给权限加上
        $actionName1["auth_a"]="cancel_recomment";
        $cancel_recomment = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="newrecomment_list";
        $newrecomment_list = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="add_recomment";
        $add_recomment = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="past_search";
        $past_search = $this->checkAuth($actionName4);
        $this->assign('past_search',$past_search);
        $this->assign('add_recomment',$add_recomment);
        $this->assign('newrecomment_list',$newrecomment_list);
        $this->assign('cancel_recomment',$cancel_recomment);
        $this->assign('user_list',$user_list);
        $this->assign('friends_list',$friends_list);
        $this->assign('page',$page);
        $this->assign('reco_list',$reco_list);
        $this->assign('result',$result);
        $this->display();
    }
    /*
     * 取消推荐
     */
    public function cancel_recomment(){
        $model_reco = M('recommend');
        $result = $model_reco->where(array('id'=>$_POST['id']))->save(array("status"=>"no"));
        $this->ajaxReturn($result,"JSON");
    }
    /*
     * 添加推荐
     */
    public function add_recomment(){
        $arr = array();
        $data["phone_num"] = $_POST["phone"];
        $model_user = M("user");
        $user = $model_user->where($data)->find();
        $arr['re_date'] = date("Y-m-d",time());
        $model_reco = M('recommend');
        $member_recoinfo = $model_reco->where($arr)->find();
        $arr["user_id"] = $user["id"];
        $recoinfo = $model_reco->where($arr)->find();
        if($member_recoinfo == null){
            $reco_user = $model_reco->order('id desc')->select();   
            $arr["re_batch"] = $reco_user[0]["re_batch"] + 1;
            $arr["op_user"] = $_SESSION['employee'];
            $arr["status"] = "yes";
            $result = $model_reco->add($arr);
        }else{
            if($recoinfo == null){
                $arr["re_batch"] = $member_recoinfo["re_batch"];
                $arr["op_user"] = $_SESSION['employee'];
                $arr["status"] = "yes";
                $result = $model_reco->add($arr);
            }else{
                $result = 0;
            }
        }
        $this->ajaxReturn($result,"JSON");
    }
    /*
     * 历史推荐——搜索
     */
    public function past_search(){
        $model_recommend = M('recommend');
        $data["phone_num"] = $_POST["phone_num"];
        $model_user = M("user");
        $user = $model_user->where($data)->select();
        $arr["user_id"] = $user[0]["id"];
        $arr["status"] = "yes";
        $reco_count = $model_recommend->where($arr)->count();
        import('Think.Page');
        $page_class = new Page($reco_count,15);
        $page_class->setConfig('prev', '<<');
        $page_class->setConfig('next', '>>');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        $reco_list = $model_recommend->where($arr)->limit($page_class->firstRow.','.$page_class->listRows)->select();
        $model_friends_focus = M("friends_focus");
        $friends["user_id"] = $user[0]["id"];;
        $friends_list = $model_friends_focus->where($friends)->count();

         //给权限加上
        $actionName1["auth_a"]="cancel_recomment";
        $cancel_recomment = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="newrecomment_list";
        $newrecomment_list = $this->checkAuth($actionName2);
        $actionName3["auth_a"]="add_recomment";
        $add_recomment = $this->checkAuth($actionName3);
        $actionName4["auth_a"]="past_search";
        $past_search = $this->checkAuth($actionName4);
        $this->assign('past_search',$past_search);
        $this->assign('add_recomment',$add_recomment);
        $this->assign('newrecomment_list',$newrecomment_list);
        $this->assign('cancel_recomment',$cancel_recomment);
        $this->assign('user_list',$user);
        $this->assign('friends_list',$friends_list);
        $this->assign('reco_list',$reco_list);
        $result = 1;
        $this->assign('result',$result);
        $flag="2";//是搜索
        $this->assign('flag',$flag);
        $this->display("Account/newrecomment_list");
    }
    /**
     * 授予角色前
     */
    public function account_role_show(){
        $member['id'] = $_GET['id'];
        $memberInfo = M("account")->where($member)->find();
        $roleInfo = M("role")->select();
        $this->assign('account_info',$memberInfo);
        $this->assign('roleInfo',$roleInfo);
        $this->display();
    }
    /**
     * 正式授予角色
     */
    public function account_role(){
        $account['id'] = $_POST['id'];
        $account['role'] = $_POST['role'];
        $res = M('account')->save($account);
        if($res){
            $this->success('操作成功！',U("admin/account/account_list"));
        }else{
            $this->error('操作失败！',U("admin/account/account_list"));
        }
    }
    /**
     * 修改密码
     */
    public function account_setpass(){
        $data["id"] = $_POST['id'];
        $data["password"] = md5($_POST['password']);

        $account_model = M('account');
        $account = $account_model->where($data)->find();
        $res = "";
        if($account){
            $data["password"] = md5($_POST["confirm_pass"]);
            $res = $account_model->save($data);
        }else{
           $res = 2; //原始密码错误!
        }
        $this->ajaxReturn($res,"JSON");
    }
}