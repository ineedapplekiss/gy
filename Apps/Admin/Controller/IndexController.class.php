<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class IndexController extends CommonController {
    public function index(){
    	$this->assign('webName','会员交易管理系统');
    	$this->assign('menus',C("MENU"));
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