<?php
namespace app\manage\controller;
use app\manage\model\User;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Login extends Controller
{
    public function login(Request $request)
    {
        if($request->isAjax()){
            $user = new User();
            $userinfo=$user->getuser();
            if($userinfo){
                Session::set("manageinfo",$userinfo);

                jsonMsg("登录成功","0");
            }else{
                jsonMsg("登录失败,请检查用户名和密码是否正确","1");
            }
        }else{
            jsonMsg("非法提交","1");
        }
    }

    public function showlogin(){
        return view("manage/login/login");
    }

}
