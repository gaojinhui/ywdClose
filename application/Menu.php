<?php
namespace app\manage\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Menu extends Model
{
    public function getmenu(){
      //  $rs = tree(Db::name("menu")->order("menuid",'asc')->select(),0);
       
        $where['u.groupid']=$_SESSION['think']['manageinfo']['grroup_id']
        $where['m.status']=1;
        $rs = tree(Db::name("menu")
                            ->alias('m')
                            ->field('m.*')
                            ->join("guard_group_menu u","m.id=u.menu_id")
                            ->where($where)
                            ->order("m.menuid",'asc')
                            ->select(),0);
        return $rs;
    }
    public function update_menu(){
        $param = input();
        if(!empty($param['title'])){$where['title']=$param['title'];}
        if(!empty($param['status'])){$where['status']=$param['status'];}
        if(!empty($param['parentid'])){ $where['parentid']=$param['parentid'];}
        if(!empty($param['url'])){$where['url']=$param['href'];}
        if(!empty($param['icon'])){$where['icon']=$param['icon'];}
        if(!empty($param['type'])){$where['type']=$param['type'];}
        if(!empty($param['body'])){$where['body']=$param['body'];}
        if(!empty($param['litpic'])){$where['litpic']=$param['litpic'];}
        if(!empty($param['menu_type'])){$where['menu_type']=$param['menu_type'];}
        if(substr($where['litpic'],strlen($where['litpic'])-7)=='menuadd'){
            $where['litpic']='';
        }
        $where['spread']=0;
        $rs = Db::name("menu")->where(['id'=>$param['id']])->update($where);
        return $rs;

    }

    public function deletemenu(){
        $param = input();
        $where['id']=$param['id'];
        $rs = Db::name("menu")->where($where)->delete();
        return $rs;
    }
    public function upmenuid(){
        $param = input();
        $where['menuid']=$param['menuid'];
        $rs = Db::name('menu')->where(['id'=>$param['id']])->update($where);
        return $rs;
    }

    public function addmenu_menu(){
        $param = input();
        if(!empty($param['title'])){$where['title']=$param['title'];}
        if(!empty($param['status'])){$where['status']=$param['status'];}
        if(!empty($param['parentid'])){ $where['parentid']=$param['parentid'];}
        if(!empty($param['url']))
        {
            $where['url']=$param['href'];
        }
        else
        {
            $where['url']='';
        }
        if(!empty($param['icon'])){$where['icon']=$param['icon'];}
        if(!empty($param['type'])){$where['type']=$param['type'];}
        if(!empty($param['body']))
        {
            $where['body']=$param['body'];
        }
        else
        {
            $where['body']='';
        }
        if(!empty($param['litpic'])){$where['litpic']=$param['litpic'];}
        if(!empty($param['menu_type'])){$where['menu_type']=$param['menu_type'];}
        // if(substr($where['litpic'],strlen($where['litpic'])-7)=='menuadd'){
        //     $where['litpic']='';
        // }
        $where['spread']=0;
        $rs  = Db::name("menu")->insert($where);
             $id  =   Db::name('menu')->field('id')->where($where)->find();
            if($param['menu_type']==1){
                $data['href']='/manage/index/shownews?type='.$id['id'];
            }else{
                $data['href']='/manage/video_contorller/getVideolist?type='.$id['id'];
            }
            $result=Db::name('menu')->where($where)->update($data);
            $b['uid']=$_SESSION['think']['manageinfo']['uid'];
            $b['menu_id']=$id['id'];
            $b['state']=1;
            $res=Db::name('user_menu')->insert($b);
        return $rs;
    }
      public static function getNewType(){
        $where['parentid']=2;
        $where['status']=1;
        $rs = Db::name('menu')->field("id,title")->where($where)->select();
        return $rs;
    }

    public static   function getMenuId($uid){
        $where['uid']=$uid;
        $rs = Db::name("user_menu")
            ->field('menu_id')
            ->where($where)
            ->select();
        return $rs;
    }
}