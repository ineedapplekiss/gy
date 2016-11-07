<?php 
namespace Admin\Controller;
use Admin\Controller\CommonController;
class AdminController extends CommonController{
    //用户列表
    public function pw(){
        if(IS_POST)
        {
            $ck=A('CheckInput');
            $opwd=$ck->in('密码','opwd','password','',6,16); 
            $pwd=$ck->in('密码','pwd','password','',6,16);
            $data['password']=$pwd;

            $map["uid"] = session("uid"); 
            $map["password"] = $opwd; 

            $result=M()->table(C('DB_PREFIX').'auth_user')->where($map)->save($data);
            if(!$result){
                $return['message']='更新用户失败';
                $return['status']=false;
                $this->ajaxReturn($return);
            }else{
                $return['message']='更新用户成功!';
                $return['status']=true; 
                $this->ajaxReturn($return);
            }
        }
        else
        {
            $this->display();
        }
    }

    public function log(){        
        if(IS_POST)
        {
            $ck=A('CheckInput');

            $sort=$ck->in('排序','sort','cnennumstr','id',0,0);
            $order=$ck->in('规则','order','cnennumstr','desc',0,0);

            $page=$ck->in('当前页','page','intval','1',0,0);   
            $rows=$ck->in('每页记录数','rows','intval','',0,0);  

            //查询条件
            $user_name=$ck->in('用户名','user_name','cnennumstr','',0,0);     
            $logCate=$ck->in('类别','logCate','intval','',0,0);
            $shopId=$ck->in('商铺','shopId','intval','',0,0);
            $stime=$ck->in('开始时间','stime','datetime','',0,0);
            $etime=$ck->in('结束时间','etime','datetime','',0,0);

            $map = array();

            //仅允许查询权限下的商铺
            $shopIds = D("Shop")->getShopsByUid(session('uid'));
            if($shopId>0 && in_array($shopId, array_column($shopIds, "id")))//指定商铺
            {
                $map['al.act_id'] = $shopId;
            }
            else
            {
                $shopIds = implode(",",array_column($shopIds, "id"));
            }

            !empty($stime) && $map['al.create_time']= array("gt", strtotime($stime));
            !empty($etime) && $map['al.create_time']= array("lt", strtotime($etime));
            !empty($stime) && !empty($etime) && $map['o.create_time']= array("between", array(strtotime($stime), strtotime($etime)));

            !empty($user_name)?$map['al.user_name']=array('like','%'.$user_name.'%'):'';
            if($logCate)
            {
                $map["al.action_code"] = array("in", D("AdminLog")->cate2actions($logCate));
            }


            $count=D('AdminLog')->alias("al")->where($map)->count();
            $info=D('AdminLog')
                ->alias("al")
                ->field("al.*, s.shop_name")
                ->join("left join ".C('DB_PREFIX')."shop as s on al.act_id=s.id")
                ->order($sort.' '.$order)->page($page.','.$rows)->where($map)->select();
            
            //格式化输出
            if($count)
            foreach ($info as $k => $v) {
                $info[$k]['action_code'] = D('AdminLog')->formatAction($v['action_code']);
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
        else
        {
            //仅允许查询权限下的商铺
            $this->assign('shops',D("Shop")->getShopsByUid(session('uid')));
            
            $this->assign('logCate',D("AdminLog")->cate());
            $this->display();
        }
    }

    public function test()
    {
        echo D("AdminLog")->formatAction("Login.checkLogin");
    }
    

}
?>