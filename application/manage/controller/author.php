<?php
namespace app\manage\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class author extends Controller
{
    public function _initialize() {
        $userinfo =Session::get("manageinfo");
        if(!$userinfo){return $this->redirect("/manage/login/showlogin");}
    }
}
