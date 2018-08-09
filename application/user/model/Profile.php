<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Profile extends Model
{
    protected $table = 'guard_user_profile';

    public function getAuthTextAttr($value,$data){
        $status = [1=>'未认证',2=>'已认证'];
        return $status[$data['auth_status']];
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