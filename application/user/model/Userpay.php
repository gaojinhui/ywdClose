<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Userpay extends Model
{
    protected $table = 'guard_user_pay';

    public function getStatusTextAttr($value,$data){
        $status = [1=>'待支付',2=>'已支付'];
        return $status[$data['status']];
    }
    public function getPaystatusTextAttr($value,$data){
        $status = [1=>'应付帐款',2=>'应收帐款'];
        return $status[$data['soc']];
    }
    public function getPaytimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['apply_time']);
    }
    public function getMemberTypeTextAttr($value,$data){
        $status = [1=>'普通会员',2=>'认证会员'];
        return $status[$data['member_type']];
    }
    public function getTradeTextAttr($value,$data){
        $status = [1=>'农业'];
        return $status[$data['trade']];
    }
}