<?php
namespace Common\Model;
use Think\Model;
/**
 * 汇率
 */
class ErateModel extends Model
{	
	protected $tableName = 'erate';
	
	const RATE_KEY = 'erate';//进行中 （活动时间范围内）
	
	public function getErate()
	{
		return $this->where(array("k"=>\Common\Model\ErateModel::RATE_KEY))->getField("rate");
	}

	public function setErate($value = 1.00)
	{
		$data = array(
			"k"	=> \Common\Model\ErateModel::RATE_KEY,
			'rate' => $value,
			"add_time"	=> NOW_TIME
			);
		$cond = array(
			"k"	=> \Common\Model\ErateModel::RATE_KEY
			);
		return $this->where($cond)->add($data, $cond, 'rate');
	}
}