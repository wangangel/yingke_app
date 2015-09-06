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
        $this->assign('page',$page);
        $this->assign('trade_list',$trade_list);
      	$this->display();
    }
    
}