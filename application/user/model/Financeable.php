<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Financeable extends Model
{
    public function unitprice()
    {
        return $this->hasOne('Unitprice','id','unit_id');
    }
    public function unitweight()
    {
        return $this->hasOne('Unitweight','id','unit_id');
    }
  public function getStatusTextAttr($value,$data){
      $status = [1=>'全部',2=>'待提报',3=>'待审核',4=>'可融资'];
      return $status[$data['status']];
  }
  public function getTypeTextAttr($value,$data){
      $status = [1=>'酒类',2=>'粮食类',3=>'煤炭',4=>'香菇'];
      return $status[$data['type']];
  }
  public function getEndtimeTextAttr($value,$data){
      return  date("Y-m-d H:i:s",$data['endtime']);
  }
    public function getStarttimeTextAttr($value,$data){
        return  date("Y-m-d H:i:s",$data['starttime']);
    }

}