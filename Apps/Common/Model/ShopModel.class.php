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

	/**
     * @describe 获取管理用户可以操作的商铺列表
     * @param $uid
     */
	public function getShopsByUid($uid)
	{
		$shopIds = D("AuthGroupAccess")->alias("aga")
			->join('LEFT JOIN '.C('DB_PREFIX').'auth_group_2_shop ag2s on aga.group_id = ag2s.id')
			->where(array("aga.uid"=>$uid))
			->getField("ag2s.rules");
		if(!$shopIds)
		{
			$this->error = "找不到对应的商铺";
			return false;
		}
		return $this->field("id, shop_name")
			->where(array(
				"id"=>array("in", $shopIds),
				"status"=>self::STATUS_NORMAL
				))
			->select();
	}
	
}