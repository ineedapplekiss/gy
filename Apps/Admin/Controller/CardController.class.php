<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class CardController extends CommonController {

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
    public function cardList(){
    	$ck=A('CheckInput');

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	

    	
    	$count=D('Card')->count();
        $info=D('Card')
            ->alias("c")
            ->field("c.*,u.username")
            ->join("left join ".C('DB_PREFIX')."auth_user as u on c.uid=u.uid")
            ->order($sort.' '.$order)->page($page,$rows)->select();

        if($count)
        {
            $ids = implode(",",array_column($info, "id"));
            $CardDetail = D("CardDetail")->field('card_id, count(1) as n')->where(array(
                "card_id" => array("in", $ids),
                "status"    => \Common\Model\CardDetailModel::STATUS_DIS
                ))->group("card_id")->select();
            $map = array();
            foreach ($CardDetail as $v) {
                $map[$v["card_id"]] = $v['n'];
            }

            foreach ($info as $k => $v) {
                $info[$k]["amount"] = $map[$v["id"]];
            }

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
     * 列表
     * @author shang
     * @access public
     * @return null
     */
    public function carddetailList(){
        $ck=A('CheckInput');

        $sort=$ck->in('排序','sort','cnennumstr','id',0,0);
        $order=$ck->in('规则','order','cnennumstr','desc',0,0);

        $page=$ck->in('当前页','page','intval','1',0,0);   
        $rows=$ck->in('每页记录数','rows','intval','',0,0);

        $cardId=$ck->in('每页记录数','cardId','intval','',0,0); 

        $map["c.card_id"] = $cardId;
        
        $count=D('CardDetail')->alias("c")->where($map)->count();
        $info=D('CardDetail')
            ->alias("c")
            ->field("c.*,u.name")
            ->join("left join ".C('DB_PREFIX')."customer as u on c.card_no=u.card_no")
            ->where($map)
            ->order($sort.' '.$order)->page($page,$rows)->select();

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
     * 添加
     *@author shang
     *
     */
    public function add(){
        if(!IS_POST){
            $this->assign('cates',D("Cate")->allCate());
            $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
            $this->display();
        }else{

            $ck=A('CheckInput');
            $activityName=$ck->in('活动名称','activity_name','cnennumstr','',2,20);
            $cardNumber=$ck->in('会员卡数量','card_number','intval','',0,0);

            if ($cardNumber < 1) {
                $result['message']='请填写会员卡数量';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }

            $data = array();
            $data['activity_name']  = $activityName;
            $data['card_number']    = $cardNumber;
            $data['add_time']       = NOW_TIME;
            $data['uid']            = $_SESSION['uid'];
            $addStatus = D("Card")->addCard($data);
            if($addStatus){
                $result['message']='添加会员卡成功!';
                $result['status']=true; 
                //log
                $msg = sprintf("添加会员卡 %s", $activityName);
                action_log($this->_user, $msg);
            }else{
                $result['message']='添加会员卡失败!';
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
            $data['status'] = $status == \Common\Model\PackageModel::STATUS_DIS ? \Common\Model\PackageModel::STATUS_EN : \Common\Model\PackageModel::STATUS_DIS;
            $status=D('Package')->where($map)->save($data);

            if(false===$status){
                $return['message']='操作出错!';
                $return['status']=false;
            }else{
                $return['message']='操作成功!';
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
            $data['status'] = \Common\Model\PackageModel::STATUS_DEL;
            $status=D('Package')->where($map)->save($data);

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