<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class CateController extends CommonController {

	public function index(){
		// $cate=D('cate');
		// $cateInfo=$cate->order('add_time desc')->select();
		// $this->assign('cateInfo',$cateInfo);
		$this->display();
	}

	/**
	 * 分类列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function cateList(){
    	$cate=D('Cate');
    	$ck=A('CheckInput');
    	
        $map['status'] = \Common\Model\CateModel::STATUS_NORMAL;

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	
    	
    	$count=$cate->where($map)->cache('cateList','60')->count();
        $info=$cate->order($sort.' '.$order)->page($page.','.$rows)->where($map)->page($page,$rows)->select();
    	
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
     * 分类添加
     *@author shang
     *
     */
    public function addHandle(){
    	$cate=D('Cate');

    	$ck=A('CheckInput');
		$data['name']=$ck->in('分类名称','name','cnennumstr','',2,20);	
		$data['shop_id']=$ck->in('所属商铺','shop_id','intval','0',0,16);
		$data['add_time']=NOW_TIME;

		$addStatus=$cate->add($data);
		if($addStatus){
	    	$result['message']='添加分类成功!';
	    	$result['status']=true;	
            //log
            $msg = sprintf("添加分类 %s ", $data['name']);
            action_log($this->_user, $msg);
		}else{
			$result['message']='添加分类失败!';
	    	$result['status']=false;	
		}
    	$this->ajaxReturn($result);
    }

    /**
     * 分类编辑
     * @author shang
     * 
     */
    public function editHandle(){
    	$cate=D('Cate');

    	$ck=A('CheckInput');
    	$map['id']=$ck->in('分类id','get.id','intval','',1,0);

        $data['name']=$ck->in('分类名称','name','cnennumstr','',2,20);  
        $data['shop_id']=$ck->in('所属商铺','shop_id','intval','',1,16);


		if(empty($map['id'])){
			$return['message']='分类id不能为空!';
			$return['status']=false;
		}else{
			$status=$cate->where($map)->save($data);
			if(false===$status){
				$return['message']='分类更新出错!';
				$return['status']=false;
			}else{
				$return['message']='分类更新成功!';
				$return['status']=true;
                //log
                $msg = sprintf("分类id %s 更新： %s ", $map['id'], $data['name']);
                action_log($this->_user, $msg);
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
			$cate=D('Cate');
            $data = array();
            $data['status'] = \Common\Model\CateModel::STATUS_DEL;
            $status=$cate->where($map)->save($data);

			if(false===$status){
                $return['message']='删除出错!';
                $return['status']=false;
			}else{
                $return['message']='删除成功!';
                $return['status']=true;
                //log
                $msg = sprintf("分类id %s 删除", $map['id']);
                action_log($this->_user, $msg);
			}
		}
		$this->ajaxReturn($return);
    }


    /**
     * ajax检查分类是否存在
     * @author shang
     */
    public function checkCate(){
        if(!IS_POST) exit;
        $ck=A('CheckInput');
        $map['name'] = $ck->in('分类名称','val','cnennumstr','',2,20);  
        $map['status'] = \Common\Model\CateModel::STATUS_NORMAL;  

        $result= D('Cate')->where($map)->field('id')->cache('cate'.$map['name'],'60')->find(); 
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