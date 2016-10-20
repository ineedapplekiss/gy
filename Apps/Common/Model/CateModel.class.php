<?php
namespace Common\Model;
use Think\Model;
/**
 * 商品分类
 */
class CateModel extends Model
{	
	protected $tableName = 'g_cate';
	const STATUS_NORMAL = 1;//正常数据
	const STATUS_DEL = 0;//删除

	const TYPE_GOODS_CATE = 1;//商品分类
	const TYPE_SHOPS_CATE = 2;//商铺分类

	public function allCate()
	{
		return $this->field("id,name")->where(array("status"=>self::STATUS_NORMAL, "type"=>self::TYPE_GOODS_CATE))->select();
	}
}