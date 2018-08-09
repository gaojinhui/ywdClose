<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Gathering extends Model
{
    protected $table = 'guard_user_gathering';

    public function getStatusTextAttr($value,$data){
        $status = [1=>'待收款',2=>'已收款'];
        return $status[$data['status']];
    }
    public function getPaystatusTextAttr($value,$data){
        $status = [1=>'银行转账',2=>'现金交易'];
        return $status[$data['pay_type']];
    }
    public function getPaytimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['apply_time']);
    }
    public function getFinishtimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['finish_time']);
    }
    public function getTypestatusTextAttr($value,$data){
        $status = [1=>'应付帐款',2=>'应收帐款'];
        return $status[$data['type']];
    }

}