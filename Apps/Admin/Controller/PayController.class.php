<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class PayController extends CommonController {

	public function index(){
        
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
            if(!I("post.gender",0,"intval"))
            {
                $result['message']='请选择性别!';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }

            //检查会员生日
            if(!I("post.birthday"))
            {
                $result['message']='请选择生日!';
                $result['status']=false;  
                $this->ajaxReturn($result);
            }

            $ck=A('CheckInput');
            $name=$ck->in('姓名','name','cnennumstr','',2,20);
            $levelId=$ck->in('会员等级','level_id','intval','',1,20);
            $gender=$ck->in('性别','gender','intval','',1,20);
            $idNo=$ck->in('身份证','id_no','cnennumstr','',16,18);
            $birthday=$ck->in('生日','birthday','date','',0,0);
            $mobile=$ck->in('手机','mobile','intval','',11,11);
            $address=$ck->in('联系地址','address','cnennumstr','',2,20);
            $shopId=$ck->in('商铺','shop_id','intval','',0,0);
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
            }else{
                $result['message']='添加会员失败!';
                $result['status']=false;    
            }
            $this->ajaxReturn($result);
        }
    }
}	