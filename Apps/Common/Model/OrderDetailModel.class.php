<?php
namespace Common\Model;
use Think\Model;
/**
 * 订单商品
 */
class OrderDetailModel extends Model
{	
	protected $tableName = 'order_detail';

	const TYPE_GOODS = 1;//单品
    const TYPE_PACKAGE = 2;//套餐


	/**
     * @describe 添加订单商品
     * @param $goodsList 提交的数据
     * @return boolean
     */
	public function addGoods($goodsList, $cusId)
	{
		try{
			$this->startTrans();

			foreach ($goodsList as $goods) {
				$data = array(
					"cus_id" 	=> $cusId,
					"name" 		=> $goods["name"],
					"goods_id" 	=> $goods["id"],
					"shop_id"	=> $goods["shop_id"],
					"type" 		=> \Common\Model\OrderDetailModel::TYPE_GOODS,
					"sale_id" 	=> $cusId,
					"count"		=> 1,
					"yj"		=> $goods["price"],
					"jf"		=> $goods["price"],
					"add_time"	=> NOW_TIME
					);
				$res = $this->add($data);
				if(!$res) throw new \Exception("添加商品失败", 1);
			}
			$this->commit();
		} catch(Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
	}
}