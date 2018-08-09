<?php
namespace app\manage\controller;
use app\manage\model\Indexdata;
use app\manage\model\Menu;
use app\manage\model\Banner;
use app\manage\model\News;
use app\manage\model\Other;
use app\manage\model\Partner;
use app\manage\model\Profile;
use app\manage\model\User;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\image;
use think\Page;
class Index extends author
{
    public function index()
    {
        return view("manage/index");
    }

    public function main(){
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="javascript:" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
        );
        $result = Indexdata::get(['id'=>1])->toArray();
        $this->assign("sysinfo",$info);
        $this->assign("result",array_splice($result,1));
        return $this->fetch("manage/main");
    }


    public function loginout(){
        Session::delete('manageinfo');
        jsonMsg("退出成功","0");
    }

    public function getnavs(Request $request){//获取左侧导航列表
        if($request->isAjax()){
            $menulist = New Menu();
            $rs = $menulist->getmenu();
            if($rs){jsonMsg("success",0,$rs);}}else{jsonMsg("非法请求",1);
        }
    }
    public function showmenu(){//显示菜单管理界面
        $data = DB::name('menu')->where(['type'=>1])->order('menuid asc')->select();
        $result = tree($data);
        $this->assign("list",$result);
        return $this->fetch('manage/menu/menulist');
    }
    public function menuadd(Request $request){//输出添加菜单页面
        $param=$request->param();
        $result = Db::name('menu')->where(['parentid'=>0,'type'=>1])->select();
        $this->assign("menulist",$result);
        if(isset($param['id'])){
            $list = Db::name('menu')->where(['id'=>$param['id']])->find();
            $this->assign("list",$list);
        }

        return $this->fetch('manage/menu/menuadd');
    }

    public function menuadd_handle(Request $request){
        if($request->isAjax()){
            $rs= new Menu();
            if($rs->addmenu_menu()){jsonMsg("更新成功",0);}else{jsonMsg("更新失败",1);}
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function menu_update(Request $request){
        $param=$request->param();
        $result = Db::name('menu')->where(['parentid'=>0,'type'=>1])->select();
        $this->assign("menulist",$result);
        if(isset($param['id'])){
            $list = Db::name('menu')->where(['id'=>$param['id']])->find();
            $this->assign("list",$list);
        }

        return $this->fetch('manage/menu/menu_update');
    }

    public function menuupdate_handle(Request $request){//修改菜单
        if($request->isAjax()){
            $rs= new Menu();
            if($rs->update_menu()){jsonMsg("更新成功",0);}else{jsonMsg("更新失败",1);}
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function showicon(){//输出添加菜单图标界面
        return view('manage/menu/icon');
    }

    public function menuswitch(Request $request){//菜单状态
        $param = $request->param();
        $data = DB::name('menu')->update(['id'=>$param['id'],'status'=>$param['status']]);
        if($data>0){
            jsonMsg("更新成功",0);
        }else{
            jsonMsg("更新失败",1);
        }
    }
    public function delmenu(Request $request){
        if($request->isAjax()){
            $menu = new Menu();
            if($menu->deletemenu()){
                jsonMsg("删除成功",0);
            }else{
                jsonMsg("删除失败",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }

    }
    public function menu_sort(Request $request){
        if($request->isAjax()){
            $menu = new Menu();
            if($menu->upmenuid()){
                jsonMsg("修改成功",0);
            }else{
                jsonMsg("修改失败",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }
    public function bannerlist(){
        return view("manage/banner/banner");
    }
    public function getbannerdata(){
        $rs = Banner::all();
        return jsonMsg("success",0,$rs);
    }
    public function showbanneradd(){
        return view("manage/banner/addbanner");
    }
    public function upload(){
        $file = request()->file('file');
        $s3 = new \Aws\S3\S3Client([
            'version' => '2006-03-01',
            'region'  => 'us-west-2',
            'credentials' => [
                'key'    => '74e13b42-6c66-43e9-a147-f24225badf90',
                'secret' => 'df778d4b-506c-4ea7-bcf5-c8173c44ef59',
            ],
            'endpoint' => 'http://cosapi-wz.chinacache.com'
        ]);
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'video');
            $bucket = 'fckjai-img';
            $key ='ok_'.time().'.jpg'; 
            $tr=$s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile'   =>$info->getpathName()
            ]);
            $imageUrl=$info->getpathName();
            $intr = unlink($imageUrl);
            if($info){
                jsonMsg("成功",0,$tr['ObjectURL']);
            }else{
                jsonMsg("失败",1,$file->getError());
            }
        }
    }

    public function addbanner(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['title'])){$where['title']=$param['title'];}
            if(isset($param['url'])){$where['pic']=$param['url'];}
            if(isset($param['link'])){$where['link']=$param['link'];}
            if(isset($param['cate'])){$where['cate']=$param['cate'];}
            if(isset($param['status'])){$where['status']=$param['status'];}
            $where['time']=time();
            $banner = new Banner();
            $rs = $banner->save($where);
            if($rs){jsonMsg("添加成功",0);}else{jsonMsg("添加失败",1);}
        }else{
            jsonMsg("非法提交",1);
        }

    }

    public function delbanner(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $banner  = Banner::get($param['id']);
                $rs= $banner->delete();
                // $rs = Db::name("banner")->where(['id'=>$param['id']])->delete();
                //unlink('/public/uploads/PPT/'.$thumb["picName"]);
                if($rs){
                    jsonMsg("删除成功",0);
                }else{
                    jsonMsg("删除失败，请重试",1);
                }
            }else{
                jsonMsg("尚未选择删除的图片",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function update_handle(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $where[$param['field']]=$param['val'];
                $rs = Db::name("banner")->where(['id'=>$param['id']])->update($where);
                if($rs){
                    jsonMsg("修改成功",0);
                }else{
                    jsonMsg("修改失败，请重试",1);
                }
            }else{
                jsonMsg("没有找到条件",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }
    public function update_status(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $where["status"]=$param['val'];
                $rs = Db::name("banner")->where(['id'=>$param['id']])->update($where);
                if($rs){
                    jsonMsg("修改成功",0);
                }else{
                    jsonMsg("修改失败，请重试",1);
                }
            }else{
                jsonMsg("没有找到条件",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function about(Request $request){
        $param = $request->param();
        $where['id']=$param['id'];
        $rs = Db::name("other")->where($where)->find();
        if(!$rs){
            $where['content']="暂无内容";
            $where['title']="请填写内容标题例如：公司简介";
            Db::name("other")->insert($where);
        }
        $this->assign("data",$rs);
        return $this->fetch("manage/other/content");
    }

    public function update_about(Request $request){
        $param = $request->param();
        if(isset($param['id'])){$where['id']=$param['id'];}else{jsonMsg("没有修改目标","1");}
        if(isset($param['content'])){$where['content']=$param['content'];}else{jsonMsg("没有填写内容","1");}
        if(isset($param['title'])){$where['title']=$param['title'];}else{jsonMsg("没有标题","1");}
        $where['time']=time();
        $rs = Db::name("other")->update($where);
        if($rs){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
    }

    public function shownews(Request $request){
        $param = $request->param();
        if($param){
            return view("manage/news/newsList",['title'=>"文章列表"]);
        }else{
            return view("manage/news/newState",['title'=>"待审核文章"]);
        }

    }

    public function getnewslist(Request $request){
        $param = $request->param();
        if($param['type']=='null' || $param['type']==''){
            $where['newsType']=['>',1];
        }else{
            $where['newsType']=$param['type'];
        }
        $news= Db::table('guard_newslist')
            ->where($where)
            ->page($param['page'],$param['limit'])
            ->column('id,newsName,newsAuthor,isShow,newsType,newsTime');
        $count= Db::table('guard_newslist')
            ->where($where)
            ->count();
        if($news){jsonMsg("success","0",$news,$count);}else{jsonMsg('暂时没有内容',1,$news,$count);}
    }
    public function newStateList(Request $request){
        $param = $request->param();
        $where['newsType']=2;
        $news= Db::table('guard_newslist')
            ->where($where)
            ->page($param['page'],$param['limit'])
            ->column('id,newsName,newsAuthor,isShow,newsType,newsTime');
        $count= Db::table('guard_newslist')
            ->where($where)
            ->count();
        if($news){jsonMsg("success","0",$news,$count);}else{jsonMsg('暂时没有内容',1,$news,$count);}
    }
  
    public function update_news(Request $request){
        $param = $request->param();
        $news = new News();
        $rs =$news->save([$param['field'] =>$param['val']],['id' =>$param['id']]);
        if($rs){jsonMsg("更新成功",0);}else{jsonMsg("更新失败",1);}

    }
    public function update_news_status(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $news = new News();
                $rs =$news->save(["isShow"=>$param['val']],['id' =>$param['id']]);
                if($rs){
                    jsonMsg("修改成功",0);
                }else{
                    jsonMsg("修改失败，请重试",1);
                }
            }else{
                jsonMsg("没有找到条件",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function addnews(){
        return view("manage/news/newsAdd",['title'=>"添加文章"]);
    }

    public function addnews_handle(Request $request){
        $param = $request->param();
        if(isset($param['title'])){$where['newsName']=$param['title'];}
        if(isset($param['author'])){$where['newsAuthor']=$param['author'];}
        if(isset($param['isShow'])){$where['isShow']=$param['isShow'];}
        if(isset($param['editorValue'])){$where['newsContent']=$param['editorValue'];}
        if(!empty($param['type'])){$where['newsType']=$param['type'];}else{jsonMsg("缺少文章类型",1);}
        $where['newsTime']=time();
        $news = new News();
        $rs = $news->save($where);
        if($rs){jsonMsg("添加成功",0);}else{jsonMsg("添加失败",1);}
    }

    public function getnews(Request $request){
        $param = $request->param();
        $rs =News::get(['id'=>$param['id']]);
        $this->assign("news",$rs);
        return $this->fetch("manage/news/shownews");
    }
    public function delnews(Request $request){
        $id = $request->param("id");
        if(!$id){jsonMsg("请选择文章",0);}
        $rs = News::destroy(['id' =>$id]);
        if($rs){jsonMsg("删除成功",0);}else{jsonMsg("删除失败",1);}
    }

    public function news_update(Request $request){
        $id = $request->param("id");
        $rs = News::get(['id'=>$id]);
        $this->assign("newscon",$rs);
        return $this->fetch("manage/news/newsUpdate");
    }
    public function news_update_handle(Request $request){
        $param = $request->param();
        if(isset($param['title'])){$where['newsName']=$param['title'];}
        if(isset($param['author'])){$where['newsAuthor']=$param['author'];}
        if(isset($param['isShow'])){$where['isShow']=$param['isShow'];}
        if(isset($param['editorValue'])){$where['newsContent']=$param['editorValue'];}
        if(isset($param['url'])){$where['images']=$param['url'];}
        $where['newsTime']=time();
        $news = new News();
        $rs = $news->save($where,['id'=>$param['id']]);
        if($rs){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
    }

    public function partnerlist(Request $request){
        if($request->isAjax()){
            $rs = Partner::all();
            if($rs){jsonMsg("success",0,$rs);}else{jsonMsg('error',1);}
        }else{
            jsonMsg("非法请求",1);
        }
    }

    public function showpartner_page(){
        return view("manage/partner/partner");
    }
    public function showaddpartner(){
        return view("manage/partner/addpartner");
    }
    public function delpartner(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $rs = Db::name("banner")->where(['id'=>$param['id']])->delete();
                if($rs){
                    jsonMsg("删除成功",0);
                }else{
                    jsonMsg("删除失败，请重试",1);
                }
            }else{
                jsonMsg("尚未选择内容",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function addpartner_handle(Request $request){
        $param = $request->param();
        if(isset($param['title'])){$where['conpanyname']=$param['title'];}
        if(isset($param['isShow'])){$where['status']=$param['isShow'];}
        if(isset($param['editorValue'])){$where['content']=$param['editorValue'];}
        if(!empty($param['link'])){$where['link']=$param['link'];}else{jsonMsg("缺少网址",1);}
        $where['time']=time();
        $news = new Partner();
        $rs = $news->save($where);
        if($rs){jsonMsg("添加成功",0);}else{jsonMsg("添加失败",1);}
    }
    public function update_parener_status(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $where["status"]=$param['val'];
                $paertner = new Partner();
                $rs = $paertner->where(['id'=>$param['id']])->update($where);
                if($rs){
                    jsonMsg("修改成功",0);
                }else{
                    jsonMsg("修改失败，请重试",1);
                }
            }else{
                jsonMsg("没有找到条件",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }
    public function update_paertner_handle(Request $request){//修改菜单
        if($request->isAjax()){
            $paertner= new Partner();
            $param = $request->param();
            $where[$param['field']]=$param['val'];
            $rs=$paertner->where(['id'=>$param['id']])->update($where);
            if($rs){jsonMsg("更新成功",0);}else{jsonMsg("更新失败",1);}
        }else{
            jsonMsg("非法提交",1);
        }
    }
    public function delpaertner(Request $request){
        if($request->isAjax()){
            $rs = Partner::get($request->param("id"));
            if($rs->delete()){jsonMsg("删除成功",0);}else{jsonMsg("删除失败",1);}
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function update_partner(Request $request){
        $rs = Partner::get($request->param("id"));
        $this->assign("result",$rs);
        return $this->fetch("manage/partner/partnerUpdate");
    }

    public function paertner_update_handle(Request $request){
        $param = $request->param();
        if(isset($param['title'])){$where['conpanyname']=$param['title'];}
        if(isset($param['link'])){$where['link']=$param['link'];}
        if(isset($param['status'])){$where['status']=$param['status'];}
        if(isset($param['editorValue'])){$where['content']=$param['editorValue'];}
        $where['time']=time();
        $news = new Partner();
        $rs = $news->save($where,['id'=>$param['id']]);
        if($rs){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
    }

    public function get_alluser(){
        return view('manage/user/alluser',['title'=>"用户管理"]);
    }
    public function add_user(){
        return view('manage/user/add',['title'=>"添加管理人员"]);
    }
    public function get_userlist(Request $request){
        if($request->isAjax()){
            $rs=Db::view('User')
                ->view('User_profile','*','user_profile.uid=User.uid')
                ->where(['role_id'=>1])
                ->select();
            $arr=array();
            foreach($rs as $key=> $v){
                $v['key']=$key+1;
                $v['addtime'] =date("Y-m-d H:i:s",$v['create_date']);
                array_push($arr,$v);
            }
            jsonMsg('success',0,$arr);
        }else{
            jsonMsg('不能接受的请求');
        }
    }
    public function showadduser(){
        return view("manage/user/adduser",['title'=>'添加用户']);
    }

    public function adduser(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            $user = new User();
            $profile = new Profile();
            if(isset($param['username'])){$user->username=$param['username'];}
            if(isset($param['phone'])){$user->phone=$param['phone'];}
            if(isset($param['password'])){$user->password=$param['password'];}
            $user->create_date=time();
            if(isset($param['corporation'])){$profile->corporation=$param['corporation'];}
            if(isset($param['company'])){$profile->company=$param['company'];}
            if(isset($param['licence'])){$profile->licence=$param['licence'];}
            if(isset($param['establishment'])){$profile->establishment=$param['establishment'];}
            if(isset($param['register_capital'])){$profile->register_capital=$param['register_capital'];}
            if(isset($param['contact_name'])){$profile->contact_name=$param['contact_name'];}
            if(isset($param['company_phone'])){$profile->company_phone=$param['company_phone'];}
            if(isset($param['company_address'])){$profile->company_address=$param['company_address'];}
            if(isset($param['licenceimg'])){$profile->licenceimg=$param['licenceimg'];}
            $user->profile=$profile;
            $rs=$user->together('profile')->save();
            if(User::get(['username'=>$param['username']])){jsonMsg('添加失败，因为用户已存在');}
            if(false === $rs){jsonMsg($user->getError());}
            if($rs){jsonMsg("添加成功",0);}else{jsonMsg("添加失败",1);}
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function getuserinfo(Request $request){
        if($request->isAjax()){

        }else {
            jsonMsg("非法提交", 1);
        }
    }

    public function update_userstatus(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            if(isset($param['id'])){
                $where["status"]=$param['val'];
                $rs = User::where(['uid'=>$param['id']])->update($where);
                if($rs){
                    jsonMsg("修改成功",0);
                }else{
                    jsonMsg("修改失败，请重试",1);
                }
            }else{
                jsonMsg("没有找到条件",1);
            }
        }else{
            jsonMsg("非法提交",1);
        }
    }

    public function show_userinfo(Request $request){
        $param = $request->param();
        $rs=Db::view('User',['uid'=>'id','*'])
            ->view('User_profile','*','user_profile.uid=User.uid')
            ->where('id','eq',$param['id'])
            ->find();
        $rs['addtime']=date("Y-m-d H:i:s",$rs['create_date']);
        $this->assign('info',$rs);
        return $this->fetch('manage/user/showuser');
    }

    public function show_finance_page(Request $request){
        $param = $request->param();
        $where['id']=$param['id'];
        $other = new Other();
        $rs = Other::get($where);
        if(!$rs){
            $where['content']="暂无内容";
            $where['title']="请填写内容标题例如：公司简介";
            $other->save($where);
        }
        $this->assign("data",$rs);
        return $this->fetch("manage/other/finance");
    }

    public function update_finance_page(Request $request){
        $param = $request->param();
        if(isset($param['id'])){$where['id']=$param['id'];}else{jsonMsg("没有修改目标","1");}
        if(isset($param['content'])){$where['content']=$param['content'];}else{jsonMsg("没有填写内容","1");}
        if(isset($param['title'])){$where['title']=$param['title'];}else{jsonMsg("没有标题","1");}
        $where['time']=time();
        $other = new Other();
        $rs = $other->save($where,['id'=>$param['id']]);
        if($rs){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
    }

    public function update_indexdata(Request $request){
        if($request->isAjax()){
            $data = Indexdata::get(1);
            $key = $request->param('key');
            $num = $request->param('text');
            if(!is_numeric($num)){jsonMsg('所填写必须是数字');}
            $data->$key=$num;
            $rs=$data->save();
            if($rs){
                jsonMsg("success",0);
            }else{
                jsonMsg("修改失败,或者没做任何修改");
            }
        }else{
            jsonMsg("非法操作");
        }
    }

    public function editbanner(Request $request)
    {
        $param=$request->param();
        $where['id']=$param['id'];
        $res=Db::name('banner')->where($where)->find();
        $this->assign('result',$res);
        return $this->fetch("manage/banner/editbanner");
    }
    public function editbanner_handle(Request $request)
    {
        $param=$request->param();
        $where['id']=$param['id'];
        $data['title']=$param['title'];
        $data['pic']=$param['url'];
        $data['link']=$param['link'];
        $data['file']=$param['file'];
        $data['status']=$param['status'];
        $data['cate']=$param['cate'];
        $data['url']=$param['url'];
        $res=Db::name('banner')->where($where)->update($data);
        if($res){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
    }
}
