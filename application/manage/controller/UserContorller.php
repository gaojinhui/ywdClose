<?php
namespace app\manage\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
use app\manage\model\User;
class UserContorller extends author
{
   public function addUser(Request $request){
       if($request->isAjax()){
           $param = $request->param();
           if(User::get(['username'=>$param['username']])){jsonMsg('添加失败，因为用户名已存在');}
           $user = new User();
           if(isset($param['username'])){$user->username=$param['username'];}
           if(isset($param['phone'])){$user->phone=$param['phone'];}
           if(isset($param['password'])){$user->password=md5(md5($param['password']));}
           if(isset($param['role'])){$user->role_id=$param['role'];}
           if(isset($param['state'])){$user->state=$param['state'];}
           $user->create_date=time();
           $rs=$user->save();
           if(false === $rs){jsonMsg($user->getError());}
           if($rs){jsonMsg("添加成功",0);}else{jsonMsg("添加失败",1);}
       }else{
           jsonMsg("非法提交",1);
       }
   }
    public function get_alluser(){
        return view('manage/user/alladmin',['title'=>"用户管理"]);
    }

    // 用户列表接口
    public function getAdminUser(Request $request){
       $param = $request->param();
         $data= User::getAdminUser( $param );
         $userinfo = Db::name("person")->select();
        $count=count($userinfo);
        if($data){jsonMsg("success","0",$data,$count);}else{jsonMsg("暂无数据",1);}
    }
    public function saveInfo(Request $request){
        $param = $request->param();
        $where['id']=$param['id'];
        $data['nickName']=$param['username'];
        $data['phone']=$param['phone'];
        $data['fen']=$param['fen'];
        $data['balance']=$param['balance'];
        $data['birthday']=$param['birthday'];
        $data['height']=$param['height'];
        $data['fater_height']=$param['fater_height'];
        $data['mother_height']=$param['mother_height'];
        $data['foot_leng']=$param['foot_leng'];
        $data['foot_type']=$param['foot_type'];
        $data['heart_rate']=$param['heart_rate'];
        $data['left_eye']=$param['left_eye'];
        $data['right_eye']=$param['right_eye'];
        $data['weight']=$param['weight'];
        $data['sex']=$param['status'];
        $rs = Db::name('person')->where($where)->update($data);
        if($rs){
            echo "<script>";
            echo "window.location.href='http://www.rjt-stirling.com/manage/user_contorller/get_alluser'";
            echo "</script>";
        }else{
            echo "<script>";
            echo "window.location.href='http://www.rjt-stirling.com/manage/user_contorller/get_alluser'";
            echo "</script>";
        }
    }
    // 编辑 用户
    public function show_userinfo(Request $request){
        $param = $request->param();
        $rs=User::show_userinfo($param['id']);
        $foot_type[0]['foot_type']=1;
        $foot_type[1]['foot_type']=2;
        $this->assign('info',$rs);
        $this->assign('foot_type',$foot_type);
        if(array_key_exists('save',$param)){
            return $this->fetch('manage/user/saveadmin');
        }else{
            return $this->fetch('manage/user/showadmin');
        }
    }
    public function edit_user(Request $request)
    {
        $param = $request->param();
        $where["openid"]=$param['openid'];
        $data["nickName"] =$param['username'];
        $data["phone"]=$param['phone'];
        $data["fen"]=$param['fen'];
        $data["balance"]=$param['balance'];
        $data["birthday"]=$param['birthday'];
        $data["height"]=$param['height'];
        $data["fater_height"]=$param['fater_height'];
        $data["mother_height"] =$param['mother_height'];
        $data["foot_leng"]=$param['foot_leng'];
        $data["foot_type"]=$param['foot_type'];
        $data["heart_rate"]=$param['heart_rate'];
        $data["left_eye"]=$param['left_eye'];
        $data["right_eye"]=$param['right_eye'];
        $data["weight"]=$param['weight'];
        $data["gender"]=$param['status'];
        $res=Db::name('person')->where($where)->update($data);
        if($res){
            echo "<script>";
            echo "window.location.href='/manage/user_contorller/get_alluser'";
            echo "</script>";
        }else{
            echo "<script>";
            echo "window.location.href='/manage/user_contorller/get_alluser'";
            echo "</script>";
        }
    }
    public function saveAdmin(Request $request){
        $param = $request->param();
        $data=User::saveAdmin($param);
        if($data){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
    }
    /*用户反馈信息列表*/
    public function email(Request $request){
        $param = $request->param();
        if(isset($param['id']))
        {
            $id=$param['id'];
        }
        else
        {
            $id='type';
        }
        $type=$param['ty'];
        $this->assign('id',$id);
        $this->assign('type',$type);
        $this->assign('openid',$param['openid']);
        return view('manage/user/email',['title'=>"用户反馈"]);
    }
    // 反馈测试 视频的用户列表
    public function emailuser(Request $request){
        $param = $request->param();
        if(isset($param['id']))
        {
            $id=$param['id'];
        }
        else
        {
            $id='type';
        }
        $type=$param['ty'];
        $this->assign('id',$id);
        $this->assign('type',$type);
        return view('manage/user/emailuser',['title'=>"用户反馈"]);
    }
    // 反馈测试视频的用户列表获取 数据
    public function   email_user_list(Request $request)
    {
        $param = $request->param();
        $where['p.id']=$param['id'];
        $where['e.type']=$param['type'];
        $w['e.type']=$param['type'];
        if($where['p.id']=='type')
        {
            $data = Db::name("person")
                    ->alias('p')
                    ->join("guard_email e","e.openid=p.openid")
                    ->where($w)
                    ->order('e.id desc')
                    ->group('e.openid')
                    ->page($param['page'],$param['limit'])
                    ->select();
            $res= Db::name("email")
                    ->alias('e')
                    ->group('e.openid')
                    ->select();
        }
        else
        {
            $data = Db::name("person")
                    ->alias('p')
                    ->join("guard_email e","e.openid=p.openid")
                    ->where($where)
                    ->order('e.id desc')
                    ->page($param['page'],$param['limit'])
                    ->select();
            $res=Db::name("email")
                    ->alias('e')
                    ->join("guard_person p","p.openid=e.openid")
                    ->where($where)
                    ->group('e.openid')
                    ->select();
        }
        foreach($data as $k=>$v)
        {
            $statue['stat']=$v['stat'];
            $st=Db::name('email')->where($statue)->order('id desc')->find();
            if($st['stat']==2){
                $data[$k]['statuetype']='未回复';
            }elseif($st['stat']==1){
                $data[$k]['statuetype']='已回复';
            }
            if($v['rtime'])
            {
                $data[$k]['rtime']=date('Y-m-d  H-i-s',$v['rtime']);
            }
             if($v['ftime'])
             {
                $data[$k]['ftime']=date('Y-m-d  H-i-s',$v['ftime']);
             }   
        }
        $count=count($res);
        if($data){jsonMsg("success","0",$data,$count);}else{jsonMsg("暂无数据",1);}
    }
    public function email_list(Request $request){
        $param = $request->param();
        $wher['e.id']=$param['id'];
       // $where['e.type']=$param['type'];
        $where['e.openid']=$param['openid'];
        //$w['e.type']=$param['type'];
        $w['e.openid']=$param['openid'];
        if($param['type'] !='1-1'&&$param['type'] !='2-2' && $param['type'] != '3-3' && $param['type'] !='4-4')
        {
             $data = Db::name("person")
                    ->alias('p')
                     ->field('e.id,e.openid,e.videoid,p.nickName,v.title,e.ftime,e.rtime,e.stat')
                    ->join("guard_email e","e.openid=p.openid")
                   ->join("guard_video v","v.id=e.videoid")
                    ->where($w)
                    ->order('e.id desc')
                    ->group('e.emailGroup')
                    ->page($param['page'],$param['limit'])
                    ->select();
            $res= Db::name("email")
                    ->alias('e')
                    ->where($w)
                    ->group('e.emailGroup')
                    ->select();
        }
        else if($param['type']=='1-1' || $param['type'] =='2-2' || $param['type'] == '3-3' || $param['type'] =='4-4')
        {
            $data = Db::name("email")
                    ->alias('e')
                    ->field('e.id,e.openid,e.videoid,p.nickName,v.title,e.ftime,e.rtime,e.stat')
                    ->join("guard_person p","p.openid=e.openid")
                    ->join("guard_video v","e.videoid=v.pid")
                    ->where($where)
                    ->order('e.id desc')
                    ->group('e.emailGroup')
                    ->page($param['page'],$param['limit'])
                    ->select();
            $res=Db::name("email")
                    ->alias('e')
                    ->where($where)
                    ->group('e.emailGroup')
                    ->select();
        }
        foreach($data as $k=>$v)
        {
            $wpr['openid']=$v['openid'];
            $wpr['videoid']=$v['videoid'];
            $r=Db::name('record')->where($wpr)->order('id desc')->find();
            $data[$k]['day']='已按照计划训练'.$r['day'].'天';
            if($v['rtime'])
            {
                $data[$k]['rtime']=date('Y-m-d  H-i-s',$v['rtime']);
            }
             if($v['ftime'])
             {
                $data[$k]['btime']=$v['ftime'];
                $data[$k]['ftime']=date('Y-m-d  H-i-s',$v['ftime']);
             }   
        }
        $count=count($res);
        if($data){jsonMsg("success","0",$data,$count);}else{jsonMsg("暂无数据",1);}
    }
    /*删除用户提交的反馈*/
    public function delemail(Request $request){
        $param=$request->param();
        $where['id']=$param['id'];
        $rts=Db::name('email')->field('emailGroup')->where($where)->find();
        $rs=Db::name('email')->where($rts)->delete();
         if ($rs) {
                return jsonMsg("success", 0, $rs);
            } else {
                return jsonMsg("添加失败", 1);
            }
    }
    /*查看用户反馈的信息*/
    public function showemail(Request $request){
        $param=$request->param();
        $where['e.id']=$param['id'];
        $rs=Db::name('email')
                ->alias('e')
                ->join("guard_person p","p.openid=e.openid")
                ->where($where)->find();
        $this->assign('info',$rs);
        return $this->fetch('manage/user/showemail');
    }
    /*给用户返回数据信息*/
    public function fan_email(Request $request){
        $param=$request->param();
        $where['e.id']=$param['id'];
        $where['e.openid']=$param['openid'];
        $emailGroup=Db::name('email')->alias('e')->field('e.emailGroup,e.videoid')->where($where)->find();
        $videoUrl=Db::name('email')->where($emailGroup)->select();
        if($emailGroup['videoid']=='4-4'){
            foreach ($videoUrl as $k => $v) {
                $videoUrl[$k]['title']='自由测评';
            }
            $title='自由测评';
        }else{
            $wvideo['id']=$emailGroup['videoid'];
            $videoTitle=Db::name('video')->where($wvideo)->find();
            // foreach ($videoUrl as $k => $v) {
            //     $videoUrl[$k]['title']=$videoTitle['title'];
            // }
            $title=$videoTitle['title'];
        }
        $rs=Db::name('email')
                ->alias('e')
                ->join("guard_person p","e.openid=p.openid")
                ->where($where)->find();
        $this->assign('info',$rs);
        $this->assign('id',$param['id']);
        $this->assign('videoUrl',$videoUrl);
        $this->assign('title',$title);
        $this->assign('btime',$param['btime']);
        $this->assign('openid',$param['openid']);
        return $this->fetch('manage/user/fan_email');
    }
    /*存储给用户返回的数据信息*/
    public function email_fan(Request $request)
    {
        $param                       =        $request->param();
        if(empty($param['remarks'])){
            $data['remarks']=1;
        }else{
            $data['remarks']=$param['remarks'];
        }
        $data['litpic1']              = implode("|", $param['litpic1']);
        $data['litpic2']              =       implode("|", $param['litpic200']);
        $data['title']              =        $param['title'];
        $where['ftime']                 =$param['btime'];
        //$res=Db::name('email')->field('openid')->where($where)->find();
        $data['openid']                  =        $param['openid'];
        if(isset($param['editorValue']))
        {
            if(empty($param['editorValue'])){
                $data['editorValue']    =      1;
            }else{
                $data['editorValue']    =       $param['editorValue'];
            }
          
        }else{
            $data['editorValue']    =      1;
        }
        if(isset($param['summary']))
        {
          $data['summary']        =$param['summary'];
        }else{
             $data['summary']        =1;
        }
        $data['rtime']=time();
        $data['pid']=$param['uid'];
        $rs=Db::name('email_fan')->insert($data);
        // 回复成功记录状态
        $d['stat']=1;//1已经 回复 2没有回复
        $d['rtime']=time();
        $rts=Db::name('email')->where($where)->update($d);
        // if($rs)
        // {
        //     header('location: '.$_SERVER['HTTP_REFERER']);
        // }
        if ($rs) {
                return jsonMsg("success", 0, $rs);
            } else {
                return jsonMsg("添加失败", 1);
            }
    }
    // 查看现有的 用户的训练计划
    public  function  xunlian(Request $request)
    {
        $param=$request->param();
        // 反馈回来的id
        $where['id']=$param['id'];
        $videoid=Db::name('email')->field('openid,videoid')->where($where)->find();//找出反馈的视频的id
        //根据反馈的视频的id还有用户的 id查找用户现有的训练计划
        $wxl['videoid']=$videoid['videoid'];
        $wxl['openid']=$param['openid'];
        if(isset($param['value'])){
            $wxl['day']=$param['value'];
        }else{
            if(date('w')=='0'){
                $wxl['day']=7;
            }else{
                $wxl['day']=date('w');
            }
        }
        $res=Db::name('program_person')->where($wxl)->select();
        if(!$res){
            $res[0]['content']='';
            $res[0]['daytime']=time();
            $res[0]['action']='';
            $res[0]['num_time']='';
            $res[0]['group_time']='';
            $res[0]['group_num']='';
            $res[0]['group_rest']='';
        }
        $w['id']=$param['id'];
        $data=Db::name('video')->field('training_content')->where($w)->find();
        $this->assign('xun_list',$res);
        $this->assign('data',$data);
        $this->assign('id',$wxl['videoid']);
        $this->assign('openid',$param['openid']);
        $this->assign('value',$wxl['day']);;
        $this->assign('content',$res[0]['content']);
        $this->assign('action',$res[0]['action']);
        $this->assign('daytime',$res[0]['daytime']);
        return $this->fetch('manage/user/xunlian');
    }
    // 用户训练 计划添加 页面 
    public  function user_xun(Request $request)
    {
        $param=$request->param();
        // 反馈回来的id
        $where['id']=$param['id'];
        $video=Db::name('email')->field('id,openid,videoid')->where($where)->find();//找出反馈的视频的id
        $videoid['videoid']=$video['videoid'];
        $videoid['openid']=$param['openid'];
        $w['id']=$videoid['videoid'];
        $data=Db::name('video')->field('training_content')->where($w)->find();
        $this->assign('data',$data);
        $this->assign('videoid',$videoid);
        return $this->fetch('manage/user/Addxunlian');
    }
    // 添加用户 训练计划
    public function insert_xunlian(Request  $request)
    {
        $param=$request->param();
        $xun_number=$param['xun_number'];
            if($xun_number>=1)
            {
                $time=time();
                for($i=1;$i<=$xun_number;$i++)
                {
                    if(isset($param["action$i"])){
                        $data['openid']=$param['openid'];
                        $data['videoid']=$param['videoid'];
                        $data['action']=$param["action$i"];//动作
                        $data['num_time']=$param["num_time$i"];//个数/时间
                        $data['group_time']=$param["group_time$i"];//个数/时间
                        $data['group_num']=$param["group_num$i"];//组数
                        $data['group_rest']=$param["group_rest$i"];//组间休息
                        $data['day']=$param['xtime'];
                        $data['content']=$param['content'];
                        $data['daytime']=$time;
                        $rts=Db::name('program_person')->insert($data);
                    }
                }
            }
            if($xun_number>0)
            {
                if($rts){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
            }
            else
            {
                if($tr){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
            }
    }
    // 更新用户训练计划
    public  function  update_xunlian(Request $request)
    {
            $param=$request->param(); 
            //$w['videoid']=$param['videoid'];
            $w['day']=$param['xtime'];
            $w['daytime']=$param['daytime'];
            $tr=Db::name('program_person')->where($w)->delete();
            $xun_number=$param['xun_number'];
            if($xun_number>=1)
            {
                for($i=1;$i<=$xun_number;$i++)
                {
                    if(isset($param["action$i"])){
                        $data['openid']=$param['openid'];
                        $data['videoid']=$param['videoid'];
                        $data['action']=$param["action$i"];//动作
                        $data['num_time']=$param["num_time$i"];//个数/时间
                        $data['group_time']=$param["group_time$i"];//个数/时间
                        $data['group_num']=$param["group_num$i"];//组数
                        $data['group_rest']=$param["group_rest$i"];//组间休息
                        $data['day']=$param['xtime'];
                        $data['content']=$param['content'];
                        $data['daytime']=$param['daytime'];
                        $rts=Db::name('program_person')->insert($data);
                    }
                }
            }
            if($xun_number>0)
            {
                if(isset($rts)){
                    jsonMsg("修改成功",0);
                }else{
                    if($tr){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
                }
            }
            else
            {
                if($tr){jsonMsg("修改成功",0);}else{jsonMsg("修改失败",1);}
            }
    }
    // 预览回复 信息 
    public function yulan(Request  $request)
    {
            $param=$request->param();
            $where['pid']=$param['id'];
            $where['remarks']=1;
            $res=Db::name('email_fan')->where($where)->find();
            $litpic1=explode('|',$res['litpic1']);
            $litpic2=explode('|',$res['litpic2']);
            $this->assign('res',$res);
            $this->assign('litpic1',$litpic1);
            $this->assign('litpic2',$litpic2);
            return $this->fetch('manage/user/yulan');
    }
    // 编辑预览信息 
    public function edityulan(Request  $request){
            $param=$request->param();
            $w['e.id']=$param['id'];
            $rs=Db::name('email')
                    ->alias('e')
                    ->join("guard_person p","e.openid=p.openid")
                    ->where($w)->find();
            $this->assign('info',$rs);
            $this->assign('id',$param['id']);

            $where['pid']=$param['id'];
            $where['remarks']=1;
            $res=Db::name('email_fan')->where($where)->find();
            $litpic1=explode('|',$res['litpic1']);
            $litpic2=explode('|',$res['litpic2']);
            $this->assign('res',$res);
            $this->assign('litpic1',$litpic1);
            $this->assign('litpic2',$litpic2);
            return $this->fetch('manage/user/edityulan');
    }
    //更新预览数据
    public function  yulan_edit(Request  $request)
    {
            $param=$request->param();
            if(isset($param['litpic1'])){
                    $data['litpic1']              = implode("|", $param['litpic1']);
            }else{
                    $data['litpic1']='';
            }
            if(isset($param['litpic200'])){
                    $data['litpic2']              =       implode("|", $param['litpic200']);
            }else{
                    $data['litpic2']='';
            }
            $where['id']                 =$param['uid'];
            // $res=Db::name('email')->field('openid')->where($where)->find();
            // $data['openid']                  =        $res['openid'];
            if(isset($param['editorValue']))
            {
              $data['editorValue']    =       $param['editorValue'];
            }
            if(isset($param['summary']))
            {
              $data['summary']        =$param['summary'];
            }
            $data['rtime']=time();
            $data['pid']=$param['uid'];
            $www['pid']=$param['uid'];
            $www['remarks']=1;
            $rs=Db::name('email_fan')->where($www)->update($data);
            if($rs)
            {
                jsonMsg("修改成功",0);
            }
            else
            {
                jsonMsg("修改失败",0);
            }
    }
    // 添加报表页面
    public function addreport(Request $request)
    {
        $param = $request->param();
        $id=$param['ty'];//传过来的值为1
        $type=$param['personid'];//传过来的值为type
        $openid=$param['openid'];
        $this->assign('id',$id);
        $this->assign('type',$type);
        $this->assign('openid',$openid);
        return view('manage/user/addreport',['title'=>"生成报表"]);
    }
    // 给用户反馈过的图片
    public function litpic(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $where['remarks']=1;
            $type=$param['type'];
            $res=Db::name('email_fan')->where($where)->select();
            $array=array();
            $rts=array();
            foreach($res as $k=>$v){
                 if($type==1){
                    $array=explode('|',$v['litpic1']);//给用户反馈的标准图片
                }else{
                    $array=explode('|',$v['litpic2']);//用户反馈过来的图片
                }
                 foreach($array as $v1){
                    $rts[]=$v1;
                }
            }
            $this->assign('res',$rts);
            return view('manage/user/litpic');
    }
    // 添加报表的方法
    public function add_report(Request $request)
    {
            $param=$request->param();
            $data['title']=$param['remarks'];//报表标题
            $g='';
            for($i=0;$i<9;$i++){
                if($i!=8){
                     $g.=$param['biaoid'][$i].','.$param['bao'][$i].'|';
                 }else{
                    $g.=$param['biaoid'][$i].','.$param['bao'][$i];
                 }
            }
            $data['pid']=$g;
            $data['openid']=$param['openid'];
            $data['litpic1']=implode("|", $param['litpic1']);
            $data['litpic2']=implode("|",$param['litpic200']);
            if(isset($param['editorValue']))
            {
              $data['editorValue']    =       $param['editorValue'];
            }
            if(isset($param['summary']))
            {
              $data['summary']        =$param['summary'];
            }
            $data['remarks']=2;
            $data['rtime']=time();
            $rs=Db::name('email_fan')->insert($data);
            if($rs){jsonMsg("添加成功",0);}else{jsonMsg("添加失败",1);}
    }
     // 报表列表页面
    public function reportlist(Request $request)
    {
            $param = $request->param();
            $id=$param['ty'];//传过来的值为1
            $type=$param['personid'];//传过来的值为type
            $openid=$param['openid'];
            $this->assign('id',$id);
            $this->assign('openid',$openid);
            return view('manage/user/reportlist',['title'=>"报表列表"]);
    }
    // 报表列表页面数据
    public function report_list(Request  $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $where['remarks']=2;
            $rs=Db::name('email_fan')->where($where)->page($param['page'],$param['limit'])->select();
            foreach($rs as $k=>$v){
                    $rs[$k]['rtime']=$v['rtime'];
            }
            $count=count($rs);
            if($rs){jsonMsg("success","0",$rs,$count);}else{jsonMsg("暂无数据",1);}
    }
    // 删除报表
    public function delreport(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['id'];
            $res=Db::name('email_fan')->where($where)->delete();
            if($res){jsonMsg("删除成功",0);}else{jsonMsg("删除失败",1);}
    }
    // 预览报表信息
    public function showreport(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['id'];
            $where['remarks']=2;
            $res=Db::name('email_fan')->where($where)->find();
            $litpic1=explode('|',$res['litpic1']);
            $litpic2=explode('|',$res['litpic2']);
            $this->assign('res',$res);
            $this->assign('litpic1',$litpic1);
            $this->assign('litpic2',$litpic2);
            return $this->fetch('manage/user/showreport');
    }
    // 编辑报表信息页面
    public function editreport(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['id'];
            $this->assign('id',$param['id']);
            $where['remarks']=2;
            $res=Db::name('email_fan')->where($where)->find();
            $litpic1=explode('|',$res['litpic1']);
            $litpic2=explode('|',$res['litpic2']);
            $this->assign('res',$res);
            $this->assign('litpic1',$litpic1);
            $this->assign('litpic2',$litpic2);
            $this->assign("openid",$param['openid']);
            return $this->fetch('manage/user/editreport');
    }
    public function report_edit(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['uid'];
            $where['openid']=$param['openid'];
            $data['editorValue']=$param['editorValue'];
            $data['summary']=$param['summary'];
            $data['litpic1']=implode("|", $param['litpic1']);
            $data['litpic2']=implode("|",$param['litpic200']);
             $g='';
            for($i=0;$i<9;$i++){
                if($i!=8){
                     $g.=$param['biaoid'][$i].','.$param['bao'][$i].'|';
                 }else{
                    $g.=$param['biaoid'][$i].','.$param['bao'][$i];
                 }
            }
            $data['pid']=$g;
            $res=Db::name('email_fan')->where($where)->update($data);
            if($res){jsonMsg("更新成功",0);}else{jsonMsg("更新失败",1);}
    }
    public function video_title_list()
    {
            $where['parentid'] = array('in','146,166,170,184');
            $res=Db::name('video')->field('id,title')->where($where)->order('id asc')->limit(0,9)->select();
            foreach($res as $k=>$v){
                    $result[$k]['name']=$v['title'];
                    $result[$k]['max']=100;
            }
            foreach($res as $k=>$v){
                    $rts[$k]['name']=$v['title'];
                    $rts[$k]['id']=$v['id'];
            }
            jsonMsg("success","0",$result,$rts);
    }
    // 预览报表雷达图数据
    public function lei_list(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['id'];
            $where['openid']=$param['openid'];
            $res=Db::name('email_fan')->field('pid')->where($where)->find();
            $array=explode('|',$res['pid']);
            foreach($array as $k=>$v){
                $rts[$k]=explode(',',$v);
            }
            $result=array();
            $val=array();
            foreach($rts as $k=>$v){
                $w['id']=$v[0];
                $r=Db::name('video')->field('title')->where($w)->find();
                $result[$k]['name']=$r['title'];
                $result[$k]['max']=100;
                $rts[$k]['name']=$r['title'];
                $rts[$k]['id']=$v[1];
                $val[$k]=$v[1];
            }
            if(isset($param['type'])){
                $arr[0]=$rts;
                $arr[1]=$val;
            }else{
                $arr=$val;
            }
            jsonMsg("success","0",$result,$arr);
    }
}
