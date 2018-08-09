<?php
namespace app\manage\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class User extends Model
{
    public function getuser(){
        $param = input();
        if(isset($param['username'])){$where['username']=$param['username'];}else{jsonMsg("用户名不能为空","1");}
        if(isset($param['userpwd'])){$where['password']=md5(md5($param['userpwd']));}else{jsonMsg("密码不能为空","1");}
        if(isset($param['captcha'])){if(!captcha_check($param['captcha'])){jsonMsg("验证码不正确","1");}}
        $where['role_id']=2;
        $userinfo = Db::name("user")->field("password",true)->where($where)->find();
        return $userinfo;
    }
    public function getAddtimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['create_date']);
    }

    public function profile()
    {
        return $this->hasOne('Profile','uid','uid');
    }
    // 用户列表接口
    public static function getAdminUser($data){
        if(isset($data['keyword'])){
            $where['username']=['like',"%".$data['keyword']."%"];
        }
        $where['status']=1;
        $userinfo = Db::name("person")->page($data['page'],$data['limit'])->select();
        return $userinfo;
    }
    public static function show_userinfo($data){
        $where['id']=$data;
        $userinfo = Db::name("person")->where($where)->find();
        return $userinfo;
    }
    public static function saveAdmin($param){
        $where['uid']=$param['uid'];
        unset($param['uid']);
        $data=Db::name("user")->where($where)->update($param);
       return $data;
    }
    //获取分组列表
    public static  function getadmingrouplist()
    {
        return Db::name('user_group')->select();
    }
    // 添加分组
    public  static function addgroup($param)
    {
        $data['title']=$param['title'];
        $data['groupid']=$param['groupid'];
        $res=Db::name('user_group')->insert($data);
        return $res;
    }
}