<?php
namespace Common\Model;
use Think\Model;
/**
 * 用户流水
 */
class CbcModel extends Model
{	
	protected $tableName = 'c_balance_change';

	const TYPE_RECHARGE = 1;//充值
	const TYPE_CONSUMER = 2;//消费
}