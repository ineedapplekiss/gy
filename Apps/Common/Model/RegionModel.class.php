<?php
namespace Common\Model;
use Think\Model;
/**
 * 省市县
 */
class RegionModel extends Model
{	
	protected $tableName = 'region';

	public function getByPid($pid)
	{
		return $this->where(array("parent_id"=>$pid))->select();
	}
}