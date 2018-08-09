<?php
namespace app\user\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class author extends Controller
{
    public function _initialize() {
        $userinfo =Session::get("userinfo");
        if(!$userinfo){return $this->redirect("/");}
    }
}
