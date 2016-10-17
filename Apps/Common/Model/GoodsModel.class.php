<?php
namespace Common\Model;
use Think\Model;
/**
 * 商品
 */
class GoodsModel extends Model
{	
	protected $tableName = 'goods';
	
	
	/**
	 * @var 正常数据
	 */
	const STATUS_NORMAL = 1;
	
	/**
	 * @var 隐藏数据
	 */
	const STATUS_DEL = 0;
	
}