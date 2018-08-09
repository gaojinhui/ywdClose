<?php
namespace app\index\model;
use think\model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Index extends Model
{
	/*查询轮播图数据*/
	public function cast()
	{
		$result=Db::name('banner')->where('status=1')->select();
		return $result;
	}
	/*轮播图下面的栏目*/
	public function classtype()
	{
		$result		=	Db::name('menu')->select();
		return 		$result;
	}
	/*新闻资讯*/
	public function new()
	{
		$result		=	Db::name('newlist')->where('newtype=2')->select();
		return 		$result;
	}
}