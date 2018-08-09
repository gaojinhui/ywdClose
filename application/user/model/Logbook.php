<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Logbook extends Model
{
    protected $table = 'guard_user_logbook';

    public function getStatusTextAttr($value,$data){
        $status = [1=>'办理中',2=>'已完成'];
        return $status[$data['status']];
    }

    public function getTimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['sign_time']);
    }


}