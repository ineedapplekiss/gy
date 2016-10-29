<?php
namespace Common\Model;
use Think\Model;
/**
 * 会员
 */
class CusModel extends Model
{	
	protected $tableName = 'customer';
	
	//系统三个状态
	const STATUS_EN = 1;//启用
	const STATUS_DIS = 0;//暂停
	const STATUS_DEL = -1;//下架删除

	

	/**
     * @describe 添加会员
     * @param $info 提交的数据
     * @return boolean
     */
	public function addCus($info)
	{
		try{
			$this->startTrans();
			$cusId = $this->add($info);
			if(!$cusId) throw new \Exception("添加会员失败", 1);

			//会员卡状态修改
			$cardDetail = $map = array();
			$cardDetail["status"]	= \Common\Model\CardDetailModel::STATUS_EN;

			$map["card_no"]	= $info["card_no"];
			$res = D("CardDetail")->where($map)->save($cardDetail);
			if(!$res) throw new \Exception("会员卡激活失败", 1);

			//添加流水
			$cbcInfo["type"]	= \Common\Model\CbcModel::TYPE_RECHARGE;
			$cbcInfo["jf"]		= $info["jf"];
			$cbcInfo["act_id"]	= $cusId;
			$cbcInfo["add_time"]= NOW_TIME;
			$res = D("Cbc")->add($cbcInfo);
			if(!$res) throw new \Exception("流水添加失败", 1);
			
			$this->commit();
		} catch(Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
	}
}