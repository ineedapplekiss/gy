<?php
namespace Common\Model;
use Think\Model;
/**
 * 商品促销
 */
class SaleModel extends Model
{	
	protected $tableName = 'sale';
	
	//系统三个状态
	const STATUS_EN = 1;//启用
	const STATUS_DIS = 0;//暂停
	const STATUS_DEL = -1;//下架删除

	//促销种类
	const TYPE_G_ZJ = 1;//商品直降
	const TYPE_G_ZK = 2;//商品折扣
	const TYPE_S_MJ = 3;//商铺满减
	const TYPE_S_MZ = 4;//商铺满折

	//逻辑上的四个状态
	const STATUS_ALIVE = 1;//进行中 （活动时间范围内）
	const STATUS_SOON = 2;//定时 （没开始）
	const STATUS_PASS = 3;//失效 （已结束）
	const STATUS_OFFLINE = 4;//暂停（活动时间范围内）

	/**
     * @describe 取促销的逻辑状态
     * @param $status 数据库套餐状态， 0暂停 1启用 -1下架删除
     * @param $sta 开始时间
     * @param $end 结束时间
     * @return -1 删除 1 进行中 2定时 3失效 4暂停
     */
	public function getStatus($status, $sta, $end)
	{
		if($status == \Common\Model\SaleModel::STATUS_DEL) return \Common\Model\SaleModel::STATUS_DEL;
		if($sta>=NOW_TIME)
		{
			return \Common\Model\SaleModel::STATUS_SOON;
		}
		elseif($end<NOW_TIME)
		{
			return \Common\Model\SaleModel::STATUS_PASS;
		}
		else//时间范围内
		{
			if($status == \Common\Model\SaleModel::STATUS_EN)
				return \Common\Model\SaleModel::STATUS_ALIVE;
			else
				return \Common\Model\SaleModel::STATUS_OFFLINE;
		}
	}

	/**
     * @describe 格式化状态
     * @param $status 数据库套餐状态
     * @return string
     */
	public function formatStatus($status)
	{
		$arr = array(
			\Common\Model\SaleModel::STATUS_DEL 		=> "删除",
			\Common\Model\SaleModel::STATUS_ALIVE 	=> "进行中",
			\Common\Model\SaleModel::STATUS_SOON 	=> "定时",
			\Common\Model\SaleModel::STATUS_PASS 	=> "失效",
			\Common\Model\SaleModel::STATUS_OFFLINE 	=> "暂停"
			);
		return $arr[$status];
	}

	/**
     * @describe 格式化类型
     * @param $status 数据库套餐状态
     * @return string
     */
	public function formatType($type)
	{
		$arr = array(
			\Common\Model\SaleModel::TYPE_G_ZJ 	=> "商品直降",
			\Common\Model\SaleModel::TYPE_G_ZK 	=> "商品折扣",
			\Common\Model\SaleModel::TYPE_S_MJ 	=> "商铺满减",
			\Common\Model\SaleModel::TYPE_S_MZ 	=> "商铺折扣",
			);
		return $arr[$type];
	}

	/**
     * @describe 添加促销
     * @param $info 提交的数据
     * @return boolean
     */
	public function addSale($info)
	{
		try{
			$this->startTrans();
			$saleId = $this->add($info);
			if(!$saleId) throw new \Exception("添加促销失败", 1);

			//存储促销商品
			$goods_info = $info["goods_info"];
			$add_time = NOW_TIME;
            array_walk($goods_info, function(&$goods)use($saleId, $add_time){
            	$saleGoods = array();
                $saleGoods["jf"] = $goods["price"];
                $saleGoods["s_id"] = $saleId;
                $saleGoods["goods_id"] = $goods["id"];
                $saleGoods["add_time"] = $add_time;
                $goods = $saleGoods;
            });
            $saleGoodsId = D("SaleGoods")->addAll($goods_info);
            if(!$saleGoodsId) throw new \Exception("添加促销商品失败", 1);
			$this->commit();
		} catch(Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
	}
}