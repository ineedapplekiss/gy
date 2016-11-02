<?php 
namespace Admin\Controller;
use Admin\Controller\CommonController;
class AdminController extends CommonController{
    //用户列表
    public function pw(){
        $this->display();
    }

    public function log(){        
        echo 2;
    }


    //更新用户
    public function pwSave(){
       if(!IS_POST) exit; 
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

}
?>