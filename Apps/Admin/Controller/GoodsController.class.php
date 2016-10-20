<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class GoodsController extends CommonController {

	public function index(){
		$this->display();
	}

	/**
	 * 列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function goodsList(){
    	$ck=A('CheckInput');
        $map['status'] = \Common\Model\GoodsModel::STATUS_NORMAL;

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	
    	
    	$count=D('Goods')->where($map)->cache('cateList','60')->count();
        $info=D('Goods')->order($sort.' '.$order)->page($page.','.$rows)->where($map)->page($page,$rows)->select();
    	
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
     * 商品添加
     *@author shang
     *
     */
    public function add(){
        if(!IS_POST){
            $this->assign('cates',D("Cate")->allCate());
            //$this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
            $this->assign('shops',D("Shop")->getShopsByUid(2));
            $this->display();
        }else{
            $shopIds = I("post.shop_ids");
            $cateId = I("post.cate_id");
            if(!$shopIds || !is_array($shopIds))
            {
                $result['message']='请选择商铺';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }

            if(!$cateId)
            {
                $result['message']='请选择分类';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }

            $ck=A('CheckInput');
            $goodsName=$ck->in('商品名称','name','cnennumstr','',2,20);  
            $goodsCode=$ck->in('商品代码','code','cnennumstr','0',2,20);
            $goodsPrice=$ck->in('商品价格','price','float(17,2)','',0,0);
            $data['add_time']=NOW_TIME;
            $data = array();

            foreach ($shopIds as $shopId) {
                $data[] = array(
                    "shop_id"   => $shopId,
                    "name"      => $goodsName,
                    "code"      => $goodsCode,
                    "cate_id"   => $cateId,
                    "price"     => $goodsPrice,
                    "add_time"  => NOW_TIME
                    );
            }
            $addStatus = D("Goods")->addAll($data);
            if($addStatus){
                $result['message']='添加分类成功!';
                $result['status']=true; 
            }else{
                $result['message']='添加分类失败!';
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
        }

    }

    /**
     * 商品编辑
     *@author shang
     *
     */
    public function edit($id){

        if(!IS_POST){
        
            $map['id'] = intval($id);
        
            $cont = D('Agent')->where($map)->find();
        
            $this->assign('cont',json_encode($cont));
        
            $this->display();
        }else{
        
            $model = D('Agent');
        
            $data = $model->create();
             
            $map['id'] = $data['id'];
             
            $insertId = $model->save($data,$map);
        
            action_log($this->_user, '修改地理人：名字'.$data['name']);
             
            if($insertId){
                 
                $return['status']=true;
        
                $return['message']='修改成功！';
                 
            }else{
                 
                $return['status']=false;
        
                $return['message']='修改失败！';
                 
            }
             
            $this->ajaxReturn($return);
        }
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