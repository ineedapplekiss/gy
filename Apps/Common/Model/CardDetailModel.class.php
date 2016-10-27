<?php
namespace Common\Model;
use Think\Model;
/**
 * 会员卡详情
 */
class CardDetailModel extends Model
{	
	protected $tableName = 'card_detail';
	
	//系统三个状态
	const STATUS_EN = 1;//已激活
	const STATUS_DIS = 0;//未激活
	const STATUS_DEL = -1;//作废

	//获取可用的会员卡
	public function getCs()
	{
		return $this->field('id, card_no')->where(array('status'=>\Common\Model\CardDetailModel::STATUS_DIS))->select();
	}
}