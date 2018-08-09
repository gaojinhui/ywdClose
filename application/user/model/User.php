<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class User extends Model
{
    protected $table ="guard_user";
    public function getStatusTextAttr($value,$data){
        $status = [1=>'正常',2=>'锁定',3=>'已结束'];
        return $status[$data['status']];
    }

    public function profile()
    {
        return $this->hasOne('Profile','uid','uid');
    }
}