<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class PayController extends CommonController {

	public function index(){
        $this->assign('cates',D("Cate")->allCate());
        $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
        $this->display();
    }

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

    public function detailList()
    {
        $ck=A('CheckInput');
        $cus_id=$ck->in('会员id','cus_id','intval','',0,0);

        $count=D('OrderDetail')->alias("c")->where(array("c.cus_id"=>$cus_id))->count();
        $info=D('OrderDetail')
            ->alias("c")
            ->field("c.*")
            ->where(array("c.cus_id"=>$cus_id))
            ->select();

        foreach ($info as $key => $value) {
            $info[$key]["use_sale"] = $value["sale_id"] ? 1 : 0;
            $info[$key]["type"] = $value["type"] == 1 ? "商品" : "套餐";
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

        $goodsCount = D("OrderDetail")->where(
            array(
                "cus_id"=>$cus_id,
                "goods_id"=>array("in",$goodsIds)
                )
            )->count();
        if($goodsCount){
            $result['message']='不要添加重复商品!';
            $result['status']=false; 
            $this->ajaxReturn($result);
        }

        $goods_info = D("Goods")->where(array("id"=>array("in",$goodsIds), "status"=>\Common\Model\GoodsModel::STATUS_NORMAL))->select();

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

    public function getGoodsSales()
    {
        $ck=A('CheckInput');
        $goods_id=$ck->in('商品id','goods_id','intval','',0,0);
        $goods_id = I("get.goods_id");

        if(!$goods_id)
        {
            $result['message']='选择一个商品';
            $result['status']=false;
            $this->ajaxReturn($result);
        }

        $res = D("Sale")->select();
        $this->ajaxReturn($res);
    }
}	