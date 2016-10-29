<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class SaleController extends CommonController {

	public function index(){
        //仅允许查询权限下的商铺
        $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
		$this->display();
	}

	/**
	 * 列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function saleList(){
    	$ck=A('CheckInput');

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	

        //查询条件
        $name=$ck->in('促销名称','name','cnennumstr','',0,0);     
        $status=$ck->in('状态','status','intval','',0,0);
        $shopId=$ck->in('商铺','shopId','intval','',0,0);

        $map = array();

        //仅允许查询权限下的商铺
        $shopIds = D("Shop")->getShopsByUid(session('uid'));
        if($shopId>0 && in_array($shopId, array_column($shopIds, "id")))//指定商铺
        {
            $map['p.shop_id'] = $shopId;
        }
        else
        {
            $shopIds = implode(",",array_column($shopIds, "id"));
            $map['p.shop_id'] = array('IN',$shopIds);
        }

        !empty($name)?$map['p.name']=array('like','%'.$name.'%'):'';//指定套餐名称
        switch ($status) {
            case 1://进行中
                $map['p.status'] = \Common\Model\PackageModel::STATUS_EN;
                $map['p.sta_time'] = array("ELT",NOW_TIME);
                $map['p.end_time'] = array("GT",NOW_TIME);
                break;
            case 2://定时
                $map['p.status'] = array("NEQ", \Common\Model\PackageModel::STATUS_DEL);
                $map['p.sta_time'] = array("GT",NOW_TIME);
                break;
            case 3://失效
                $map['p.status'] = array("NEQ", \Common\Model\PackageModel::STATUS_DEL);
                $map['p.end_time'] = array("ELT",NOW_TIME);
                break;
            case 4://暂停
                $map['p.status'] = \Common\Model\PackageModel::STATUS_DIS;
                $map['p.sta_time'] = array("ELT",NOW_TIME);
                $map['p.end_time'] = array("GT",NOW_TIME);
                break;
            default:
                $map['p.status'] = array("NEQ", \Common\Model\PackageModel::STATUS_DEL);
                break;
        }

    	
    	$count=D('Sale')->alias("p")->where($map)->count();
        $info=D('Sale')
            ->alias("p")
            ->field("p.id, p.name, p.type, p.shop_id, p.price, p.full, p.cut, p.rule_gender, p.rule_age, p.sta_time, p.end_time, p.add_time, p.update_time, p.status, s.shop_name")
            ->join("left join ".C('DB_PREFIX')."shop as s on p.shop_id=s.id")
            ->order($sort.' '.$order)->page($page.','.$rows)->where($map)->page($page,$rows)->select();

        //格式化输出
        foreach ($info as $k => $v) {
            $info[$k]['logic_status'] = D('Sale')->getStatus($v["status"], $v["sta_time"], $v["end_time"]);
            $info[$k]['logic_status'] = D('Sale')->formatStatus($info[$k]['logic_status']);
            $info[$k]['setime'] = date("Y-m-d H:i:s", $v["sta_time"]) . "~" . date("Y-m-d H:i:s", $v["end_time"]);
            $info[$k]['type'] = D('Sale')->formatType($info[$k]['type']);
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
     * 促销添加
     *@author shang
     *
     */
    public function add(){
        if(!IS_POST){
            $this->assign('cates',D("Cate")->allCate());
            $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
            $this->display();
        }else{

            //检查格式
            if(I('post.age') && !preg_match('/^\d{1,2}-\d{1,2}$/', I('post.age'))){
                $return['message']='年龄限制格式不正确!';
                $return['status']=false;
                $this->ajaxReturn($return);
            }
            if(!preg_match('/^[0-9,]+$/', I('post.goodsid'))){
                $return['message']='请选择商品!';
                $return['status']=false;
                $this->ajaxReturn($return);
            }
            if((I('post.stime') || I('post.etime')) && strtotime(I('post.etime')) < strtotime(I('post.stime'))){
                $return['message']='促销结束时间需要大于开始时间';
                $return['status']=false;
                $this->ajaxReturn($return);
            }
            if(in_array(I('post.type'), array(1,2)) && empty(I('post.price')))
            {
                $return['message']='请填写价格或者折扣';
                $return['status']=false;
                $this->ajaxReturn($return);
            }
            if(in_array(I('post.type'), array(3,4)) && empty(I('post.cut')))
            {
                $return['message']='请填写价格或者折扣';
                $return['status']=false;
                $this->ajaxReturn($return);
            }


            $ck=A('CheckInput');
            $name=$ck->in('促销名称','name','cnennumstr','',2,20);
            $type=$ck->in('活动类型','type','intval','',1,20);
            $shopId=$ck->in('商铺','shopId','intval','',0,0);
            $price=$ck->in('价格（折扣）','price','float(17,2)','0.00',0,0);
            $full=$ck->in('满','full','float(17,2)','0.00',0,0);
            $cut=$ck->in('减价格（折扣）','cut','float(17,2)','0.00',0,0);
            $gender=$ck->in('性别限制','gender','intval','',0,0);
            $age=I('post.age');
            $stime=$ck->in('开始时间','stime','datetime','',0,0);
            $etime=$ck->in('结束时间','etime','datetime','',0,0);
            $goodsIds=I('post.goodsid');

            $goods_info = D("Goods")->where(array("id"=>array("in",$goodsIds)))->select();

            $data = array();
            $data['name']       = $name;
            $data['type']       = $type;
            $data['shop_id']    = $shopId;
            $data['price']      = $price;
            $data['full']       = $full;
            $data['cut']        = $cut;
            $data['rule_gender']= $gender;
            $data['rule_age']   = $age;
            $data['sta_time']   = strtotime($stime);
            $data['end_time']   = strtotime($etime);
            $data['add_time']   = NOW_TIME;
            $data['goods_info'] = $goods_info;
            $addStatus = D("Sale")->addSale($data);
            if($addStatus){
                $result['message']='添加促销成功!';
                $result['status']=true; 
            }else{
                $result['message']='添加促销失败!'.D("Sale")->error;
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
        }

    }

    /**
     * 套餐启用停用
     *@author shang
     *
     */
    public function edit($id, $status){

        if(!IS_POST||$issystem=='1') exit;
        $map['id']=(int)$id;

        if(empty($map['id'])){
            $return['message']='id不能为空!';
            $return['status']=false;
        }else{
            $data = array();
            $data['status'] = $status == \Common\Model\SaleModel::STATUS_DIS ? \Common\Model\SaleModel::STATUS_EN : \Common\Model\SaleModel::STATUS_DIS;
            $status=D('Sale')->where($map)->save($data);

            if(false===$status){
                $return['message']='修改出错!';
                $return['status']=false;
            }else{
                $return['message']='修改成功!';
                $return['status']=true;
            }
        }
        $this->ajaxReturn($return);
    }

    /**
     * 删除
     * @param $id 
     * @param $M 模型 
     * 
     * @author shang
     */
    public function delHandle($id,$M){
        if(!IS_POST||$issystem=='1') exit;
    	$map['id']=(int)$id;

		if(empty($map['id'])){
			$return['message']='id不能为空!';
			$return['status']=false;
		}else{
            $data = array();
            $data['status'] = \Common\Model\SaleModel::STATUS_DEL;
            $status=D('Sale')->where($map)->save($data);

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

}	