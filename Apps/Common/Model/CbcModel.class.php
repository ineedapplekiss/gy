<?php
namespace Common\Model;
use Think\Model;
/**
 * 用户流水
 */
class CbcModel extends Model
{	
	protected $tableName = 'c_balance_change';

	const TYPE_RECHARGE = 1;//充值jf
	const TYPE_CONSUMER = 2;//消费
	const TYPE_REFUND = 3;//退款
	const TYPE_RECHARGE_RMB = 4;//充值rmb,只作为记录用

	/**
     * @describe 操作用户账户，产生流水
     * @param $id 用户id
     * @param $type 三种流水类型
     * @param $jf 积分数量 （绝对值）
     * @param $act_id 关联业务id
     * @return boolean
     */
	public function balanceChange($id, $type, $jf, $act_id=0)
	{
		switch ($type) {
			case \Common\Model\CbcModel::TYPE_RECHARGE:
				$res = D("Cus")->where(array("id"=>$id))->setInc('jf',$jf);
				if(!$res) throw new \Exception("充值积分失败", 1);
				break;
			case \Common\Model\CbcModel::TYPE_CONSUMER:
				$res = D("Cus")->where(array("id"=>$id,
					"jf"=>array("egt",$jf)))->setDec('jf',$jf);
				if(!$res) throw new \Exception("消费积分失败", 1);
				//流水记录正负值
				$jf = bcsub(0, $jf, 2);
				break;
			case \Common\Model\CbcModel::TYPE_REFUND:
				$res = D("Cus")->where(array("id"=>$id))->setInc('jf',$jf);
				if(!$res) throw new \Exception("返还积分失败", 1);
				break;	
			default:
				throw new \Exception("类型错误", 1);
				break;
		}
		//产生流水
		$data = array(
			"c_id"=>$id,
			"type"=>$type,
			"jf"=>$jf,
			"act_id"=>$act_id,
			"add_time"=>NOW_TIME
			);
		$res = $this->add($data);
		if(!$res) throw new \Exception("添加流水失败", 1);

		return true;
	}

	public function formatType($type)
	{
		$arr = array(
			\Common\Model\CbcModel::TYPE_RECHARGE 	=> "充值",
			\Common\Model\CbcModel::TYPE_CONSUMER 	=> "消费",
			\Common\Model\CbcModel::TYPE_REFUND 	=> "退款"
			);
		return $arr[$type];
	}
}