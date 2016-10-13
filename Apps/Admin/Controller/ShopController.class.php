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
     * 会员编辑
     * @author 黄药师 <46914685@qq.com> 20141207
     * 
     */
    public function editHandle(){
    	$member=M('member');

    	$ck=A('CheckInput');
    	$map['id']=$ck->in('会员id','get.id','intval','',1,0);	    		
		$pwd=$ck->in('密码','pwd','password','',0,16);	
		empty($pwd)?'':$data['password']=$pwd;
		$data['email']=$ck->in('邮箱','email','email','',5,32);	

		$data['nickname']=$ck->in('昵称','nickname','cnennumstr','',0,20);	
		$data['mobile']=$ck->in('手机号码','mobile','intval','',0,0);
		$data['groupid']=$ck->in('会员组id','gid','intval','',0,0);
		$data['point']=$ck->in('积分点数','pt','intval','',0,0);
		$vip=$ck->in('是否为VIP会员','vip','intval','',0,0);
		$data['islock']=$ck->in('是否启用','lk','intval','',0,0);
		$overduedate=$ck->in('会员过期时间','overduedate','date','',0,0);
		if(!empty($vip)&&!empty($overduedate)){
			$overduedate=strtotime($overduedate.' 23:59:59');
			$data['overduedate']=$overduedate;
			$data['vip']=$vip;
		}else{
			$data['vip']='0';
			$data['overduedate']='0';
		}

		if(empty($map['id'])){
			$return['message']='会员id不能为空!';
			$return['status']=false;
		}else{
			$status=$member->where($map)->save($data);
			if(false===$status){
				$return['message']='会员更新出错!';
				$return['status']=false;
			}else{
				$return['message']='会员更新成功!';
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
     * @author 黄药师 <46914685@qq.com> [20141207]
     */
    public function delHandle($id,$issystem,$M){
        if(!IS_POST||$issystem=='1') exit;
    	$map['id']=(int)$id;
    	switch ($M) {
    		case 'm':$Model='member';break;
    		case 'g':$Model='member_group';break;
    	}

		if(empty($map['id'])){
			$return['message']='id不能为空!';
			$return['status']=false;
		}else{
			$member=M($Model);
            if($M=='m') $status=$member->where($map)->delete();
			else{
                //删除会员组,把组里的会员移动到注册会员组里
                $status=$member->where($map)->delete();
                $_a=M()->table(C('DB_PREFIX').'member')->where(array('groupid'=>$id))->save(array('groupid'=>'1'));
            }
			if($status){
				$return['message']='删除成功!';
				$return['status']=true;
			}else{
				$return['message']='删除出错!';
				$return['status']=false;
			}
		}
		$this->ajaxReturn($return);
    }


    /**
     * 会员组首页
     * 
     */
    public function group(){

    	$this->display();
    }

    /**
	 * 会员组列表
	 * @author 黄药师 <46914685@qq.com> 20141209
	 * @access public
	 * @return null
	 * 
	 */
    public function groupList(){
    	$memberGroup=M('member_group');
    	$ck=A('CheckInput');
    	$page=$ck->in('当前页','page','intval','1',0,0);	
    	$rows=$ck->in('每页记录数','rows','intval','',0,0);	
    	$sort=$ck->in('排序','sort','cnennumstr','sort',0,0);
    	$order=$ck->in('规则','order','cnennumstr','desc',0,0);
    	
    	$count=$memberGroup->cache('groupList'.$page.$rows,'60')->count();
    	$info=$memberGroup->order($sort.' '.$order)->page($page,$rows)->select();

    	$data['total']=$count;
    	$data['rows']=$info;
    	$this->ajaxReturn($data);
    }

    /**
     * 添加会员组
     * @author 黄药师 <46914685@qq.com> 20141213
     * @access public
     */
    public function groupAdd(){
    		$ck=A('CheckInput');
    		$map['id']=$ck->in('会员组id','get.id','intval','',0,0);
    		
            $data['sort']=$ck->in('排序',"sort",'intval','0',0,0);
    		$data['point']=$ck->in('积分',"groupPoint",'intval','0',0,0);
    		$data['starnum']=$ck->in('星星数',"starnum",'intval','0',0,0);

    		$data['allowpost']=$ck->in('允许投稿',"allowpost",'bool','0',0,0);
    		$data['allowpostverify']=$ck->in('投稿不需审核',"allowpostverify",'bool','0',0,0);
    		$data['allowupgrade']=$ck->in('允许自助升级',"allowupgrade",'bool','0',0,0);

    		$data['allowsendmessage']=$ck->in('允许发短消息',"allowsendmessage",'bool','0',0,0);
    		$data['allowattachment']=$ck->in('允许上传附件',"allowattachment",'bool','0',0,0);
    		$data['allowsearch']=$ck->in('搜索权限',"allowsearch",'bool','0',0,0);

    		$price_d=$ck->in('包日',"price_d",'float(8,2)','',0,20);
    		$price_m=$ck->in('包月',"price_m",'float(8,2)','',0,20);
    		$price_y=$ck->in('包年',"price_y",'float(8,2)','',0,20);
           !empty($price_d)?$data['price_d']=$price_d:'';
           !empty($price_m)?$data['price_m']=$price_m:'';
           !empty($price_y)?$data['price_y']=$price_y:'';

    		$data['allowmessage']=$ck->in('最大短消息数',"allowmessage",'intval','0',0,20);
    		$data['allowpostnum']=$ck->in('日最大投稿数',"allowpostnum",'intval','0',0,20);
    		$data['usernamecolor']=$ck->in('用户名颜色',"usernamecolor",'string','',0,10);

    		$data['icon']=$ck->in('用户组图标',"icon",'string','',0,30);
    		$data['description']=$ck->in('简洁描述',"description",'string','',0,20);
			
			$return=array();
    		$memberGroup=M('member_group');

    		if(empty($map['id'])){
    			$data['name']=$ck->in('会员组','name','cnennumstr','',2,20);
    			$result=$memberGroup->add($data);
    			if($result){
					$return['message']='会员组添加成功!';
					$return['status']=true;
				}else{
					$return['message']='会员组添加出错!';
					$return['status']=false;
				}
    		}else{
    			$result=$memberGroup->where($map)->save($data);
    			if(false===$result){
					$return['message']='会员组更新出错!';
					$return['status']=false;
				}else{
					$return['message']='会员组更新成功!';
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
        $map['shop_name']=$ck->in('商铺名称','shop_name','cnennumstr','',0,0);    

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
    /**
     * ajax检查会员是否存在
     * @author 黄药师 <46914685@qq.com> [20150120]
     */
    public function checkGroup(){
        if(!IS_POST) exit;
        $ck=A('CheckInput');
        $map['name']=$ck->in('会员组','val','cnennumstr','',2,10); 

        $result=M()->table(C('DB_PREFIX').'member_group')->where($map)->field('id')->cache('member_group'.$map['name'],'60')->find(); 
        if($result) {
            $return['status']=false;
            $return['id']=$result['id'];
        }else {
            $return['status']=true;
            $return['id']='0';
        }
        $this->ajaxReturn($return);
    }
    /**
     * ajax检查邮箱是否存在
     * @author 黄药师 <46914685@qq.com> [20150120]
     */
    public function checkEmail(){
        if(!IS_POST) exit;
        $ck=A('CheckInput');
        $map['email']=$ck->in('邮箱','val','string','',5,32);    

        $result=M()->table(C('DB_PREFIX').'member')->where($map)->field('id')->cache('member'.$map['email'],'60')->find(); 
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