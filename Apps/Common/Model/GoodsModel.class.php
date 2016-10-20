<?php
namespace Common\Model;
use Think\Model;
/**
 * 商品
 */
class GoodsModel extends Model
{	
	protected $tableName = 'goods';
	
	const STATUS_NORMAL = 1;//正常数据
	const STATUS_DEL = 0;//下架删除
	
}