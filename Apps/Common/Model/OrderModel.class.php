<?php
namespace Common\Model;
use Think\Model;
/**
 * 促销商品
 */
class OrderModel extends Model
{	
	protected $tableName = 'order';

	const STATUS_EN = 1;//正常数据
	const STATUS_DIS = 0;//删除

	/**
     * @describe 添加订单
     * @param $cusId $sale_id
     * @return boolean
     */
	public function pay($cusId, $sale_id, $remark)
	{
		//获取order detail信息
		$ods = D("OrderDetail")->where(array(
			"cus_id"=>$cusId,
			"order_id" => array("eq", 0)
			))->select();
		if(!$ods)
		{
			$this->error = "空订单";
			return false;
		}
		$shop_ids = array_unique(array_column($ods, "shop_id"));
		if(count($shop_ids)>1)
		{
			$this->error = "不支持多家商铺商品";
			return false;
		}
		$shop_id = $shop_ids[0];

		//会员信息
		$cusDetail = D("Cus")->where(array("id"=>$cusId))->find();

		//折扣信息
		$sale = D("Sale")->where(array("id"=>$sale_id))->find();

		$sg = D("SaleGoods")->where(array("sale_id"=>$sale_id))->select();
		$sg_goods_ids = array_column($sg, "goods_id");


		if($sale_id)
		{
			if($sale["status"] != \Common\Model\SaleModel::STATUS_EN)
			{
				$this->error = "促销状态不正确";
				return false;
			}
			if($sale["type"] != \Common\Model\SaleModel::TYPE_S_MJ && $sale["type"] != \Common\Model\SaleModel::TYPE_S_MZ)
			{
				$this->error = "促销类型不正确";
				return false;
			}
			if($sale["sta_time"] > NOW_TIME ||  $sale["end_time"] < NOW_TIME)
			{
				$this->error = "促销时间不正确";
				return false;
			}
			if($sale["rule_gender"] && $sale["rule_gender"] != $cusDetail["gender"])
			{
				$this->error = "性别限定不符合";
				return false;
			}
			if($sale["rule_age"])
			{
				$age = date("Y") - substr($cusDetail["birthday"], 0, 4);
				list($min,$max) = explode("-", $cusDetail["rule_age"]);
				if(($max!=0 && $age>$max) || ($min!=0 &&$age<$min))
				{
					$this->error = "年龄限定不符合";
					return false;
				}		
			}
		}

		$data = array();
		$data["c_id"] = $cusId;
		$data["m_id"] = session("uid");
		$data["shop_id"] = $shop_id;
		$data["sale_id"] = $sale_id;
		$data["order_sn"] = "";
		$data["yj"] = "0";
		$data["jf"] = "0";
		$data["remark"] = $remark;
		$data["status"] = \Common\Model\OrderModel::STATUS_DIS;
		$data["add_time"] = NOW_TIME;
		$order_id = $this->add($data);
		if(!$order_id) return false;

		try{
			$this->startTrans();

			$total = $saleTotal = $yj = 0;
			foreach ($ods as $od) {
				//有促销，会员价，不在促销名单里的，不参与打折计算
				if($od["use_c_level"] || $od["sale_id"] || !in_array($od["goods_id"], $sg_goods_ids))
				{
					$total = bcadd($total, $od["jf"], 4);//不打折的总价
				}
				else
				{
					$saleTotal = bcadd($saleTotal, $od["jf"], 4);//打折的总价
				}
			}
			$yj = bcadd($total, $saleTotal, 2);
			if($saleTotal >= $sale["full"])//价位符合
			{
				if($sale["type"] == \Common\Model\SaleModel::TYPE_S_MJ)//满减(不考虑每满减)
				{
					$jf = bcadd($total, bcsub($saleTotal, $sale["cut"], 4), 2);
				}
				elseif ($sale["type"] == \Common\Model\SaleModel::TYPE_S_MZ) //满折
				{
					$jf = bcadd($total, bcmul($saleTotal, $sale["cut"], 4), 2);
				}
				else
				{
					$jf = $yj;
				}
			}
			else
			{
				$jf = $yj;
			}
			
			//更新订单表
			$data = array();
			$data["yj"] = $yj;
			$data["jf"] = $jf;
			$data["status"] = \Common\Model\OrderModel::STATUS_EN;
			$res = $this->where(array("id"=>$order_id))->save($data);
			if(!$res) throw new \Exception("更新订单失败", 1);

			//更新订单商品
			$data = array();
			$data["order_id"] = $order_id;
			$res = D("OrderDetail")->where(array("id"=>array("in", implode(",", array_column($ods, "id")))))->save($data);
			if(!$res) throw new \Exception("更新订单商品商品失败", 1);
			
			$this->commit();
		} catch(Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
	}
}