<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class CusController extends CommonController {

	public function index(){
        //仅允许查询权限下的商铺
        $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
        $this->assign('levels',D("Level")->where(array("status"=>\Common\Model\LevelModel::STATUS_EN))->select());
        $this->assign('curshopid',session('curshopid'));
        $this->assign('curshopname',session('curshopname'));
        $this->assign('card_nos',D("CardDetail")->getCs());
        $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
        $this->assign('shengs', D("Region")->getByPid(1));
        $this->assign('erate', D("Erate")->getErate());

		$this->display();
	}

	/**
	 * 列表
	 * @author shang
	 * @access public
	 * @return null
	 */
    public function cusList(){
    	$ck=A('CheckInput');

    	$sort=$ck->in('排序','sort','cnennumstr','id',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);

		$page=$ck->in('当前页','page','intval','1',0,0);
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);

        //查询条件
        $name=$ck->in('套餐名称','name','cnennumstr','',0,0);
        $card_no=$ck->in('卡号','card_no','cnennumstr','',0,0);  
        $mobile=$ck->in('手机号','mobile','cnennumstr','',0,0);

        $status=$ck->in('状态','status','intval','',0,0);
        $shopId=$ck->in('商铺','shopId','intval','',0,0);
        $level=$ck->in('等级','level','intval','',0,0);
        $cond=$ck->in('过滤条件','cond','intval','',0,0);

        $map = array();

        //仅允许查询权限下的商铺
        $shopIds = D("Shop")->getShopsByUid(session('uid'));
        if($shopId>0 && in_array($shopId, array_column($shopIds, "id")))//指定商铺
        {
            $map['c.shop_id'] = $shopId;
        }
        else
        {
            $shopIds = implode(",",array_column($shopIds, "id"));
            $map['c.shop_id'] = array('IN',$shopIds);
        }

        !empty($name)?$map['c.name']=array('like','%'.$name.'%'):'';//名称
        !empty($card_no)?$map['c.card_no']=array('like','%'.$card_no.'%'):'';//卡号
        !empty($mobile)?$map['c.mobile']=array('like','%'.$mobile.'%'):'';//手机号

        if(isset($_POST["status"]))
        {
            switch ($status) {
                case 1://启用
                    $map['c.status'] = \Common\Model\CusModel::STATUS_EN;
                    break;
                case 0://停用
                    $map['c.status'] = \Common\Model\CusModel::STATUS_DIS;
                    break;
                default:
                    $map['c.status'] = array("NEQ", \Common\Model\CusModel::STATUS_DEL);
                    break;
            }
        }
        
        $level && $map['c.level']=$level;//等级

    	
    	$count=D('Cus')->alias("c")->where($map)->count();
        $info=D('Cus')
            ->alias("c")
            ->field("c.*, s.shop_name")
            ->join("left join ".C('DB_PREFIX')."shop as s on c.shop_id=s.id")
            ->order($sort.' '.$order)->page($page.','.$rows)->where($map)->page($page,$rows)->select();

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
     * 套餐添加
     *@author shang
     *
     */
    public function add(){
        if(!IS_POST){
            $this->assign('curshopid',session('curshopid'));
            $this->assign('curshopname',session('curshopname'));
            $this->assign('card_nos',D("CardDetail")->getCs());
            $this->assign('levels',D("Level")->where(array("status"=>\Common\Model\LevelModel::STATUS_EN))->select());
            $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
            $this->assign('shengs', D("Region")->getByPid(1));
            $this->assign('erate', D("Erate")->getErate());
            $this->display();
        }else{
            if($_FILES["photo"]["name"])
            {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     3145728 ;// 设置附件上传大小
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =     './Public/Uploads/'; // 设置附件上传根目录
                $upload->savePath  =     ''; // 设置附件上传（子）目录
                // 上传文件 
                $info   =   $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    $result['message']=$upload->getError();
                    $result['status']=false;  
                    $this->ajaxReturn($result);
                }else{// 上传成功
                    $photo = $upload->rootPath.$info["photo"]["savepath"].$info["photo"]["savename"];
                }
            }
            

            //检查会员卡号
            $cards = D("CardDetail")->getCs();
            if(!in_array(I("post.card_no"), array_column($cards,"card_no")))
            {
                $result['message']='会员卡号不正确!';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }
            else
            {
                $card_no = I("post.card_no");
            }

            //检查会员等级
            if(!I("post.level_id",0,"intval"))
            {
                $result['message']='请选择会员等级!';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }

            //检查会员性别
            /**
            if(!I("post.gender",0,"intval"))
            {
                $result['message']='请选择性别!';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }
            */

            //检查会员生日
            /**
            if(!I("post.birthday"))
            {
                $result['message']='请选择生日!';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }
            */

            $ck=A('CheckInput');
            $name=$ck->in('姓名','name','cnennumstr','',2,20);
            $levelId=$ck->in('会员等级','level_id','intval','',1,20);
            $gender=$ck->in('性别','gender','intval',0,0,0);
            $idNo=$ck->in('身份证','id_no','cnennumstr','',0,0);
            $birthday=$ck->in('生日','birthday','date','',0,0);
            $mobile=$ck->in('手机','mobile','intval','',0,0);
            $address=$ck->in('联系地址','address','cnennumstr','',0,0);
            $shopId=$ck->in('商铺','shop_id','intval','',1,0);
            $jf=$ck->in('充值金额','jf','float(17,2)','',0,0);
            $pw=$ck->in('密码','pwd','cnennumstr','',6,20);

            $region = D("Region")->where(array("region_id" => array("in", I("post.sheng",0,'intval').",".I("post.shi",0,'intval').",".I("post.xian",0,'intval'))))->select();
            $address = implode("", array_column($region, "region_name")).$address;


            $data = array();
            $data['card_no']    = $card_no;
            $data['photo']      = $photo;
            $data['name']       = $name;
            $data['level_id']   = $levelId;
            $data['gender']     = $gender;
            $data['id_no']      = $idNo;
            $data['birthday']   = $birthday;
            $data['birthday_cond'] = substr($birthday,5,2).substr($birthday,-2);
            $data['mobile']     = $mobile;
            $data['shop_id']    = $shopId;
            $data['address']    = $address;
            $data['jf']         = $jf;
            $data['pw']         = $pw;
            $data['add_time']   = NOW_TIME;
            $addStatus = D("Cus")->addCus($data);
            if($addStatus){
                $result['message']='添加会员成功!';
                $result['status']=true; 
                //log
                $msg = sprintf("添加会员 %s ", $name);
                action_log($this->_user, $msg, $shopId);
            }else{
                $result['message']='添加会员失败!';
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
        }

    }

    /**
     * 会员生效失效
     *@author shang
     *
     */
    public function edit($id, $status){

        if(!IS_POST||$issystem=='1') exit;

        if(empty($id)){
            $return['message']='id不能为空!';
            $return['status']=false;
        }else{
            $ids = explode(",", $id);
            $statuss = explode(",", $status);
            foreach ($ids as $k => $v) {
                $data = $map = array();
                $map['id']=$v;
                $data['status'] = !$statuss[$k];
                $status=D('Cus')->where($map)->save($data);
            }

            if(false===$status){
                $return['message']='修改出错!';
                $return['status']=false;
            }else{
                $return['message']='修改成功!';
                $return['status']=true;
                //log
                $msg = sprintf("启用（停用）会员 %s ", $map['id']);
                action_log($this->_user, $msg);
            }
        }
        $this->ajaxReturn($return);
    }

    /**
     * 会员编辑
     *@author shang
     *
     */
    public function editCus(){

        if(!IS_POST){
            $id = I("get.id", 0, "intval");
        
            $info=D('Cus')
                ->alias("c")
                ->field("c.*, s.shop_name, l.name as l_name")
                ->join("left join ".C('DB_PREFIX')."shop as s on c.shop_id=s.id")
                ->join("left join ".C('DB_PREFIX')."c_level as l on c.level_id=l.id")
                ->where(array("c.id"=>$id))
                ->find();
            $gMap = array(
                0 => "未选",
                1 => "男",
                2 => "女"
                );
            $info["gender_map"] = $gMap[$info["gender"]];
            $this->assign('info',$info);


            $this->assign('curshopid',session('curshopid'));
            $this->assign('curshopname',session('curshopname'));
            $this->assign('card_nos',D("CardDetail")->getCs());
            $this->assign('levels',D("Level")->where(array("status"=>\Common\Model\LevelModel::STATUS_EN))->select());
            $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
            $this->assign('shengs', D("Region")->getByPid(1));
            $this->assign('erate', D("Erate")->getErate());
            $this->display();
        }else{
            $id = I("post.id", 0, "intval");
            if($_FILES["photo"]["name"])
            {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     3145728 ;// 设置附件上传大小
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =     './Public/Uploads/'; // 设置附件上传根目录
                $upload->savePath  =     ''; // 设置附件上传（子）目录
                // 上传文件 
                $info   =   $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    $result['message']=$upload->getError();
                    $result['status']=false;  
                    $this->ajaxReturn($result);
                }else{// 上传成功
                    $photo = $upload->rootPath.$info["photo"]["savepath"].$info["photo"]["savename"];
                }
            }

            $ck=A('CheckInput');
            $name=$ck->in('姓名','name','cnennumstr','',2,20);
            $gender=$ck->in('性别','gender','intval',0,0,0);
            $idNo=$ck->in('身份证','id_no','cnennumstr','',0,0);
            $birthday=$ck->in('生日','birthday','date','',0,0);
            $mobile=$ck->in('手机','mobile','intval','',0,0);
            $address=$ck->in('联系地址','address','cnennumstr','',0,0);

            $region = D("Region")->where(array("region_id" => array("in", I("post.sheng",0,'intval').",".I("post.shi",0,'intval').",".I("post.xian",0,'intval'))))->select();
            $address = implode("", array_column($region, "region_name")).$address;


            $data = array();
            if($_FILES["photo"]["name"])$data['photo']      = $photo;
            if($name)$data['name'] = $name;
            if($gender)$data['gender']     = $gender;
            if($idNo)$data['id_no']      = $idNo;
            if($birthday)
            {
                $data['birthday']   = $birthday;
                $data['birthday_cond'] = substr($birthday,5,2).substr($birthday,-2);
            }
            if($mobile)$data['mobile']     = $mobile;
            if($address)$data['address']    = $address;
            $status = D("Cus")->where(array("id"=>$id))->save($data);
            if($status){
                $result['message']='编辑会员成功!';
                $result['status']=true; 
            }else{
                $result['message']='添加会员失败!';
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
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
            $data['status'] = \Common\Model\CusModel::STATUS_DEL;
            $status=D('Cus')->where($map)->save($data);

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
     * 获取省市县联动菜单数据
     *@author shang
     *
     */
    public function apiCity(){
        $pid = I("post.pid") ? I("post.pid") : (I("get.pid") ? I("get.pid") : 1);
        $res = D("Region")->getByPid($pid);
        $this->ajaxReturn($res);
    }

    /**
     * 会员详细
     *@author shang
     *
     */
    public function detail(){
        $id = I("get.id", 0, "intval");
        
        $info=D('Cus')
            ->alias("c")
            ->field("c.*, s.shop_name, l.name as l_name")
            ->join("left join ".C('DB_PREFIX')."shop as s on c.shop_id=s.id")
            ->join("left join ".C('DB_PREFIX')."c_level as l on c.level_id=l.id")
            ->where(array("c.id"=>$id))
            ->find();
        $gMap = array(
            0 => "未选",
            1 => "男",
            2 => "女"
            );
        $info["gender"] = $gMap[$info["gender"]];
        $this->assign('levels',D("Level")->where(array("status"=>\Common\Model\LevelModel::STATUS_EN))->select());
        $this->assign('card_nos',D("CardDetail")->getCs());
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 会员订单详细
     *@author shang
     *
     */
    public function balanceChange(){
        if(!IS_POST)
        {
            $id = I("get.id",0,"intval");
            $this->assign('id',$id);
            $this->display();
        }
        else
        {
            $id = I("get.id",0,"intval");

            $map["o.c_id"] = $id;
            //不显示错误订单
            $map['o.status']= array("neq", \Common\Model\OrderModel::STATUS_DIS);

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
                ->order('o.add_time desc')->where($map)->select();

            if($count)
            {
                $odInfo = D("OrderDetail")->where(array("order_id"=>array("in", implode(",", array_column($info, "id")))))->select();

                $map = array();
                foreach ($odInfo as $v) {
                    $map[$v["order_id"]][] = sprintf("%s(%s)", $v["name"], $v["type"]==1 ? "商品" : "套餐");
                }

                //格式化输出
                foreach ($info as $k => $v) {
                    $info[$k]['order_info'] = implode("+",$map[$v["id"]]);
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
        
    }

    //换卡
    public function changeCard()
    {
        if(!IS_POST) exit;

        $ck=A('CheckInput');
        $cid=$ck->in('cid','cid','intval','',1,40);
        $card_no=$ck->in('新卡号','card_no','cnennumstr','',1,40);
        $old_card_no=$ck->in('旧卡号','old_card_no','cnennumstr','',1,40);

        $status = D("Cus")->changeCard($cid, $old_card_no, $card_no);
        if(false===$status){
            $return['message']=D("Cus")->getError();
            $return['status']=false;
        }else{
            $return['message']='修改成功!';
            $return['status']=true;
        }
        $this->ajaxReturn($return);

    }

    //充值
    public function recharge()
    {
        if(!IS_POST) exit;

        $ck=A('CheckInput');
        $cid=$ck->in('cid','cid','intval','',2,20);
        $jf=$ck->in('充值金额','jf','float(17,2)','',0,0);
        if($jf<=0)
        {
            $return['message']="请输入充值金额";
            $return['status']=false;
            $this->ajaxReturn($return);
        }
        //充值
        $status = D("Recharge")->doRecharge($cid, $jf);

        if(false===$status){
            $return['message']=D("Recharge")->getError();
            $return['status']=false;
        }else{
            $return['message']='修改成功!';
            $return['status']=true;
        }
        $this->ajaxReturn($return);

    }

    //改密码
    public function pw()
    {
        if(!IS_POST) exit;

        $ck=A('CheckInput');
        $cid=$ck->in('cid','cid','intval','',2,20);
        $pwd=$ck->in('新密码','pwd','cnennumstr','',1,20);
        $opwd=$ck->in('旧密码','opwd','cnennumstr','',1,20);

        $status = D("Cus")->where(array("id"=>$cid, "pw"=>$opwd))->save(array("pw"=>$pwd));
        if(false===$status){
            $return['message']='修改失败!';
            $return['status']=false;
        }else{
            $return['message']='修改成功!';
            $return['status']=true;
        }
        $this->ajaxReturn($return);
    }

    //升级
    public function level()
    {
        if(!IS_POST) exit;

        $ck=A('CheckInput');
        $cid=$ck->in('cid','cid','intval','',2,20);
        $level=$ck->in('等级','level','intval','',1,20);

        $status = D("Cus")->where(array("id"=>$cid))->save(array("level_id"=>$level));
        if(false===$status){
            $return['message']='修改失败!';
            $return['status']=false;
        }else{
            $return['message']='修改成功!';
            $return['status']=true;
        }
        $this->ajaxReturn($return);
    }

}	