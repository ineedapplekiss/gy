<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class OrderController extends CommonController {

	public function index(){
        //仅允许查询权限下的商铺
        $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
		$this->display();
	}

    public function detail(){
        $id = I("get.id", 0, "intval");
        $order = D('Order')
            ->alias("o")
            ->field("o.*, c.name as uname, c.card_no, s.shop_name")
            ->join("left join ".C('DB_PREFIX')."customer as c on o.c_id=c.id")
            ->join("left join ".C('DB_PREFIX')."shop as s on o.shop_id=s.id")
            ->where(array("o.id"=>$id))
            ->find();
        $od = D("OrderDetail")->where(array("order_id"=>$id))->select();
        if($order["status"] == \Common\Model\OrderModel::STATUS_REFUND)
        {
            $status = "已退款";
        }
        elseif($order["status"] == \Common\Model\OrderModel::STATUS_DIS)
        {
            $status = "无效订单";
        }
        else
        {
            if(date("Y-m-d") == date("Y-m-d", $order["add_time"]))
            {
                $status = "可退款";
            }
            else
            {
                $status = "已完成";
            }
        }
        $this->assign('order',$order);
        $this->assign('status',$status);
        $this->assign('od',$od);
        $this->display();
    }

	/**
	 * 列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function orderList(){
    	$ck=A('CheckInput');

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	

        //查询条件
        $uname=$ck->in('会员姓名','uname','cnennumstr','',0,0);     
        $card_no=$ck->in('会员卡号','card_no','cnennumstr','',0,0);
        $shopId=$ck->in('商铺','shopId','intval','',0,0);
        $stime=$ck->in('开始时间','stime','datetime','',0,0);
        $etime=$ck->in('结束时间','etime','datetime','',0,0);

        $map = array();
        !empty($uname)?$map['c.name']=array('like','%'.$uname.'%'):'';
        !empty($card_no)?$map['c.card_no']=array('like','%'.$card_no.'%'):'';
        !empty($stime) && $map['o.add_time']= array("gt", strtotime($stime));
        !empty($etime) && $map['o.add_time']= array("lt", strtotime($etime));
        !empty($stime) && !empty($etime) && $map['o.add_time']= array("between", array(strtotime($stime), strtotime($etime)));

        //仅允许查询权限下的商铺
        $shopIds = D("Shop")->getShopsByUid(session('uid'));
        if($shopId>0 && in_array($shopId, array_column($shopIds, "id")))//指定商铺
        {
            $map['o.shop_id'] = $shopId;
        }
        else
        {
            $shopIds = implode(",",array_column($shopIds, "id"));
            $map['o.shop_id'] = array('IN',$shopIds);
        }

    	
    	$count=D('Order')->alias("o")
            ->field("o.*, c.name as uname, c.card_no, s.shop_name")
            ->join("left join ".C('DB_PREFIX')."customer as c on o.c_id=c.id")
            ->join("left join ".C('DB_PREFIX')."shop as s on o.shop_id=s.id")
            ->where($map)->count();
        $info=D('Order')
            ->alias("o")
            ->field("o.*, c.name as uname, c.card_no, s.shop_name")
            ->join("left join ".C('DB_PREFIX')."customer as c on o.c_id=c.id")
            ->join("left join ".C('DB_PREFIX')."shop as s on o.shop_id=s.id")
            ->order($sort.' '.$order)->page($page.','.$rows)->where($map)->page($page,$rows)->select();

        $odInfo = D("OrderDetail")->where(array("order_id"=>array("in", implode(",", array_column($info, "id")))))->select();

        $map = array();
        foreach ($odInfo as $v) {
            $map[$v["order_id"]][] = sprintf("%s(%s)", $v["name"], $v["type"]==1 ? "商品" : "套餐");
        }

        //格式化输出
        foreach ($info as $k => $v) {
            $info[$k]['order_info'] = implode("+",$map[$v["id"]]);
        }
    	
    	if(!empty($info)){
            $data['total']=$count;
            $data['rows']=$info;
        }else{
            $data['total']=0;
            $data['rows']=0;
        }
        $this->ajaxReturn($data);
    }
    

    /**
     * 订单退款
     *@author shang
     *
     */
    public function refund($id){

        //if(!IS_POST) exit;
        $map['id']=(int)$id;

        if(empty($map['id'])){
            $return['message']='id不能为空!';
            $return['status']=false;
        }else{
            $res = D("Order")->refund($map['id']);
            if($res){
                $return['message']='退款成功!';
                $return['status']=true; 
            }else{
                $return['message']=D("Order")->getError();
                $return['status']=false;    
            }
        }
        $this->ajaxReturn($return);
    }

}	