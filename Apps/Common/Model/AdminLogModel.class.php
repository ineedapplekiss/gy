<?php
namespace Common\Model;
use Think\Model;
/**
 * 日志
 */
class AdminLogModel extends Model
{	
	protected $tableName = 'admin_log';
	
	
	public function cate()
	{
		return array(
			array("id"=>1, "name"=>"登录/登出"),
			array("id"=>2, "name"=>"商品管理"),
			array("id"=>3, "name"=>"会员管理"),
			array("id"=>4, "name"=>"交易管理"),
			array("id"=>5, "name"=>"系统设置"),
			);
	}

	public function cate2actions($logCate)
	{
		switch ($logCate) {
			case 1:
				$arr = array(
					"Login.checkLogin",
					"Index.logout"
					);
				break;
			
			default:
				$arr = array();
				break;
		}
		return implode(",", $arr);
	}

	public function formatAction($action)
	{
		$authr = M()->table(C('DB_PREFIX').'auth_rule')->where(array('pid'=>array("neq",0)))->field('name,title')->select();//查询模块信息 
		foreach ($authr as $v) {
			list($m, $c, $f) = explode("/", $v["name"]);
			$key = $c.".".$f;
			$arr[$key] = $v["title"];
		}
		$arr["Login.checkLogin"] = "登录";
		$arr["Index.logout"] = "退出";
		return $arr[$action];
	}

}