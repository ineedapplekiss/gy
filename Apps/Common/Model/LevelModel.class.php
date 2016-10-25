<?php
namespace Common\Model;
use Think\Model;
/**
 * 会员等级
 */
class LevelModel extends Model
{	
	protected $tableName = 'c_level';
	
	const STATUS_EN  = 1;//正常
	const STATUS_DIS = 0;//下架删除
}