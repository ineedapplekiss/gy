<?php 
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	protected $_user;
	
	public function _initialize(){

       	if(CONTROLLER_NAME != "Index")layout('Common/layout');//add layout
		//验证登陆,没有登陆则跳转到登陆页面
		if(empty($_SESSION['username']))$this->redirect('Admin/Login/index');

		//权限验证		
		if(!authCheck(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME,session('uid'))){
			header('HTTP/1.1 404 Not Found');
			$return['message']='你没有权限';
			$return['status']=false;
			$this->ajaxReturn($return);			
		}

		//配置用户信息
		$this->setUserInfo();
		
	}

    protected function _empty(){
        //$this->error('你请求的页面不存在!!');
        echo "<script>$.messager.alert('错误提示','你请求的页面不存在!!','error');</script>";
    }

    /**
	 * 配置用户信息
	 */
	public function setUserInfo(){
	    $this->_user['uid'] = session('uid');
	    $this->_user['user_name'] =session('username');
	    $this->_user['ip'] = get_client_ip();
	}

	public function csvHeader()
	{
        header("Content-Type: application/vnd.ms-excel; charset=GB2312");
        header("Pragma: public");   
        header("Expires: 0");   
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");   
        header("Content-Type: application/force-download");   
        header("Content-Type: application/octet-stream");   
        header("Content-Type: application/download");   
        header("Content-Disposition: attachment;filename=".date("Ymd_His").".csv");   
        header("Content-Transfer-Encoding: binary ");
	}
}
?>