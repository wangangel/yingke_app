<?php
namespace Api\Model;
use Think\Model;

// 用户模型
class MbUserTokenModel extends Model {
    /**
	 * 查询
     *
	 * @param array $condition 查询条件
     * @return array
	 */
    public function getMbUserTokenInfo($condition) {
        return $this->where($condition)->find();
    }

    public function getMbUserTokenInfoByToken($token) {
        if(empty($token)) {
            return null;
        }
        return $this->getMbUserTokenInfo(array('token' => $token));
    }

	/**
	 * 新增
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function addMbUserToken($param){
        return $this->add($param);
	}
	
	/**
	 * 删除
	 * @param int $condition 条件
	 * @return bool 布尔类型的返回结果
	 */
	public function delMbUserToken($condition){
        return $this->where($condition)->delete();
	}
    
}