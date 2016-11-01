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
    public function userSave(){
       if(!IS_POST) exit; 
        $ck=A('CheckInput');     
        $pwd=$ck->in('密码','pwd','password','',0,16);
        !empty($pwd)?$data['password']=$pwd:''; 
        $data['realname']=$ck->in('姓名','realname','cnennumstr','',0,16); 
        $data['email']=$ck->in('邮箱','email','string','',0,32); 
        $data['score']=$ck->in('积分','score','intval','',0,0); 
        $map['uid']=$ck->in('用户id','uid','intval','',1,0); 
        $g['group_id']=$ck->in('会员组id','groupId','intval','',1,0); 

        $result=M()->table(C('DB_PREFIX').'auth_user')->where($map)->save($data);
        if(false===$result){
            $return['message']='更新用户失败';
            $return['status']=false;
            $this->ajaxReturn($return);
        }else{
            if($map['uid']!='1'){//非超级管理员
                $gid=M()->table(C('DB_PREFIX').'auth_group_access')->where($map)->save($g);
                if(false===$gid){
                   $return['message']='更新用户明细表出错!';
                   $return['status']=false; 
                   $this->ajaxReturn($return);
                }
            }
        }
        $return['message']='更新用户成功!';
        $return['status']=true; 
        $this->ajaxReturn($return);
    }

}
?>