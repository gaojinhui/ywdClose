<?php
namespace app\index\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Other extends Model
{
    protected $table = 'guard_other';
}