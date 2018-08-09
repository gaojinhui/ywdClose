<?php
namespace app\index\model;
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
        $userinfo = Db::name("user")->where($where)->field("password",true)->find();
        return $userinfo;
    }

}