<?php
namespace Api\Controller;
use Api\Common\MobileController;
use Think\Upload;
class IndexController extends MobileController{
    public function __construct(){
        parent::__construct();
    }


    /*
     * 首页优惠
     */
    public function coupon(){
        //取几条优惠信息展示，默认6条
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):6;
        $model_coupon   = M('coupon');
        $result = $model_coupon->where(array('status'=>0))->order('id desc')->limit($arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有优惠信息');
        }else{
            foreach ($result as $k => $v) {
                        $data[$k]['id'] =  $v['id'];
                        $data[$k]['picurl'] =  $v['picurl'];
                        $data[$k]['price'] =  $v['price'];
                        $data[$k]['detail'] =  $v['detail'];
                        $data[$k]['createtime'] =  $v['createtime'];
                        $data[$k]['attr'] =  $v['attr'];
               
            }
            output_data($data);
        }
        
    }




    /*
     * 好评服务
     */
    public function goodcomment_service(){
        //取几条优惠信息展示，默认4条
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):4;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $goods_model = M('servergoods');
        $result = $goods_model->where(array('attr'=>0))->order('haoping_num desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有服务信息');
        }else{
            foreach ($result as $k => $v) {
                //每一个服务还要去一条好评数据展示
                $goodscomment_model = M("goodscomment");
                $arr = array();
                $arr['goodsid'] = $v['id'];
                $arr['grade'] = 2;
                $goodscomment_info = $goodscomment_model->where($arr)->limit(0,1)->select();
                $data['content'][$k]['haopinginfo']['commentid'] = $goodscomment_info[0]['id'];
                $data['content'][$k]['haopinginfo']['goodsid'] = $goodscomment_info[0]['goodsid'];
                $data['content'][$k]['haopinginfo']['userid'] = $goodscomment_info[0]['userid'];
                $data['content'][$k]['haopinginfo']['content'] = $goodscomment_info[0]['content'];
                $data['content'][$k]['haopinginfo']['comefrom'] = $goodscomment_info[0]['comefrom'];
                $data['content'][$k]['haopinginfo']['replycount'] = $goodscomment_info[0]['replycount'];
                $data['content'][$k]['haopinginfo']['prasiecount'] = $goodscomment_info[0]['prasiecount'];
                $data['content'][$k]['haopinginfo']['grade'] = $goodscomment_info[0]['grade'];
                $data['content'][$k]['haopinginfo']['com_picurl'] = $goodscomment_info[0]['picurl'];
                $data['content'][$k]['haopinginfo']['commenttime'] = $goodscomment_info[0]['commenttime'];
                $data['content'][$k]['haopinginfo']['star_num'] = $goodscomment_info[0]['star_num'];
                //根据userid来获取用户信息
                $user_model = M("user");
                $user_info = $user_model->where(array('userid'=>$goodscomment_info[0]['userid']))->find();
                $data['content'][$k]['userinfo']['userid'] = $user_info['id'];
                $data['content'][$k]['userinfo']['username'] = $user_info['username'];
                $data['content'][$k]['userinfo']['headurl'] = $user_info['headurl'];

                $data['content'][$k]['goodsid'] = $v['id'];
                $data['content'][$k]['serverid'] = $v['serverid'];
                $data['content'][$k]['servernum'] = $v['servernum'];
                $data['content'][$k]['goodsname'] = $v['goodsname'];
                $data['content'][$k]['faceurl'] =  $v['faceurl'];
                $data['content'][$k]['ceurl'] =  $v['ceurl'];
                $data['content'][$k]['goodsprice'] =  $v['goodsprice1'];
                $data['content'][$k]['anzhuang_price'] =  $v['goodsprice2'];
                $data['content'][$k]['peisong_price'] =  $v['goodsprice3'];
                $data['content'][$k]['chaogao_price'] =  $v['goodsprice4'];
                $data['content'][$k]['chaijiu_price'] =  $v['goodsprice5'];
                $data['content'][$k]['sale_count'] =  $v['sale_count'];
                $data['content'][$k]['comment_num'] =  $v['comment_num'];
                $data['content'][$k]['haoping_num'] =  $v['haoping_num'];
            }
        }
            output_data($data);
    }





    /*
     * 获取图片轮播
     */
    public function banner()
    {
        //取几条图片轮播展示，默认5条
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):5;
        $model_banner   = M('banner');
        $result = $model_banner->where()->order('id desc')->limit($arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有优惠信息');
        }else{
            foreach ($result as $k => $v) {
                        $data[$k]['id'] =  $v['id'];
                        $data[$k]['bannername'] =  $v['bannername'];
                        $data[$k]['bannerlink'] =  $v['bannerlink'];
                        $data[$k]['picurl'] =  $v['picurl'];
                        $data[$k]['releasetime'] =  $v['releasetime'];
            }
            output_data($data);
        }
       
    }






    /**
     *  接口：对评论信息点赞
     *
     *  @params null
     *  @return xml
     */
    public function dianzan(){
        $arrOpt = array();
        if($_REQUEST['key'] == null || $_REQUEST['userid'] == null ){
            output_error('请先登录！');
        }
        //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $arrOpt['commentid'] = intval($_REQUEST['commentid']);
        $arrOpt['userid'] = intval($_REQUEST['userid']);
        $arrOpt['isdel'] = 0;
        $dianzan_model = M('dianzan');
        //先查询是否已经点过赞了
        $result = $dianzan_model->where($arrOpt)->select();
        if($result[0] == NULL){
            //没有点过赞
            $res = $dianzan_model->add($arrOpt);
            if($res){
                $data=array();
                $data['id'] = $res;
                output_data($data);
            }else{
                output_error('点赞失败');
            }
        }else{
            //output_error('已经点过赞了');
            $data=array();
            $data['id'] = "-1";
            output_data($data);
        }
    }




    /*
     *根据商品id获取评论列表
     */
    public function comment_list(){
        if($_REQUEST['goodsid'] == null){
            output_error('参数不全');
        }
        $arrOpt = array();
        $arrOpt['goodsid'] = intval($_REQUEST['goodsid']);
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):10;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $comment_model = M('goodscomment');
        $result = $comment_model->where(array('goodsid'=>$arrOpt['goodsid']))->order('id desc')->limit($start,$arrOpt['ps'])->select();
        $data = array();
        if($result[0] == NULL){
            output_error('没有评论信息');
        }else{
            $goodcomment_num = 0;
            $badcomment_num = 0;
            $zhongpingcomment_num = 0;
            $piccomment_num = 0;
            foreach ($result as $k => $v) {
                //根据用户id获取用户信息
                $user_model = M("user");
                $user_info = $user_model->where(array('userid'=>$v['userid']))->find();
                $data['content'][$k]['userinfo']['userid'] = $user_info['id'];
                $data['content'][$k]['userinfo']['username'] = $user_info['username'];
                $data['content'][$k]['userinfo']['headurl'] = $user_info['headurl'];

                $data['content'][$k]['id'] = $v['id'];
                $data['content'][$k]['content'] = $v['content'];
                $data['content'][$k]['comefrom'] = $v['comefrom'];
                $data['content'][$k]['serverlist'] = $v['serverlist'];
                $data['content'][$k]['replycount'] =  $v['replycount'];
                $data['content'][$k]['prasiecount'] =  $v['prasiecount'];
                $data['content'][$k]['grade'] =  $v['grade'];
                if($v['grade'] == 0 ){
                    $badcomment_num += 1;
                }else if($v['grade'] == 1){
                    $zhongpingcomment_num += 1;
                }else if($v['grade'] == 2){
                    $goodcomment_num += 1;
                }else if($v['picurl'] != null){
                    $piccomment_num +=1;
                }
                
                $data['content'][$k]['picurl'] =  $v['picurl'];
                $data['content'][$k]['commenttime'] =  $v['commenttime'];
                $data['content'][$k]['star_num'] =  $v['star_num'];

            }
            $data['badcomment_num'] = $badcomment_num;
            $data['zhongpingcomment_num'] = $zhongpingcomment_num;
            $data['goodcomment_num'] = $goodcomment_num;
            $data['piccomment_num'] =  $piccomment_num;
            output_data($data);
        }
    }
   



    /*
     *上传图片
     */

    public function add_pic(){
        $arrOpt = array();
        $arrOpt['photo'] = $_FILES['photo'];
        $arrOpt['userid'] = $_REQUEST['userid'];
        $arrOpt['token'] = $_REQUEST['key'];
        if( $_REQUEST['userid'] == NULL || $_REQUEST['key'] == NULL){
            output_error('请先登录');
        }
        //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        if($_FILES['photo'] == NULL ){
            output_error('参数不全');
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Upload/'; // 设置附件上传根目录
     
        //判断是否已经选择图片
        if(!empty($_FILES['photo']['tmp_name'])){
            //echo'已选择文件';
            // 上传单个文件 
            $info1 = $upload->uploadOne($_FILES['photo']);
            if(!$info1) {
                // 上传错误提示错误信息
                $str = $upload->getError();
                output_error($str);
            }
            //这里是设置文件的url注意使用.不是+  
            $imgurl1 = $info1['savepath'].$info1['savename'];
            output_data(array('picurl'=>$imgurl1));
            
        }else{
             output_error("未选择图片上传");
        }
       
       
    }





    /*
     *发布评论
     */

    public function add_comment(){
        if($_REQUEST['userid']==NULL || $_REQUEST['key']==NULL){
            output_error('请先登录');
        }
        if( $_REQUEST['goodsid']==NULL || $_REQUEST['content']==NULL || $_REQUEST['grade']==NULL ){
            output_error('参数不全');
        }
         //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $arrOpt = array();
        $arrOpt['goodsid'] = $_REQUEST['goodsid'];
        $arrOpt['userid'] = $_REQUEST['userid'];
        $arrOpt['content'] = $_REQUEST['content'];
        $arrOpt['grade'] = $_REQUEST['grade'];
        $arrOpt['token'] = $_REQUEST['key'];
        if($_REQUEST['comefrom'] == NULL){
            //如果来源地为空，则默认为来源于北京
            $arrOpt['comefrom'] = "北京";
        }else{
            $arrOpt['comefrom'] = $_REQUEST['comefrom'];
        }
        if($_REQUEST['star_num'] == NULL){
            //如果星级为空，则默认是5星
            $arrOpt['star_num'] = 5;
        }else{
            $arrOpt['star_num'] = $_REQUEST['star_num'];
        }
        $arrOpt['picurl'] = $_REQUEST['picurl'];
        $arrOpt['serverlist'] = $_REQUEST['serverlist'];
        $arrOpt['commenttime'] = time();
        $goodscomment_model = M('goodscomment');
        $result = $goodscomment_model->add($arrOpt);
        if($result>0){
            //评论发布成功，则将对应的商品评论数量加一
            $goods_model = M('servergoods');
            $res = $goods_model->where(array('id'=>$arrOpt['goodsid']))->setInc('comment_num');
            //如果发布的是好评，则对应的商品的好评数量也要加一
            if($_REQUEST['grade'] == "2"){
                $res = $goods_model->where(array('id'=>$arrOpt['goodsid']))->setInc('haoping_num');
            }
            $data=array();
            $data['id'] = $result;
            output_data($data);
        }else{
            output_error("评论发布失败");
        }
        

    }






    /*
     *回复评论
     */

    public function reply_comment(){
        if($_REQUEST['userid']==NULL || $_REQUEST['key']==NULL){
            output_error('请先登录');
        }
        if( $_REQUEST['goodsid']==NULL || $_REQUEST['content']==NULL || $_REQUEST['comment_id']==NULL ){
            output_error('参数不全');
        }
         //验证秘钥是否正确
        $token_model = M('usertoken');
        $arr = array();
        $arr['userid'] = $_REQUEST['userid'];
        $arr['token'] = $_REQUEST['key'];
        $jieguo = $token_model->where($arr)->select();
        if($jieguo[0] == NULL){
             output_error('秘钥key不正确');
        }
        $arrOpt = array();
        $arrOpt['goodsid'] = $_REQUEST['goodsid'];
        $arrOpt['replyuserid'] = $_REQUEST['userid'];
        $arrOpt['replycontent'] = $_REQUEST['content'];
        $arrOpt['commentid'] = $_REQUEST['comment_id'];
        $arrOpt['picurl'] = $_REQUEST['picurl'];
        $arrOpt['replytime'] = time();
        $goodscommentreply_model = M('goodscommentreply');
        $result = $goodscommentreply_model->add($arrOpt);

        if($result){
           //去将对于的评论数据的回复数加1
            $comment_model = M('goodscomment');
            $id = $arrOpt['commentid'];
            $sql="update ajy_goodscomment set replycount=replycount+1 where id=$id";
            $res = $comment_model->execute($sql);
            if($res){
                $data =array();
                $data['id'] = $result;
               output_data($data);
            }
            else{
                output_error("评论回复数增加失败");
            }
        }else{
            output_error("评论回复失败");
         }
        

    }





    /*
     *城市库房
     */
    public function city_kufang(){
        $arrOpt = array();
        $arrOpt['ps'] = intval($_REQUEST['ps'])>0?intval($_REQUEST['ps']):4;
        $arrOpt['page'] = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
        $start = ($arrOpt['page']-1)*$arrOpt['ps'];
        $kufang_model = M('kufan');
        $result = $kufang_model->where()->order('id desc')->limit($start,$arrOpt['ps'])->select();

        if($result[0] == NULL){
            //则说明没有库房数据
            output_error("暂时还没有库房哦");
        }else{
            $data = array();
            foreach ($result as $k => $v) {
                
                //根据areaid去查询省市信息
                $area_model = M('area');
                $res = $area_model->where(array('id'=>$v['areaid']))->find();
                $data[$k]['id'] = $v['id'];
                $data[$k]['province'] = $res['pro'];
                $data[$k]['city'] = $res['city'];
                $data[$k]['address'] = $v['address'];
                $data[$k]['getuser'] = $v['getuser'];
                $data[$k]['tel'] = $v['tel'];  
                $data[$k]['phone'] = $v['phone'];  
                $data[$k]['sort'] = $v['sort'];  
                $data[$k]['serverrule'] = $v['serverrule'];       

            }
            output_data($data);
        }
    }






















   /*
    *向手机发送验证码
    */ 

    public function sendMsg($intPhonenum,$msg)
    {
        $arrData = array();
        $strInter = $this->phonemsginter."&mobile=".$intPhonenum."&content=".$msg;
        $xml = file_get_contents($strInter);
        $objXml = simplexml_load_string($xml);
        $arrData['code'] = (array)$objXml->code;
        $arrData['msg'] = (array)$objXml->msg;
        return $arrData;
    }



    /*
     *验证验证码
     */
    public function checkphonecode(){
        $intPhone = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
        $intCode = intval($_REQUEST['code']);
        $strSql = "update ajy_phonecode set status='1' where phone='$intPhone' and phonecode='$intCode'";
        $phonecode_model =  M('phonecode');
        $result = $phonecode_model->execute($strSql);
        if($result == 0){
            //验证失败
            output_error("验证码验证失败");
        }else{
            //验证成功
             output_data(array(
                'id' => $result
                
                
                ));
        }
    }


    /*
     * 登录
     */
    public function login(){
        if($_REQUEST['username'] == null || $_REQUEST['password'] == null) {
            output_error('用户名或密码错误！');
        }
        $model_member = M('user');
        $arr = array();
        $arr['username'] = htmlspecialchars($_REQUEST['username'],ENT_QUOTES);
        $arr['password'] = htmlspecialchars($_REQUEST['password'],ENT_QUOTES);
        // $check_phone='/^[1][358]\d{9}$/';
        
        // //验证提交过来的参数是邮箱还是用户名还是手机
        // if(preg_match($check_phone,$_POST['phone'])){
        //     $array['phone'] = $_POST['phone'];
        // }else{
        //     output_error('请输入正确的手机号码!');
        // }

        $arr['password']  = md5($arr['password']);
        $member_info = $model_member->where($arr)->select();
        if(!empty($member_info)) {

            $token = $this->_get_token($member_info[0]['id'], $member_info[0]['phone'], $_POST['client']);
            if($token) {
                
                output_data(array(
                'id' => $member_info[0]['id'],
                'phone'=>$member_info[0]['phone'],
                'username' => $member_info[0]['username'],
                'headurl' => $member_info[0]['headurl'],
                'key' => $token
                ));
            } else {
                output_error('登陆失败');
            }
        } else {
            output_error('用户名密码错误');
        }
    }



    /*
     * 退出
     */
    public function logout(){
        $array = array();
        $array['token'] = $_REQUEST['key'];
        $array['userid'] = $_REQUEST['userid'];
        $array['client_type'] = $_REQUEST['client'];

        $model_user_token = M('usertoken');
        $result = $model_user_token->where($array)->delete();
        //$result = $model_mb_user_token->delMbUserToken($array);
        if($result){
            $datas = array();
            $datas['msg'] = '退出成功！';
            output_data($datas);
        }else{
            output_error('退出失败，请重试！');
        }
    }


    /*
     * 忘记密码
     */
    public function forget_password(){
        if($_REQUEST['phonenum'] == null){
            output_error('请输入手机号码！');
        }
        if($_REQUEST['password'] == null){
            output_error('请输入密码！');
        }
        if($_REQUEST['code'] == null){
            output_error('请输入验证码！');
        }
        $model_user = M('user');
        $member_info = $model_user->where(array('phone'=>$_REQUEST['phonenum']))->find();
        if($member_info == null){
            output_error('不存在该用户！');
        }
        if($member_info['password'] == md5($_REQUEST['password'])){
            $datas = array();
            $datas['msg'] = '修改成功，请登陆！';
            output_data($datas);
        }
        $model_code = M('phonecode');
        $array = array();
        $array['phone'] = $_REQUEST['phonenum'];
        $array['phonecode'] = $_REQUEST['code'];
        $codes = $model_code->where($array)->find();
        
        //删除所有超过240秒的验证码
        $array = array();
        $array['dateline'] = array('LT',NOW_TIME-240);
        $model_code->where($array)->delete();
        if($codes == false){
            output_error('验证码错误！');
        }
        
        $array = array();
        $array['password'] = md5($_POST['password']);

        $result = $model_user->where(array('phone'=>$_REQUEST['phonenum']))->save($array);
        if($result){
            $datas = array();
            $datas['msg'] = '修改成功，请登陆！';
            output_data($datas);
        }else{
            output_error('修改失败，请稍后重试！');
        }
        
        
    }






















    /*
     * 注册在发短信之前验证是否有改手机号码
     */
    public function check_phone(){
        if($_POST['member_phone'] == null){
            output_error('请输入手机号码！');
        }
        $check_phone='/^1[0-9]{10}$/';
        if(preg_match($check_phone,$_POST['member_phone'])){
            $array['member_phone'] = $_POST['member_phone'];
        }else{
            output_error('请输入正确的手机号码!');
        }
        $model_member = M('member');
        $result = $model_member->where(array('member_phone'=>$_POST['member_phone']))->find();
        if(!$result){
            $datas = array();
            $datas['msg'] = '恭喜，该手机号码可以注册！';
            output_data($datas);
        }else{
            output_error('已经存在该手机号码了！');
        }
    }
    
}
   
    
    
    