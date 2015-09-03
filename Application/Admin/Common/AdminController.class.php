<?php
namespace Admin\Common;
use Think\Controller;
class AdminController extends Controller{
    var $close = 0; // 0-记录日志, 1-关闭日志系统

    //var $table = "sys_log";

    var $outtime = 30; //过期时间，按天，将自动删除过期日志
    public function __construct(){
        parent::__construct();

        
       $nowac = CONTROLLER_NAME."-".ACTION_NAME;
        //没有对应session信息的用户，禁止访问系统
        //除了后台登录login-login、
        //禁止用户翻墙访问没有的权限
        //获得用户拥有的权限
        //管理员id---->管理员信息(角色id)----角色信息(权限的AC)
        if($_SESSION['admin_id'] == null && $nowac !="Login-login"){
            
            $this->error('请登陆！',U("admin/login/login"));

        }else{
            $manager = M('account')->find($_SESSION['admin_id']);
            $role_id['role_id'] = $manager['role'];
            $role_info = M('role')->where($role_id)->find();
            $role_ac = $role_info['role_auth_ac'];
            $role_auth_ids = $role_info['role_auth_ids'];
            //判断当前请求的控制器-操作方法 是否存在于角色的权限范围内
            //$allowac默认允许访问权限
            $allowac = "Login-logout,Login-login,Index-top,Index-left,Index-footer,Index-main,Index-index";
            //var_dump(strpos($role_ac,$nowac));
            //① 判断本身是否拥有此权限
            //② 判断访问的是否是默认允许权限
            //③ 判断是否是超级管理员
            if(strpos($role_ac,$nowac)===false && strpos($allowac,$nowac)===false 
                    && $_SESSION['admin_name']!='admin'){
                $this->error('没有访问权限,若有需要请联系管理员！',U("admin/index/index"));
            }
            
            $this->assign('hava_authids',$role_auth_ids);
            
        }
        
    }
    //查询功能id
    public function checkAuth($actionName){
        $nowac = CONTROLLER_NAME;
        $data["auth_c"] = $nowac;
        $auth = M("auth")->where($data)->where($actionName)->select();
        return $auth[0]["auth_id"];
    }

    // 添加一条操作日志
    // $type为操作类型，$title操作说明，$viewurl查看url，$username操作人
    // 
    public function log_add($type, $title, $viewurl, $username) {
        
        return $this->add($type, $title, $viewurl, $username);
    }

    /**
     * [add description]添加日志
     * @param [type] $type     [description]
     * @param [type] $title    [description]
     * @param [type] $viewurl  [description]
     * @param [type] $username [description]
     */
    public function add($type, $title, $viewurl, $username) {

        if ($this->close || $debug_mode) return false;

        $r = array();
        $r["logtype"] = $type;
        $r["title"] = $title;
        $r["viewurl"] = $viewurl;
        $r["username"] = $username;
        $r["ip"] = $this->get_ip();
        $r["addtime"] = time();
        $model_syslog = M('syslog');
        $res = $model_syslog->add($r);
        // 过期日志删除:
        if (mt_rand(1, 1000) <= 10) {

            $this->log_clear();

        }
        return $res;

    }


    public function log_clear() {


        if ($this->outtime > 0) {

            $outtime = strtotime("-".intval($this->outtime)." days");
            $model_syslog = M('syslog');
            $model_syslog ->where('addtime <'. $outtime)->delete();
        }

    }
    // 获取当前用户的ip地址:
    public function get_ip() {
        $long_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        if ($long_ip != "") {
            foreach (explode(",", $long_ip) as $cur_ip) {
                list($ip1, $ip2) = explode(".", $cur_ip, 2);
                if ($ip1 <> "10") {
                    return $cur_ip;
                }
            }
        }
        return $_SERVER["REMOTE_ADDR"];
    }
    /**
     * 批量删除
     */
     public function del_all(){
        $nowac = CONTROLLER_NAME;
        $ids = $_GET['ids'];
        $action = $nowac."_list";
        /*$result = M("$nowac")->delete($ids);
        if($result){
            $this->success('操作成功！',U("admin/$nowac/$action"));
        }else{
            $this->error('操作失败',U("admin/$nowac/$nowac_list"));
        }*/

        $this->success('操作成功！',U("admin/$nowac/$action"));
    }

}