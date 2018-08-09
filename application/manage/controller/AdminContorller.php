<?php
namespace app\manage\controller;
namespace app\manage\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
use app\manage\model\Menu;
class AdminContorller extends author
{
	/*分组管理*/
	public function property()
	{
		return $this->fetch("manage/admin/adminGroup");
	}
	// 调取分组管理数据
	public function getadmingrouplist()
	{
		 //$res=User::getadmingrouplist();
		 $data = DB::name('group')->select();
		  $count= DB::name('group')->count();
		if($data){
			//jsonMsg("success","0",$data,$count);
			$arr=json_encode(
				array(
					'code'=>0,
					'msg'=>'',
					'count'=>$count,
					'data'=>$data
				)
			);
			echo $arr;
		}else{
			jsonMsg('暂时没有内容',1);
		}
	}
	// 添加分组页面
	public function showaddgroup()
	{
		return $this->fetch("manage/admin/adminGroup");
	}
	// 添加功能方法
	public function addgroup(Request $request)
	{
		$param = $request->param();
		    $data = DB::name('menu')->field('id,title,parentid,icon')->where(['type'=>1])->order('menuid asc')->select();
		    $menu=Menu::getMenuId($param['uid']);
		    $menu=array_column($menu,'menu_id');
		    foreach($data as $k=>$v){
		        if(in_array($v['id'],$menu)){
		            $data[$k]['check']='on';
		        }else{
		            $data[$k]['check']='off';
		        }
		    }
		    $result = tree($data);
		    $this->assign("list",$result);
		    $this->assign('uid',$param['uid']);
		return $this->fetch("manage/admin/addgroup");
	}
	public function addgroupList(Request $request)
	{
		$param=$request->param();
	}
	public function groupList()
	{
		$rts=Db::name('menu')->select();
		$List=array();
		$i=0;
		foreach($rts as $k=>$v){
			if($v['id']!=6){
				$List[$i]['id']=$v['id'];
				$List[$i]['pId']=$v['parentid'];
				$List[$i]['name']=$v['title'];
				if($v['parentid']==0){
					$List[$i]['open']=true;
				}
				$i++;
			}
		}
		$List=json_encode($List);
		jsonMsg("success","0",$List);
	}
	public function powermenu(Request $request){//显示菜单管理界面
	    $param = $request->param();
	    $data = DB::name('menu')->field('id,title,parentid,icon')->where(['type'=>1])->order('menuid asc')->select();
	    $menu=DB::name('user_group')->field('menu_id')->where(['groupid'=>$param['id']])->select();
	    $menu=array_column($menu,'menu_id');
	    foreach($data as $k=>$v){
	        if(in_array($v['id'],$menu)){
	            $data[$k]['check']='on';
	        }else{
	            $data[$k]['check']='off';
	        }
	    }
	    $result = tree($data);
	    $this->assign("list",$result);
	    $this->assign('groupid',$param['id']);
	    return $this->fetch('manage/power/menulist');
	}

	public function grouppower(Request $request){
	    $param = $request->param();
	    $arr=array();
	    foreach($param['menu'] as $k=>$v)
	    {
	        $arr[$k]['menu_id']=$v;
	        $arr[$k]['groupid']=$param['groupid'];
	    }
	    $where['groupid']=$param['groupid'];
	    DB::name('user_group')->where($where)->delete();
	    $rs=DB::name('user_group')->insertAll($arr);
	    if($rs){jsonMsg("成功",0);}else{jsonMsg("失败",1);}
	}
	public function powerid(Request $request){
		$param = $request->param();
		$data['title']=$param['name'];
		$data['time']=time();
		if(isset($param['id'])){
			$data['id']=$param['id'];
			$res=Db::name('group')->update($data);
		}else{
			$res=Db::name('group')->insert($data);
		}
	
		if($res){
			jsonMsg("成功",0);
		}else{
			jsonMsg("失败",1);
		}	
	}
	// 后台 管理员 列表页面
	public function adminList()
	{
		return $this->fetch("manage/admin/adminList");
	}
	public  function getadminlist()
	{
		$res=Db::name('user')
			->alias('u')
			->join('guard_group g','u.group_id=g.id')
			->select();
		$count=count($res);
		if($res){
			//jsonMsg("success","0",$data,$count);
			$arr=json_encode(
				array(
					'code'=>0,
					'msg'=>'',
					'count'=>$count,
					'data'=>$res
				)
			);
			echo $arr;
		}else{
			jsonMsg('暂时没有内容',1);
		}
	}
	// 添加 管理员 页面
	public function addadmin()
	{
		$res=Db::name('group')->select();
		$this->assign('group',$res);
		return $this->fetch("manage/admin/addadmin");
	}
	// 添加用户方法
	public function  adminAddt(Request $request)
	{
		$param=$request->param();
		$data['username']=$param['username'];
		$data['password']=md5(md5($param['password']));
		$data['grroup_id']=$param['newsType'];
		$data['user_type']=0;
		$res=Db::name('user')->insert($data);
		jsonMsg("success","0");
	}
}
