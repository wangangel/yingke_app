<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class TradeController extends AdminController{
	protected $autoCheckFields =false;
	/*
     * 评论展示
     */
    public function trade_list() {
        $model_trade = M('trade');        
        //获取总数
        $trade_count = $model_trade->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($trade_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $trade_list = $model_trade->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="search";
        $search = $this->checkAuth($actionName1);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('trade_list',$trade_list);
      	$this->display();
    }
    /**
     * 组合条件筛选
     */
    public function search(){
        $reg_date1 = strtotime($_POST["reg_date"]);
        $reg_date2 = strtotime($_POST["reg_date2"]);
        $seller_phone = $_POST["seller_phone"];
        $trade_name = $_POST["trade_name"];
        $buyers_phone = $_POST["buyers_phone"];
        if($reg_date1 != "" && $reg_date2 !=""){
            $map['trade_date']  = array('between',array($reg_date1,$reg_date2));
            $this->assign('reg_date',$_POST["reg_date"]);
            $this->assign('reg_date2',$_POST["reg_date2"]);
        }  
        if($seller_phone !=""){
            $map["seller_phone"] = $seller_phone;
            $this->assign('seller_phone',$seller_phone);
        }
        if($buyers_phone !=""){
            $map["buyers_phone"] = $buyers_phone;
            $this->assign('buyers_phone',$buyers_phone);
        }
        if($trade_name != ""){
            $map["trade_name"] = array('like','%'.$trade_name.'%');
            $this->assign('trade_name',$trade_name);
        }
        $model_trade = M("trade");
        //获取总数
        $trade_count = $model_trade->where($map)->count();
        //倒入分页类
        import('Think.Page');
        $page_class = new Page($trade_count,15);
        $page_class->setConfig('prev', '«');
        $page_class->setConfig('next', '»');
        $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
        $page = $page_class->show();
        //获取列表
        $trade_list = $model_trade->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $actionName1["auth_a"]="search";
        $search = $this->checkAuth($actionName1);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('trade_list',$trade_list);
        $this->display("Trade/trade_list");
    }



    
}