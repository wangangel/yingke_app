<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       $this->success('正在进入...',U('admin/login/login'));
    }
}