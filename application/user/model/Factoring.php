<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Factoring extends Model
{
    public function getEndtimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['endtime']);
    }
    public function getStarttimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['starttime']);
    }
}