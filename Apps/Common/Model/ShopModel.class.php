<?php
namespace Common\Model;
use Think\Model;
/**
 * 商铺
 */
class ShopModel extends Model
{	
	protected $tableName = 'shop';
	
	
	/**
	 * @var 正常数据
	 */
	const STATUS_NORMAL = 1;
	
	/**
	 * @var 隐藏数据
	 */
	const STATUS_DEL = 0;
	
}