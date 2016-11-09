<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ErateController extends CommonController {

    /**
     * 汇率维护
     *@author shang
     *
     */
    public function index(){
        if(!IS_POST){
            $this->assign('erate',D("Erate")->getErate());
            $this->display();
        }else{

            $ck=A('CheckInput');
            $rate=$ck->in('汇率','rate','float(17,2)','',0,0);
            $res = D("Erate")->setErate($rate);
            if($res){
                $result['message']='设置汇率成功!';
                $result['status']=true; 
                //log
                $msg = sprintf("设置汇率 %s ", $rate);
                action_log($this->_user, $msg);
            }else{
                $result['message']='设置汇率失败!';
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
        }

    }

}	