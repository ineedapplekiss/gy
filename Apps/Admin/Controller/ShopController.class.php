<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ShopController extends CommonController {

	public function index(){
		$shop=M('shop');
		$shopInfo=$shop->order('update_time desc')->select();
		$this->assign('shopInfo',$shopInfo);
		$this->display();
	}

	/**
	 * 商铺列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function shopList(){
    	$shop=M('shop');
    	$ck=A('CheckInput');
    	//搜索
        $name='';
        $email='';
        $mobile='';
    	$name=$ck->in('店铺名','shop_name','cnennumstr','',0,0);    	
	    $mobile=$ck->in('手机','link_tel','intval','',0,0);

    	!empty($name)?$map['shop_name']=array('like','%'.$name.'%'):'';
    	!empty($mobile)?$map['link_tel']=$mobile:'';
        $map['status'] = \Common\Model\ShopModel::STATUS_NORMAL;

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	
    	
    	$count=$shop->where($map)->cache('shopList'.$name.$mobile,'60')->count();
        $info=$shop->alias('M')->order($sort.' '.$order)->page($page.','.$rows)->where($map)->page($page,$rows)->select();
    	
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
     * 商铺添加
     *@author shang
     *
     */
    public function addHandle(){
    	$shop=M('shop');

    	$ck=A('CheckInput');
		$data['shop_name']=$ck->in('店铺名称','shop_name','cnennumstr','',4,20);	
		$data['link_user']=$ck->in('联系人','link_user','cnennumstr','',4,16);	
		$data['link_tel']=$ck->in('联系电话','link_tel','intval','',1,11);
		$data['address']=$ck->in('地址','address','cnennumstr','',5,32);
		$data['wx']=$ck->in('微信','wx','ennumstr','',5,32); 
		$data['add_time']=NOW_TIME;

		$addStatus=$shop->add($data);
		if($addStatus){
	    	$result['message']='添加商铺成功!';
	    	$result['status']=true;	
		}else{
			$result['message']='添加商铺失败!';
	    	$result['status']=false;	
		}
    	$this->ajaxReturn($result);
    }

    /**
     * 店铺编辑
     * @author shang
     * 
     */
    public function editHandle(){
    	$shop=M('shop');

    	$ck=A('CheckInput');
    	$map['id']=$ck->in('店铺id','get.id','intval','',1,0);

        $data['shop_name']=$ck->in('店铺名称','shop_name','cnennumstr','',4,20);    
        $data['link_user']=$ck->in('联系人','link_user','cnennumstr','',4,16); 
        $data['link_tel']=$ck->in('联系电话','link_tel','intval','',4,11);
        $data['address']=$ck->in('地址','address','cnennumstr','',5,32);
        $data['wx']=$ck->in('微信','wx','ennumstr','',5,32); 


		if(empty($map['id'])){
			$return['message']='店铺id不能为空!';
			$return['status']=false;
		}else{
			$status=$shop->where($map)->save($data);
			if(false===$status){
				$return['message']='店铺更新出错!';
				$return['status']=false;
			}else{
				$return['message']='店铺更新成功!';
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
    	switch ($M) {
    		case 'm':$Model='shop';break;
    		case 'g':$Model='member_group';break;
    	}

		if(empty($map['id'])){
			$return['message']='id不能为空!';
			$return['status']=false;
		}else{
			$member=M($Model);
            if($M=='m')
            {
                $data = array();
                $data['status'] = \Common\Model\ShopModel::STATUS_DEL;
                $status=$member->where($map)->save($data);
            }
			else{
                //删除会员组,把组里的会员移动到注册会员组里
                $status=$member->where($map)->delete();
                $_a=M()->table(C('DB_PREFIX').'member')->where(array('groupid'=>$id))->save(array('groupid'=>'1'));
            }
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


    /**
     * ajax检查商铺是否存在
     * @author shang
     */
    public function checkShop(){
        if(!IS_POST) exit;
        $ck=A('CheckInput');
        $map['shop_name'] = $ck->in('商铺名称','val','cnennumstr','',0,0);
        $map['status'] = \Common\Model\ShopModel::STATUS_NORMAL;

        $result=M()->table(C('DB_PREFIX').'shop')->where($map)->field('id')->cache('shop'.$map['shop_name'],'60')->find(); 
        if($result) {
            $return['status']=false;
            $return['id']=$result['id'];
        }else {
            $return['status']=true;
            $return['id']='0';
        }
        $this->ajaxReturn($return);
    }
}	