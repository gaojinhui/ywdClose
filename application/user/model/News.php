<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
use think\Config;
class News extends Model
{
    protected $table ="guard_newslist";
    public function getImagesAttr($value)
    {
       if($value){
           $url = $value;
       }else{
           $url =Config::get('defaultimg');
       }
       return $url;
    }

}