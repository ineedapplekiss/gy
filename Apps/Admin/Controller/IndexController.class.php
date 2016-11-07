<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class IndexController extends CommonController {
    public function index(){
    	$this->assign('webName','会员交易管理系统');

        $auth=new \Think\Auth();
        //获取当前uid所在的角色组id
        $groups=$auth->getGroups(session('uid'));
        $ret = C("MENU");
        foreach ($ret as $mk => $menu)
        {
            foreach ($menu['list'] as $lk => $item)
            {
                $check_url = $item['module'] ? $item['url'] : 'Admin/'.$item['url'];
                if(!$item['url'] or !authCheck($check_url,session('uid')))
                {
                    unset($ret[$mk]['list'][$lk]);
                }
            }
        }
    	$this->assign('menus',$ret);
        $this->display();
    }

    public function shopSelect()
    {
    	$id=(int)I('id');//商铺id
        $shop_name=I('shop_name');//商铺名称
    	session('curshopid',$id);
    	session('curshopname',$shop_name);
    	$result = true;
    	if(false===$result){
            $return['message']='权限设置失败!';
            $return['status']=false;  
        }else{
            $return['message']='权限设置成功!';
            $return['status']=true;
        }        
        $this->ajaxReturn($return);
    }

    //退出
	public function logout(){
        action_log($this->_user, "用户退出");
		unset($_SESSION['username']);
		unset($_SESSION['uid']);
		unset($_SESSION['gid']);
		unset($_SESSION['gname']);

		//add by shang
		unset($_SESSION['curshopid']);
		unset($_SESSION['curshopname']);

		$return=array();
		$return['message']='退出成功！';
		$return['status']=true;
		$this->ajaxReturn($return);
	}

	public function error(){
		$this->display();
	}

}