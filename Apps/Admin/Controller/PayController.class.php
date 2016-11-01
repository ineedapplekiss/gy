<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class PayController extends CommonController {

	public function index(){
        $this->assign('cates',D("Cate")->allCate());
        $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
        $this->display();
    }

    //获取会员信息
    public function userDetail()
    {
        $ck=A('CheckInput');
        $cardNo=$ck->in('会员卡号','card_no','cnennumstr','',2,20);

        $userDetail["cus"] = D("Cus")->alias("c")
            ->field("c.id, c.name, c.jf, c.mobile, cl.name as lname")
            ->join("left join ".C('DB_PREFIX')."c_level as cl on c.level_id = cl.id")
            ->where(array("c.card_no"=>$cardNo))
            ->find();
        if(!$userDetail["cus"])
        {
            $result['message']='会员卡号不正确';
            $result['status']=false;
            $this->ajaxReturn($result);
        }

        if(!$userDetail["cus"]["status"] != \Common\Model\CusModel::STATUS_EN)
        {
            $result['message']='会员已禁用（删除）';
            $result['status']=false;
            $this->ajaxReturn($result);
        }

        
        $userDetail['status']=true;
        $this->ajaxReturn($userDetail);
    }

    //获取会员订单商品列表
    public function detailList()
    {
        $ck=A('CheckInput');
        $cus_id=$ck->in('会员id','cus_id','intval','',0,0);

        $count=D('OrderDetail')->alias("c")->where(array(
            "c.cus_id"=>$cus_id,
            "c.order_id"  => array("eq", 0)
            ))->count();
        $info=D('OrderDetail')
            ->alias("c")
            ->field("c.*")
            ->where(array(
                "c.cus_id"=>$cus_id,
                "c.order_id"  => array("eq", 0)
            ))
            ->select();

        foreach ($info as $key => $value) {
            $info[$key]["use_sale"] = $value["sale_id"] ? 1 : 0;
            $info[$key]["type_format"] = $value["type"] == 1 ? "商品" : "套餐";
        }

        if(!empty($info)){
            $userDetail["detail"]['total']=$count;
            $userDetail["detail"]['rows']=$info;
        }else{
            $userDetail["detail"]['total']=0;
            $userDetail["detail"]['rows']=0;
        }
        $this->ajaxReturn($userDetail["detail"]);

    }

    //添加订单商品
    public function addGoods()
    {
        $ck=A('CheckInput');
        $cus_id=$ck->in('会员id','cus_id','intval','',0,0);

        if(!$cus_id)
        {
            $result['message']='选择一个会员';
            $result['status']=false;
            $this->ajaxReturn($result);
        }

        $goodsIds=I('post.goods_ids');

        //检测重复商品
        $goodsCount = D("OrderDetail")->where(
            array(
                "cus_id"=>$cus_id,
                "goods_id"=>array("in",$goodsIds),
                "order_id" => array("eq", 0)
                )
            )->count();
        if($goodsCount){
            $result['message']='不要添加重复商品!';
            $result['status']=false; 
            $this->ajaxReturn($result);
        }

        $goods_info = D("Goods")->where(array("id"=>array("in",$goodsIds), "status"=>\Common\Model\GoodsModel::STATUS_NORMAL))->select();

        //检测不同商铺商品
        $shop_ids = array_unique(array_column($goods_info, "shop_id"));
        if(count($shop_ids) > 1)
        {
            $result['message']='请选择同一个商铺商品';
            $result['status']=false;
            $this->ajaxReturn($result);
        }
        $goodsCount = D("OrderDetail")->where(
            array(
                "cus_id"=>$cus_id,
                "shop_id"=>array("not in",implode(",", $shop_ids)),
                "order_id" => array("eq", 0)
                )
            )->count();
        if($goodsCount){
            $result['message']='不要添加不同商铺商品!';
            $result['status']=false; 
            $this->ajaxReturn($result);
        }


        $addStatus = D("OrderDetail")->addGoods($goods_info, $cus_id);
        if($addStatus){
            $result['message']='添加商品成功!';
            $result['status']=true; 
        }else{
            $result['message']=D("OrderDetail")->getError();
            $result['status']=false;    
        }
        $this->ajaxReturn($result);

    }

    //添加订单套餐
    public function addP()
    {
        $ck=A('CheckInput');
        $cus_id=$ck->in('会员id','cus_id','intval','',0,0);

        if(!$cus_id)
        {
            $result['message']='选择一个会员';
            $result['status']=false;
            $this->ajaxReturn($result);
        }

        $pIds=I('post.p_ids');

        //检测重复套餐
        $pCount = D("OrderDetail")->where(
            array(
                "cus_id"=>$cus_id,
                "pack_id"=>array("in",$pIds),
                "order_id" => array("eq", 0)
                )
            )->count();
        if($pCount){
            $result['message']='不要添加重复套餐!';
            $result['status']=false; 
            $this->ajaxReturn($result);
        }

        $p_info = D("Package")->where(array("id"=>array("in",$pIds), "status"=>\Common\Model\PackageModel::STATUS_EN))->select();

        //检测不同商铺商品
        $shop_ids = array_unique(array_column($p_info, "shop_id"));
        if(count($shop_ids) > 1)
        {
            $result['message']='请选择同一个商铺套餐';
            $result['status']=false;
            $this->ajaxReturn($result);
        }
        $goodsCount = D("OrderDetail")->where(
            array(
                "cus_id"=>$cus_id,
                "shop_id"=>array("not in",implode(",", $shop_ids)),
                "order_id" => array("eq", 0)
                )
            )->count();
        if($goodsCount){
            $result['message']='不要添加不同商铺套餐!';
            $result['status']=false; 
            $this->ajaxReturn($result);
        }


        $addStatus = D("OrderDetail")->addPack($p_info, $cus_id);
        if($addStatus){
            $result['message']='添加套餐成功!';
            $result['status']=true; 
        }else{
            $result['message']=D("OrderDetail")->getError();
            $result['status']=false;    
        }
        $this->ajaxReturn($result);

    }

    //获取订单商品促销信息
    public function getGoodsSales()
    {
        $goods_id = I("get.goods_id", "intval");
        $type = I("get.type", "intval");
        $cus_id = I("get.cus_id", "intval");

        if(!$goods_id)
        {
            $result['message']='选择一个商品（套餐无折扣）';
            $result['status']=false;
            $this->ajaxReturn($result);
        }


        $res[] = array("id"=>0, "name"=>"无");
        $sales = D("OrderDetail")->getGoodsSales($goods_id, $cus_id);
        $return = array_merge($res, $sales);
        $this->ajaxReturn($return);
    }

    //获取商铺促销信息
    public function getShopSales()
    {
        $cus_id = I("get.cus_id", "intval");

        if(!$cus_id)
        {
            $result['message']='请输入正确的会员信息';
            $result['status']=false;
            $this->ajaxReturn($result);
        }
        $res[] = array("id"=>0, "name"=>"无");
        $sales = D("OrderDetail")->getShopSales($cus_id);
        $return = array_merge($res, $sales);
        $this->ajaxReturn($return);
    }

    //保存订单商品
    public function saveOd()
    {
        $ck=A('CheckInput');
        $od_id=$ck->in('订单商品','od_id','intval','',0,0);
        $payEditCount=$ck->in('数量','payEditCount','intval','',0,0);
        $use_c_level=$ck->in('会员折扣','use_c_level','intval','',0,0);
        $sale_id=$ck->in('促销','sale_id','intval','',0,0);

        if(!$od_id)
        {
            $result['message']='订单商品错误';
            $result['status']=false;
            $this->ajaxReturn($result);
        }
        if(!$payEditCount)
        {
            $result['message']='填写正确的数量';
            $result['status']=false;
            $this->ajaxReturn($result);
        }
        $od_data = D("OrderDetail")->updateOd($od_id, $payEditCount, $use_c_level, $sale_id);
        if($od_data){
            $result['message']='修改成功!';
            $result['status']=true; 
        }else{
            $result['message']=D("OrderDetail")->getError();
            $result['status']=false;    
        }
        $this->ajaxReturn($result);

    }

    /**
     * 删除
     * @param $id 
     * @author shang
     */
    public function delHandle($id){
        if(!IS_POST||$issystem=='1') exit;
        $map['id']=(int)$id;
        if(empty($map['id'])){
            $return['message']='id不能为空!';
            $return['status']=false;
        }else{
            $status=D("OrderDetail")->where($map)->delete();
            if(false===$status){
                $return['message']='删除出错!';
                $return['status']=false;
            }else{
                $return['message']='删除成功!';
                $return['status']=true;
            }
        }
        $this->ajaxReturn($return);
    }

    //下订单
    public function pay()
    {
        $ck=A('CheckInput');
        $cus_id=$ck->in('会员id','cus_id','intval','',0,0);
        $sale_id=$ck->in('折扣信息','sale_id','intval','',0,0);
        $remark=$ck->in('订单备注','remark','cnennumstr','',0,0);

        if(!$cus_id)
        {
            $result['message']='选择一个会员';
            $result['status']=false;
            $this->ajaxReturn($result);
        }

        $addStatus = D("Order")->pay($cus_id, $sale_id, $remark);
        if($addStatus){
            $result['message']='添加商品成功!';
            $result['status']=true; 
        }else{
            $result['message']=D("Order")->getError();
            $result['status']=false;    
        }
        $this->ajaxReturn($result);
    }
}	