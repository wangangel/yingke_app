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
        $page_class->setConfig('theme', '<div class="am-cf">%HEADER% <div class="am-fr"><ul class="am-pagination"><li class="am-disabled">%UP_PAGE%</li><li>%FIRST%</li> %LINK_PAGE% <li>%END%<li> <li>%DOWN_PAGE%</li></ul></div></div>');
        $page = $page_class->show();
        //获取列表
        $trade_list = $model_trade->limit($page_class->firstRow.','.$page_class->listRows)->select();
         //为权限加上
        $this->assign('page',$page);
        $this->assign('trade_list',$trade_list);
      	$this->display();
    }
    
}