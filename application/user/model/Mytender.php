<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Mytender extends Model
{
    protected $table ="guard_user_mytender";
    public function getStatusTextAttr($value,$data){
        $status = [1=>'进行中',2=>'开标中',3=>'已结束'];
        return $status[$data['status']];
    }
    public function getStarttimeTextAttr($value,$data){
        return  date("Y-m-d",$data['starttime']);
    }
    public function getEndtimeTextAttr($value,$data){
        return  date("Y-m-d",$data['endtime']);
    }
    public function getKbfsTextAttr($value,$data){
        $status = [1=>'线下',2=>'线上'];

        return $status[$data['kbfs']];
    }
    public function getZbfsTextAttr($value,$data){
        $status = [1=>'公开招标',2=>'邀请招标'];
        return $status[$data['zbfs']];
    }
    public function getCgfsTextAttr($value,$data){
        $status = [1=>'公开比价'];
        return $status[$data['zbfs']];
    }
    public function profile()
    {
        return $this->hasOne('Profile','uid','uid');
    }
}