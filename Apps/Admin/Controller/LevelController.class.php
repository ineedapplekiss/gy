<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class LevelController extends CommonController {

	public function index(){
		$this->display();
	}

	/**
	 * 列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function levelList(){
    	
    	$where = array(
            "status" => \Common\Model\LevelModel::STATUS_EN
            );
    	$count=D('Level')->where($where)->count();
        $info=D('Level')->where($where)->select();
    	
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
            $this->display();
        }else{
            $ck=A('CheckInput');
            $name=$ck->in('名称','name','cnennumstr','',2,20);
            $money=$ck->in('卡面金额','money','float(17,2)','',0,0);
            $rebate=$ck->in('折扣系数','rebate','float(17,2)','',0,0);

            $data = array();
            $data['name']       = $name;
            $data['money']      = $money;
            $data['rebate']     = $rebate;
            $data['add_time']   = NOW_TIME;
            $addStatus = D("Level")->add($data);
            if($addStatus){
                $result['message']='添加成功!';
                $result['status']=true;
                //log
                $msg = sprintf("设置等级%s", $name);
                action_log($this->_user, $msg); 
            }else{
                $result['message']='添加失败!';
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
        }

    }

    /**
     * 编辑
     *@author shang
     *
     */
    public function edit($id){

        if(!IS_POST){
            $this->assign("info", json_encode(D("Level")->find($id)));
            $this->display();        
        }else{
            $map['id']=(int)$id;

            $ck=A('CheckInput');
            $name=$ck->in('名称','name','cnennumstr','',2,20);
            $money=$ck->in('卡面金额','money','float(17,2)','',0,0);
            $rebate=$ck->in('折扣系数','rebate','float(17,2)','',0,0);

            if(empty($map['id'])){
                $return['message']='id不能为空!';
                $return['status']=false;
            }else{
                $data = array();
                $data['name']       = $name;
                $data['money']      = $money;
                $data['rebate']     = $rebate;
                $status=D('Level')->where($map)->save($data);

                if(false===$status){
                    $return['message']='编辑出错!';
                    $return['status']=false;
                }else{
                    $return['message']='编辑成功!';
                    $return['status']=true;
                    //log
                    $msg = sprintf("编辑等级(%s) %s %s", $map['id'], $data['money'], $data['rebate']);
                    action_log($this->_user, $msg); 
                }
            }
            $this->ajaxReturn($return);
        }
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
            $data['status'] = \Common\Model\LevelModel::STATUS_DIS;
            $status=D('Level')->where($map)->save($data);

			if(false===$status){
                $return['message']='删除出错!';
                $return['status']=false;
			}else{
                $return['message']='删除成功!';
                $return['status']=true;
                //log
                $msg = sprintf("删除等级(%s)", $map['id']);
                action_log($this->_user, $msg); 
			}
		}
		$this->ajaxReturn($return);
    }

}	