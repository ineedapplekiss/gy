<?php
namespace Common\Model;
use Think\Model;
/**
 * 充值策略
 */
class RechargeModel extends Model
{	
	protected $tableName = 'recharge_config';
	
	//系统三个状态
	const STATUS_EN = 1;//启用
	const STATUS_DIS = 0;//暂停
	const STATUS_DEL = -1;//下架删除

	//逻辑上的四个状态
	const STATUS_ALIVE = 1;//进行中 （活动时间范围内）
	const STATUS_SOON = 2;//定时 （没开始）
	const STATUS_PASS = 3;//失效 （已结束）
	const STATUS_OFFLINE = 4;//暂停（活动时间范围内）

	/**
     * @describe 取促销的逻辑状态
     * @param $status 数据库套餐状态， 0暂停 1启用 -1下架删除
     * @param $sta 开始时间
     * @param $end 结束时间
     * @return -1 删除 1 进行中 2定时 3失效 4暂停
     */
	public function getStatus($status, $sta, $end)
	{
		if($status == \Common\Model\SaleModel::STATUS_DEL) return \Common\Model\SaleModel::STATUS_DEL;
		if($sta>=NOW_TIME)
		{
			return \Common\Model\SaleModel::STATUS_SOON;
		}
		elseif($end<NOW_TIME)
		{
			return \Common\Model\SaleModel::STATUS_PASS;
		}
		else//时间范围内
		{
			if($status == \Common\Model\SaleModel::STATUS_EN)
				return \Common\Model\SaleModel::STATUS_ALIVE;
			else
				return \Common\Model\SaleModel::STATUS_OFFLINE;
		}
	}

	/**
     * @describe 格式化状态
     * @param $status 数据库套餐状态
     * @return string
     */
	public function formatStatus($status)
	{
		$arr = array(
			\Common\Model\SaleModel::STATUS_DEL 		=> "删除",
			\Common\Model\SaleModel::STATUS_ALIVE 	=> "进行中",
			\Common\Model\SaleModel::STATUS_SOON 	=> "定时",
			\Common\Model\SaleModel::STATUS_PASS 	=> "失效",
			\Common\Model\SaleModel::STATUS_OFFLINE 	=> "暂停"
			);
		return $arr[$status];
	}

	/**
     * @describe 格式化策略
     * @param $info 提交的数据
     * @return boolean
     */
	public function formatCond($cond_egt, $cond_elt, $return)
	{
		return sprintf("充%s-%s返%s ", $cond_egt, $cond_elt, $return);
	}
}