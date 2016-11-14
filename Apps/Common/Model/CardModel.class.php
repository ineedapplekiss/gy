<?php
namespace Common\Model;
use Think\Model;
/**
 * 会员卡
 */
class CardModel extends Model
{	
	protected $tableName = 'card';

	/**
     * @describe 添加会员卡
     * @param $data 提交的数据
     * @return boolean
     */
	public function addCard($data)
	{
		try{
			$this->startTrans();
			$cardId = $this->add($data);
			if(!$cardId) throw new \Exception("添加会员卡失败", 1);

			//生成对应数量的会员卡
			for($i=1; $i<=$data["card_number"]; $i++)
			{
				$cardDetail = array();
				$cardDetail["card_id"]	= $cardId;
				$cardDetail["card_no"]	= $this->mkcardid($cardId, $i);
				$cardDetail["activity_name"]	= $data["activity_name"];
				$cardDetail["add_time"]	= NOW_TIME;
				$cdId = D("CardDetail")->add($cardDetail);
				if(!$cdId) throw new \Exception("生成详细会员卡失败".$i, 1);
			}
			//卡号规则 8868开头+6位顺序id
			$res = $this->execute("update think_card_detail set card_no = CONCAT('8868',LPAD(id,6,0)) where card_id=".$cardId);
			if(!$res) throw new \Exception("卡号生成失败，已经超出规则定义范围".$i, 1);

			$this->commit();
		} catch(\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
        return true;
	}

	/**
     * @describe 生成会员卡号
     * @param $cardId 
     * @param $num 张数序号
     * @return 卡号
     */
	private function mkcardid($cardId, $num)
	{
		$cardId = sprintf("%05d", $cardId);
		$num = sprintf("%05d", $num);
		return uniqid($cardId."S".$num);

	}
}