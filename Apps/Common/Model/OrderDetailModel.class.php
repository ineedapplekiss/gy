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
					"sale_id" 	=> 0,
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

	/**
     * @describe 添加订单套餐
     * @param $goodsList 提交的数据
     * @return boolean
     */
	public function addPack($pList, $cusId)
	{
		try{
			$this->startTrans();

			foreach ($pList as $p) {
				$data = array(
					"cus_id" 	=> $cusId,
					"name" 		=> $p["name"],
					"pack_id" 	=> $p["id"],
					"shop_id"	=> $p["shop_id"],
					"type" 		=> \Common\Model\OrderDetailModel::TYPE_PACKAGE,
					"sale_id" 	=> 0,
					"count"		=> 1,
					"yj"		=> $p["jf"],
					"jf"		=> $p["jf"],
					"add_time"	=> NOW_TIME
					);
				$res = $this->add($data);
				if(!$res) throw new \Exception("添加套餐失败", 1);
			}
			$this->commit();
		} catch(Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
	}

	/**
     * @describe 查询商品可用促销
     * @param $goods 
     * @param $cusId 
     * @return boolean
     */
	public function getGoodsSales($goodsId, $cusId)
	{
		$cusDetail = D("Cus")->where(array("id"=>$cusId))->find();
		$map["sg.goods_id"] = $goodsId;
		$map["s.type"] = array("in",\Common\Model\SaleModel::TYPE_G_ZJ.",".\Common\Model\SaleModel::TYPE_G_ZK); 
		$map["s.rule_gender"] = array("in","0,".$cusDetail["gender"]); 
		$map["s.status"] = \Common\Model\SaleModel::STATUS_EN;
		$map["s.sta_time"] = array("lt",NOW_TIME);
		$map["s.end_time"] = array("gt",NOW_TIME);
		$sales = D("SaleGoods")->alias("sg")
			->field("s.id, s.name, s.price,s.full,s.cut,s.type,s.rule_gender, s.rule_age")
			->join("left join ".C('DB_PREFIX')."sale as s on sg.s_id = s.id")
			->where($map)
			->select();
		//过滤年龄限制
		if($cusDetail["birthday"])
		{
			$age = date("Y") - substr($cusDetail["birthday"], 0, 4);
			foreach ($sales as $sk => $sv) {
				if($sv["rule_age"])
				{
					list($min,$max) = explode("-", $cusDetail["rule_age"]);
					if(($max!=0 && $age>$max) || ($min!=0 &&$age<$min))
						unset($sales[$sk]);
				}
			}
		}
		sort($sales);
		return $sales;
	}

	/**
     * @describe 查询商铺可用促销
     * @param $goods 
     * @param $cusId 
     * @return boolean
     */
	public function getShopSales($cusId)
	{
		$ods = $this->where(array(
			"cus_id"=>$cusId,
			"sale_id" => array("eq", 0),
			"use_c_level" => array("eq", 0),
			"order_id" => array("eq", 0)
			))->select();
		if(!$ods)return array();
		$shop_id = $ods[0]["shop_id"];
		$goods_ids = array_column($ods, "goods_id");

		$cusDetail = D("Cus")->where(array("id"=>$cusId))->find();

		$map["sg.goods_id"] = array("in",implode(",",$goods_ids));
		$map["s.type"] = array("in",\Common\Model\SaleModel::TYPE_S_MJ.",".\Common\Model\SaleModel::TYPE_S_MZ); 
		$map["s.rule_gender"] = array("in","0,".$cusDetail["gender"]); 
		$map["s.status"] = \Common\Model\SaleModel::STATUS_EN;
		$map["s.sta_time"] = array("lt",NOW_TIME);
		$map["s.end_time"] = array("gt",NOW_TIME);
		$sales = D("SaleGoods")->alias("sg")
			->field("s.id, s.name, s.price,s.full,s.cut,s.type,s.rule_gender, s.rule_age")
			->join("left join ".C('DB_PREFIX')."sale as s on sg.s_id = s.id")
			->where($map)
			->group("s.id")
			->select();
		if(!$sales)return array();
		//过滤年龄限制
		if($cusDetail["birthday"])
		{
			$age = date("Y") - substr($cusDetail["birthday"], 0, 4);
			foreach ($sales as $sk => $sv) {
				if($sv["rule_age"])
				{
					list($min,$max) = explode("-", $cusDetail["rule_age"]);
					if(($max!=0 && $age>$max) || ($min!=0 &&$age<$min))
						unset($sales[$sk]);
				}
			}
		}
		sort($sales);
		return $sales;
	}
	
	/**
     * @describe 修改订单商品
     * @param $goods 
     * @param $cusId 
     * @return boolean
     */
	public function updateOd($od_id, $payEditCount, $use_c_level, $sale_id)
	{
		$odRes = $this->find($od_id);
		if(!$odRes)
		{
			$this->error = "无法获取od信息";
			return false;
		}
		if($use_c_level && $sale_id)
		{
			$this->error = "会员折扣和促销不可同时使用";
			return false;
		}
		if($odRes["type"] == \Common\Model\OrderDetailModel::TYPE_PACKAGE && ($use_c_level || $sale_id))
		{
			$this->error = "套餐不可使用会员折扣和促销";
			return false;
		}

		if($use_c_level)
		{
			$levelRate = D("Cus")
				->alias("c")
				->join("left join ".C('DB_PREFIX')."c_level as l on c.level_id = l.id")
				->where(array("c.id"=>$odRes["cus_id"]))
				->getField("l.rebate");
			$jf = bcdiv(bcmul(bcmul($odRes["yj"], $payEditCount, 4), $levelRate, 4), 100, 2);
		}
		elseif($sale_id)
		{
			$sale = D("Sale")->find($sale_id);
			if(!$sale)
			{
				$this->error = "错误的促销信息";
				return false;
			}
			if($sale["type"] == \Common\Model\SaleModel::TYPE_G_ZJ)
				$jf = bcmul($sale["price"], $payEditCount, 2);
			elseif($sale["type"] == \Common\Model\SaleModel::TYPE_G_ZK)
				$jf = bcmul(bcmul($odRes["yj"], $sale["price"], 4), $payEditCount, 2);
			else
				$jf = bcmul($odRes["yj"], $payEditCount, 2);
		}
		else
		{
			$jf = bcmul($odRes["yj"], $payEditCount, 2);
		}

		$res = $this->where(array("id"=>$od_id))->save(array(
			"use_c_level"	=> $use_c_level,
			"sale_id"		=> $sale_id,
			"count"			=> $payEditCount,
			"jf"			=> $jf
			));
		if($res)
		{
			return $res;
		}
		else
		{
			$this->error = "更新失败";
			return false;
		}

	}
}