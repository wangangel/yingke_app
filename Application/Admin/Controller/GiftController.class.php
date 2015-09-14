<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;

class GiftController extends AdminController{

	public function user_gift_list(){
                $data['gift_sign'] = 'user';
        	$gift_model = M('gift');        
                //获取总数
                $gift_count = $gift_model->where($data)->count();
                //倒入分页类
                import('Think.Page');
                $page_class = new Page($gift_count,15);
                $page_class->setConfig('prev', '«');
                $page_class->setConfig('next', '»');
                $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
                $page = $page_class->show();

                //为权限加上
                $actionName1["auth_a"]="user_gift_list";
                $user_gift_list = $this->checkAuth($actionName1);
                $actionName2["auth_a"]="gift_status_set";
                $gift_status_set = $this->checkAuth($actionName2);

        	$gift_info = $gift_model -> where($data) ->select();
                $this->assign('user_gift_list',$user_gift_list);
        	$this->assign('gift_status_set',$gift_status_set);
        	$this->assign('page',$page);
        	$this->assign('gift_info',$gift_info);
        	$this->display("Gift/user_gift_list");
	}


        /**
         * [gift_status_set 选择启用礼物,还是停用礼物]
         * @return [type] [description]
         */
        public function gift_status_set(){
                $data['id'] = $_REQUEST['id'];
                $data['status'] = $_REQUEST['status'];
                $model = M('gift');
                $info = $model ->save($data);
                $this->ajaxReturn($info,'JSON');
        }

        /**
         * [search 礼物搜索]
         * @return [type] [description]
         */
        public function search(){
                $reg_date1 = strtotime($_POST["reg_date"]);
                $reg_date2 = strtotime($_POST["reg_date2"]);
                $gift_name = $_POST['gift_name'];
                if($reg_date1 != "" && $reg_date2 !=""){
                    $map['add_date']  = array('between',array($reg_date1,$reg_date2));
                    $this->assign('reg_date',$_POST["reg_date"]);
                    $this->assign('reg_date2',$_POST["reg_date2"]);
                } 
                if($gift_name !=""){
                    $map["gift_name"] = array('like','%'.$gift_name.'%');
                    $this->assign('gift_name',$gift_name);
                } 
                $model = M('gift');
                $gift_count = $model->where($map)->count();
                //导入分页类
                import('Think.Page');
                $page_class = new Page($report_count,15);
                $page_class->setConfig('prev', '«');
                $page_class->setConfig('next', '»');
                $page_class->setConfig('theme', '<div class="pagin"><ul class="paginList"><li class="paginItem">%UP_PAGE%</li><li class="paginItem">%LINK_PAGE%</li><li class="paginItem">%DOWN_PAGE%</a></li></ul></div>');
                $page = $page_class->show();
                $gift_info = $model->where($map)->limit($page_class->firstRow.','.$page_class->listRows)->select();
                //为权限加上
                $actionName1["auth_a"]="user_gift_list";
                $user_gift_list = $this->checkAuth($actionName1);
                $actionName2["auth_a"]="gift_status_set";
                $gift_status_set = $this->checkAuth($actionName2);
                $actionName3["auth_a"]="search";
                $search = $this->checkAuth($actionName3);
                $actionName4["auth_a"]="edit_gift_save";
                $edit_gift_save = $this->checkAuth($actionName4);

                $this->assign('user_gift_list',$user_gift_list);
                $this->assign('gift_status_set',$gift_status_set);
                $this->assign('search',$search);
                $this->assign('page',$page);
                $this->assign('gift_info',$gift_info);
                $this->display("Gift/user_gift_list");

        }

        /**
         * [edit_gift 编辑礼物,并查询出礼物相关参数]
         * @return [type] [description]
         */
        public function edit_gift(){
                $data['id'] = $_REQUEST['id'];
                $model = M('gift');
                $info = $model ->where($data)->find();
                $this ->ajaxReturn($info,"JSON");
        }
        public function edit_gift_save(){
                //根据是否修改图片判断
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     3145728 ;// 设置附件上传大小
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
                $upload->savePath  = '';
                // 上传文件 
                $info   =   $upload->uploadOne($_FILES['upload']);

                        if(!$info) {// 上传错误提示错误信息
                        $this->error($upload->getError());
                         }else{// 上传成功
                         $data['gift_pic_url'] = C("WEB_URL")."/Upload/".$info['savepath'].$info['savename']; 
                        }
                $data['gift_name'] = $_REQUEST['edit_gift'];
                $data['gift_price'] = $_REQUEST['edit_gift_price'];
                $data['gift_description'] = $_REQUEST['edit_gift_depc'];
                $data['userphone'] = $_REQUEST['editphone'];
                $data['gift_sales'] = $_REQUEST['sales'];
                $data['id'] = $_REQUEST['edit_id'];
                $data['add_date'] = time();
                $info = M('gift') ->save($data);
                if($info){
                $this->success('修改成功!',U('admin/gift/user_gift_list'));
                }


        }

}