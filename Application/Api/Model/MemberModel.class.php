<?php
namespace Api\Model;
use Think\Model;

// 用户模型
class MemberModel extends Model {
    
    /*
     * 查询用户信息
     */
    public function getMemberInfo($condition, $field = '*') {
        $result = $this->where($condition)->find();
        return $result;
    }
    /**
     * 注册
     */
    public function register($register_info) {
        // 会员添加
        $member_info = array();
        $member_info['password']	= $register_info['password'];
        $member_info['member_phone']	= $register_info['member_phone'];		//手机号码
        $member_info['gender'] = $register_info['gender'];
        $member_info['hx_username'] = $register_info['hx_username'];
        $member_info['hx_password'] = $register_info['hx_password'];
        $member_info['hx_uuid'] = $register_info['hx_uuid'];
        $member_info['lat'] = $register_info['lat'];
        $member_info['lng'] = $register_info['lng'];
        $member_info['label'] = $register_info['label'];
        $member_info['integral'] = 100;
        $member_info['register_date'] = NOW_TIME;
        $insert_id	= $this->add($member_info);
        
        if($insert_id) {
            $member_info = array();
            $member_info = $this->where(array('member_id'=>$insert_id))->find(); 
            return $member_info;
        } else {
            return array('error' => '注册失败');
        }
    
    }
    
}