<?php
namespace app\index\controller;
use app\index\model\User;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Person extends Controller
{
    /*查看个人详细 信息*/
    public function user_info(Request $request)
    {
        $param=$request->param();
        $where['openid']=$param['openid'];
        $rs=Db::name('person')->where($where)->find();
        $t=time()-$rs['up_time'];
        if($t>7776000){
            // 用户修改信息过了三个月了可以修改
            $rs['up_time']=1;
        }
        else
        {
            //不够 时间不 可以 修改 
            $rs['up_time']=0;
        }
        return json($rs);
    }
    // 修改 个人信息
    public function  update_user_info(Request $request)
    {
        $param=$request->param();
        if($param['height']!=='未填写'){ $data['height']=$param['height'];}//身高
        if($param['weight']!=='未填写'){$data['weight']=$param['weight'];}//体重
        if($param['foot_leng']!=='未填写'){$data['foot_leng']=$param['foot_leng'];}//脚长
        if($param['foot_type']!=='未填写'){$data['foot_type']=$param['foot_type'];}//脚型 
        if($param['heart_rate']!=='未填写'){$data['heart_rate']=$param['heart_rate'];}//心率
        if($param['left_eye']!=='未填写'){$data['left_eye']=$param['left_eye'];}//左眼
        if($param['right_eye']!=='未填写'){$data['right_eye']=$param['right_eye'];}//右眼
        $data['up_time']=time();
        $where['openid']=$param['openid'];
        $res=Db::name('person')->where($where)->update($data);
        return json($res);
    }
    /*查看用户账户余额*/
    public function balance(Request $request)
    {
        $param=$request->param();
        $where['openid']=$param['openid'];
        $rs=Db::name('person')->field('nickName,balance,avatarUrl')->where($where)->find();
        return json($rs);
    }
    /*查看用户订单*/
    public function order(Request $request)
    {
        $param=$request->param();
        $where['p.openid']=$param['openid'];
        $ordertype=$param['orderType'];
        $where['o.r_kcdqtime']=array('egt',time());
        if($ordertype==0)
        {
            // 全部订单

        }
        elseif($ordertype==1)
        {
            // 待付款订单
           $where['o.r_state']=1;
        }
        elseif($ordertype==2)
        {
            // 待评价订单
            $where['o.evaluate']=2;
        }
        $rs=Db::name('person')
                   ->alias('p')
                   ->join("guard_order o","o.r_mid=p.id")
                   ->where($where)
                   ->select();
        return json($rs);
    }
    /*查看待付款订单*/
    public function order_stay()
    {
        $where['r_mid']=Session::get('user');
        $where['r_state']=1;
        $rs=Db::name('order')->where($where)->select();
        return json($rs);
    }
    /*查看待评价*/
    public function evaluate()
    {
        $where['r_mid']=Session::get('user');
        $where['evaluate']=2;
        $rs=Db::name('order')->where($where)->select();
        return json($rs);
    }
    /*我的课程全部课程*/
    public function my_video(Request $request)
    {
        $param=$request->param();
        $where['p.openid']=$param['openid'];
        $video_state=$param['video_state'];
        $where['o.r_kcdqtime']=array('egt',time());
        $where['o.r_state']=2;
        if($video_state==0)
        {
            // 全部订单
            $res=Db::name('person')
                   ->alias('p')
                   ->join("guard_order o","o.r_mid=p.id")
                   ->join("guard_video v","v.id=o.r_video")
                   ->where($where)
                   ->select();
            $w['v.openid']=$param['openid'];
           $rts=Db::name('video_type')
                        ->alias('v')
                        ->join("guard_video o","o.id=v.video_id")
                        ->where($w)->select();
            $rs = array_merge($res,$rts);
                //合并需要合并的俩个数组
                  $key = 'id';//去重条件
                  $tmp_arr = array();//声明数组
                 foreach($rs as $k => $v)
                 {
                  if(in_array($v[$key], $tmp_arr))
                //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                  {
                   unset($rs[$k]);
                   //删除掉数组（$arr）里相同ID的数组
                  }
                 else {
                   $tmp_arr[] = $v[$key];
                   //记录已有的id
                  }
                 }
        }
        elseif($video_state==1)
        {
            // 已经观看视频
           $w['v.openid']=$param['openid'];
           $rs=Db::name('video_type')
                        ->alias('v')
                        ->join("guard_video o","o.id=v.video_id")
                        ->where($w)->select();
        }
        elseif($video_state==2)
        {
            // 没有观看视频
            $where['o.type']=2;
            $rs=Db::name('person')
                   ->alias('p')
                   ->join("guard_order o","o.r_mid=p.id")
                   ->join("guard_video v","v.id=o.r_video")
                   ->where($where)
                   ->select();
        }
        elseif($video_state==3)
          {
            // 全部订单
            $rs=Db::name('person')
                   ->alias('p')
                   ->join("guard_order o","o.r_mid=p.id")
                   ->join("guard_video v","v.id=o.r_video")
                   ->where($where)
                   ->select();
        }  
        return json($rs);
    }
    /*已观看课程*/
    public function already_watched()
    {
        $where['r_mid']=Session::get('user');
        $where['r_state']=2;
        $where['r_kcdqtime']>=time();
        $r_video=Db::name('order')->field('r_video,series')->where($where)->find();
        if($r_video['series']==1){
            $array=Db::name('video_type')->select();
        }else{
                $where['type']=1;
                $arr=Db::name('video')->where($where)->select();
        }
    }
    /*未观看课程*/
    public function not_watching()
    {
        $where['r_mid']=Session::get('user');
        $where['r_state']=2;
        $where['r_kcdqtime']>=time();
        $r_video=Db::name('order')->field('r_video,series')->where($where)->find();
        if($r_video['series']==1){
                $arr2=Db::name('video_type')->field('video_id')->select();
                $arr=Db::name('video')->field('id')->where($where)->select();
               /*取差集就是没有观看视频的id*/
               foreach($arr2 as $v){
                    $arr4[]['id']=$v['user_id'];
                }
               $arr3=array();
               foreach ($arr as $key => $value) {
                    if(!in_array($value,$arr4)){
                        $arr3[]=$value;
                    }
                }
                foreach($arr3 as $v){
                    $w['id']=$v['id'];
                    $array1=Db::name('video')->where($w)->select();
                    $array=array_merge($array,$array1);
                }
        }else{
                $where['type']=2;
                $array=Db::name('video')->where($where)->select();
        }
         return json($array);
    }
    /*普通会员*/
    public function ordinary_vip(Request $request)
    {
        $param=$request->param();
        /*视频分类id*/
        $where['fen_id']=$param['fen_id'];
        $w['openid']=$param['openid'];
        /*查询分类下的所有视频id并转换为json格式数据*/
        $rs=Db::name('video')->field('id')->where($where)->select();
        $data['r_video']=json_encode($rs);
        $rm=Db::name('person')->where($w)->find();
        $data['r_mid']=$rm['id'];
        $data['r_money']=$param['money'];
        /*检测用户是否已经买过相同课程*/
        $where_or['r_video']=json_encode($rs);
        $where_or['r_kcdqtime']>time();
        $rs=Db::name('order')->where($where_or)->select();
        if($rs){
            return json($rs);
            die;
        }
        //生成订单号
        $orderid = time().rand(1000000000,9999999999);
        //子订单生成数据
        $arr = $rs;
        //主订单生成数据
        $data["r_order"] = $orderid;//订单号
        $data["evaluate"] = 2;//订单是否被评价2未评价1已经评价
        $data["r_kcdqtime"] = time()+7776000;//课程到期时间
        $data["r_strtime"] = time();//订单生成时间
        $data["r_state"] = 1;//订单支付状态1未支付2已经支付
        $data['r_video']=json_encode($rs);//课程id
        $data['r_mid']=Session::get('user');//购买用户的id
        $data['r_money']=$param['money'];//金额
        $data["series"] = 2;//1有全部观看的权利2只能看自己买的
        $user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
        //创建新订单
        $cg =Db::name('order')->insert($data);
        //遍历生成用户的订单 如果主订单生成成功则添加子订单,否则返回错误值
        if ($cg) {
            foreach ($arr as $val) {
                $data["r_video"] = $val['id'];
                $zddcg = Db::name('order')->insert($data);
            }
        }
        if($cg){
            $openId = empty($_POST['openId']) ? "0" : $_POST['openId'];
            //①、获取用户openid
            $tools = new \JsApiPay();
            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody('普通会员');
            $input->SetAttach('普通会员');
            $input->SetOut_trade_no($data['r_order']);
            $input->SetTotal_fee($data['r_money']*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag('普通会员');
            $input->SetNotify_url("http://".$_SERVER['SERVER_NAME']."/Home/Externally/uppaystatus");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);       
            return json(array("code"=>0,"message" =>'请求成功',"data"=>$jsApiParameters));exit; 
        }
    }
    /*超级会员*/
    public function super_vip()
    {
        $param=$request->param();
        /*视频分类id*/
        $where['fen_id']=$param['fen_id'];
        /*查询分类下的所有视频id并转换为json格式数据*/
        $rs=Db::name('video')->field('id')->where($where)->select();
        $data['r_video']=json_encode($rs);
        $data['r_mid']=Session::get('user');
        $data['r_money']=$param['money'];
        /*检测用户是否已经买过相同课程*/
        $where_or['r_video']=json_encode($rs);
        $where_or['r_kcdqtime']>time();
        $rs=Db::name('order')->where($where_or)->select();
        if($rs){
            return json($rs);
            die;
        }
        //生成订单号
        $orderid = time().rand(1000000000,9999999999);
        //子订单生成数据
        $arr = $rs;
        //主订单生成数据
        $data["r_order"] = $orderid;//订单号
        $data["evaluate"] = 2;//订单是否被评价2未评价1已经评价
        if($param['vip_type']==1){
            $data["r_kcdqtime"] = time()+2592000;//课程到期时间1个月
        }elseif($param['vip_type']==2){
            $data["r_kcdqtime"] = time()+7776000;//课程到期时间3个月
        }elseif($param['vip_type']==3){
            $data["r_kcdqtime"] = time()+378432000;//课程到期时间一年
        }
        $data["r_strtime"] = time();//订单生成时间
        $data["r_state"] = 1;//订单支付状态1未支付2已经支付
        $data['r_video']=json_encode($rs);//课程id
        $data['r_mid']=Session::get('user');//购买用户的id
        $data['r_money']=$param['money'];//金额
        $data["series"] = 1;//1有全部观看的权利2只能看自己买的
        $user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
        //创建新订单
        $cg =Db::name('order')->insert($data);
        //遍历生成用户的订单 如果主订单生成成功则添加子订单,否则返回错误值
        if ($cg) {
            foreach ($arr as $val) {
                $data["r_video"] = $val['id'];
                $zddcg = Db::name('order')->insert($data);
            }
        }
        if($cg){
            $openId = empty($_POST['openId']) ? "0" : $_POST['openId'];
            //①、获取用户openid
            $tools = new \JsApiPay();
            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody('普通会员');
            $input->SetAttach('普通会员');
            $input->SetOut_trade_no($data['r_order']);
            $input->SetTotal_fee($data['r_money']*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag('普通会员');
            $input->SetNotify_url("http://".$_SERVER['SERVER_NAME']."/Home/Externally/uppaystatus");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);       
            return json(array("code"=>0,"message" =>'请求成功',"data"=>$jsApiParameters));exit; 
        }
    }
    //修改订单的支付状态
    public function uppaystatus(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
        // file_put_contents(dirname(__FILE__).'/xml.txt',$xml); //记录日志 支付成功后查看xml.txt文件是否有内容 如果有xml格式文件说明回调成功
        $xmlObj=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
        $xmlArr=json_decode(json_encode($xmlObj),true);
        $out_trade_no=$xmlArr['out_trade_no']; //订单号
        $result_code=$xmlArr['result_code']; //状态
        S('order',$out_trade_no,60);
        S('state',$result_code,60);

        if($result_code == 'SUCCESS'){ //数据库操作
            //判断该笔订单是否在商户网站中已经做过处理
            $order = M("order"); //订单表
            $users = M("users"); //用户表
            $res = $order->field("r_state,r_phone,r_agent")->where("r_order = '".$out_trade_no."' ")->find();
            //判断主订单是否已经完成支付
            if ($res['r_state'] == "1") { //【1.待支付 2.已支付 3.支付失败 4.取消支付】
                //组装更改订单的数据
                $data['r_endtime'] = time(); //订单支付的时间
                $data['r_kcdqtime'] = time()+intval(31536000); //购买到期的时间 [在支付时间原基础上增加一年的时间戳] 60*60*24*365 = 31536000
                $data['r_state'] = "2";
                $data['return_visit'] = 1;
                //更改订单状态
                $asd = $order->where("r_order = '".$out_trade_no."'")->save($data);
                if ($asd) {
                    //密码
                    $pwd = $this->randomkeys(6);
                    //订单更改成功后创建用户的基本信息,并且发送短信(订单用户更新)
                    $use = $users->where("phone = '".$res['r_phone']."'")->find();
                        if($use){
                            $order_mid = $order->where("r_order = '".$out_trade_no."'")->save(array("r_mid"=>$use['id']));
                        }else{
                            $dad['username'] = $res['r_phone'];
                            $dad['password'] = md5($pwd);
                            $dad['phone'] = $res['r_phone'];
                            $dad['agent'] = $res['r_agent'];
                            $dad['reg_time'] = time();
                            //创建新用户
                            $yh = $users->add($dad);
                            if ($yh) {
                                //更新用户订单的mid
                                $order_mid = $order->where("r_order = '".$out_trade_no."'")->save(array("r_mid"=>$yh));
                                //调用短信接口
                                $this->VerifyCode($res['r_phone'],$pwd);
                                echo "success";
                            }
                        }
                }
            }
            echo "success";     //请不要修改或删除
        }else{ //失败
            //获取用户的ID
            // $mid = $_REQUEST['mid'];
            //获取缴费订单号
            $rec_id = $out_trade_no;
            //实例化数据库
            $orderinfo = M('order');
            //更改支付状态
            $data=array('r_state'=>3,"r_endtime"=>time());
            $result = $orderinfo->where("r_order='".$rec_id."'")->save($data);

            //验证失败
            echo "fail"; 
            exit;
        }
    }
    /*采集用户身体信息*/
    public function collection_user(Request $request)
    {
        $param=$request->param();
        if(isset($param['birthday'])){
            $data['birthday']=$param['birthday'];
        }
        if(isset($param['height'])){
            $data['height']=$param['height'];
        }
        if(isset($param['weight']))
        {
            $data['weight']=$param['weight'];
        }
        if(isset($param['fater_height'])){
            $data['fater_height']=$param['fater_height'];
        }
        if(isset($param['mother_height'])){
            $data['mother_height']=$param['mother_height'];
        }
        if(isset($param['foot_leng'])){
            $data['foot_leng']=$param['foot_leng'];
        }
        if(isset($param['foot_type'])){
            $data['foot_type']=$param['foot_type'];
        }
        if(isset($param['heart_rate'])){
            $data['heart_rate']=$param['heart_rate'];
        }
        if(isset($param['left_eye'])){
            $data['left_eye']=$param['left_eye'];
        }
        if(isset($param['right_eye'])){
            $data['right_eye']=$param['right_eye'];
        }
        $where['openid']=$param['openid'];
        if(isset($data)){
            $rs=Db::name('person')->where($where)->update($data);
            if($rs)
            {
                return json(array('code'=>1));
            }
            else
            {
                return json(array('code'=>0));
            }
        }
    }
    // 用户消息列表
    public function person_message(Request $request)
    {
        $param=$request->param();
        $w['openid']=$param['openid'];
        $res=Db::name('email_fan')
                    ->where($w)
                    ->order('rtime desc')
                    ->select();
        foreach($res as $k=>$v){
            $res[$k]['editorValue']=strip_tags($v['editorValue']);
            $res[$k]['rtime']=date('Y-m-d',$v['rtime']);
        }

        foreach($res as $k=>$v){
            $a=preg_replace ('/&nbsp;/is', '', $v['editorValue']);
            $res[$k]['editorValue']=mb_substr ($a,0,12).'...';
        }
        return json($res);
    }
    //未读 最新消息
    public function  newsMessage(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $where['type']=2;
            $res=Db::name('email_fan')->where($where)->count();
            return json($res);
    }
    //用户查看返回详情
    public  function message_article(Request $request)
    {
        $param=$request->param();
        $where['id']=$param['id'];
        $res=Db::name('email_fan')->where($where)->find();
        $data['type']=1;
        $rs=Db::name('email_fan')->where($where)->update($data);
        $res['litpic1']=explode('|',$res['litpic1']);
        $res['litpic2']=explode('|',$res['litpic2']);
        // 判断是不是报表是报表的话查找组装报表雷达图数据
        if($res['remarks']==2){
                $array=explode('|',$res['pid']);
                foreach($array as $k=>$v){
                    $rts[$k]=explode(',',$v);
                }
                $result=array();
                $val=array();
                foreach($rts as $k=>$v){
                    $w['id']=$v[0];
                    $r=Db::name('video')->field('title')->where($w)->find();
                    $result[$k][0]=$r['title'];
                    $result[$k][1]=$v[1];
                }
                $arr=array();
                $arr[0]=$result[0];
                $arr[1]=$result[8];
                $arr[2]=$result[7];
                $arr[3]=$result[6];
                $arr[4]=$result[5];
                $arr[5]=$result[4];
                $arr[6]=$result[3];
                $arr[7]=$result[2];
                $arr[8]=$result[1];
            $res['bao']=$arr;
        }
        return json($res);
    }
    //获取会员列表
    public function getVipList()
    {
        $res = Db::name('vip')->select();
        return json($res);
    }
    // 用户折线图数据
    public function canvas(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $person=Db::name('person')->where($where)->find();
            $w['user_id']=$person['id'];
            //找出用户测试的分类
            $res=Db::name('user_result')->field('value')->where($w)->group('value')->select();
            $arr=array();
            $title=array();
            foreach($res as $k=>$v){
                    $w['value']=$v['value'];
                    // 根据分类找出每天的测试分数
                    $arr[$k]=Db::name('user_result')->field('fen,value,s_time')->where($w)->group('s_time')->order('s_time desc')->limit(0,7)->select();
                    $tit['value']=$v['value'];
                    $title[$k]=Db::name('test_project')->field('test_project')->where($tit)->find();
            }
            // 用户每天训练数据
            $ww['x.openid']=$param['openid'];
            $xun=Db::name('person_xun')
                            ->alias('x')
                            ->join("guard_video v","v.id=x.videoid")
                            ->field('v.val,x.fen_p,x.day_time')->where($ww)->group('x.day_time')->order('day_time desc')->limit(0,7)->select();
            $array=array();
            foreach($arr as $k=>$v){
                    foreach($v as $kl=>$val){
                        $array[$k][0][$kl]=$val['fen'];
                        if($xun){
                            foreach($xun as $kx=>$x){
                                if($val['s_time']==$x['day_time'] && $val['value']==$x['val']){
                                    $array[$k][1][$kl]=$x['fen_p'];
                                }else{
                                    $array[$k][1][$kl]=0;
                                }
                            }
                        }else{
                            $array[$k][1][$kl]=0;
                        }
                    }
            }
            $result[0]=$array;
            $result[1]=$title;
            return json($result);
    }
    // 折线图测试分类接口
    public function canvas_type(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $person=Db::name('person')->where($where)->find();
            $w['u.user_id']=$person['id'];
            //找出用户测试的分类
            $res=Db::name('user_result')
                            ->alias('u')
                            ->join("test_project p","p.value=u.value")
                            ->where($w)->group('u.value')->order('u.id asc')->select();
             return json($res);
    }
    // 根据分类查询出分类下的折线图数据
    public function canvas_num(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $person=Db::name('person')->where($where)->find();
            $w['user_id']=$person['id'];
            if($param['typeid']){
                $w['value']=$param['typeid'];
            }else{
                $r=Db::name('user_result')->where()->order('id asc')-find();
                if($r){
                        $w['value']=$r['value'];
                }
            }
            for($i=0;$i<7;$i++){
                $w['s_time']=strtotime(date('Y-m-d',strtotime("-$i day")));
                $arr=Db::name('user_result')->field('fen')->where($w)->group('s_time')->order('s_time asc')->select();
                if($arr){
                    foreach($arr as $k=>$v){
                        $res[$i]=$v['fen'];
                    }
                }else{
                    $res[$i]=0;
                }
            }
            
            
            return json($res);
    }
    // 根据时间条件查看用户数据
    public function canvas_time(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $person=Db::name('person')->where($where)->find();
            $w['user_id']=$person['id'];
            if($param['typeid']){
                $w['value']=$param['typeid'];
            }else{
                $r=Db::name('user_result')->where()->order('id asc')-find();
                if($r){
                        $w['value']=$r['value'];
                }
            }
            if($param['time']==1){
                //近一个月
                $end=4;
                $jun=7;
            }elseif($param['time']==2){
                //近三个月
                $end=3;
                $jun=30;
            }elseif($param['time']==3){
                //近一年
                $end=12;
                $jun=90;
            }
            for($j=1;$j<=$end;$j++){
                $b='';
                    for($i=0;$i<$jun;$i++){
                        $a=$i+$jun*($j-1);
                        $w['s_time']=strtotime(date('Y-m-d',strtotime("-$a day")));
                        $arr=Db::name('user_result')->field('fen')->where($w)->group('s_time')->order('s_time asc')->select();
                        if($arr){
                            foreach($arr as $k=>$v){
                                $b+=$v['fen'];
                            }
                        }else{
                            $b+=0;
                        }
                    }
                    $res[]=ceil($b/$jun);
            }
            return json($res);
    }
    //获取用户观看的视频的列表
    public function video_list(Request $request)
    {
            $param=$request->param();
            $where['x.openid']=$param['openid'];
            $videoid=Db::name('person_xun')
                                    ->alias('x')
                                    ->join("guard_video v","v.id=x.videoid")
                                    ->field('v.title,x.videoid')->where($where)->group('x.videoid')->order('x.day_time asc')->select();
            return json($videoid);
    }
    //查看本视频的学习折线图
    public function video_canvas(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            if($param['videoid']){
                $where['videoid']=$param['videoid'];
            }else{
                $r=Db::name('person_xun')->where($where)->group('day_time')->order('day_time asc')->find();
                if($r){
                    //用户观看过视频默认找到看的第一个视频
                        $where['videoid']=$r['videoid'];
                }
            }
            for($i=0;$i<7;$i++){
                $where['day_time']=strtotime(date('Y-m-d',strtotime("-$i day")));
                $videoid=Db::name('person_xun')
                                    ->field('fen_p')->where($where)->group('day_time')->order('day_time asc')->select();
                if($videoid){
                    foreach($videoid as $k=>$v){
                        $res[$i]=$v['fen_p'];
                    }
                }else{
                    $res[$i]=0;
                }
            }
            return json($res);
    }
    public function timeSelect_video(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            if($param['videoid']){
                $where['videoid']=$param['videoid'];
            }else{
                $r=Db::name('person_xun')->where($where)->group('day_time')->order('day_time asc')->find();
                if($r){
                    //用户观看过视频默认找到看的第一个视频
                        $where['videoid']=$r['videoid'];
                }
            }
             if($param['time']==1){
                //近一个月
                $end=4;
                $jun=7;
            }elseif($param['time']==2){
                //近三个月
                $end=3;
                $jun=30;
            }elseif($param['time']==3){
                //近一年
                $end=12;
                $jun=90;
            }
            for($j=1;$j<=$end;$j++){
                $b='';
                 for($i=0;$i<7;$i++){
                    $a=$i+$jun*($j-1);
                    $where['day_time']=strtotime(date('Y-m-d',strtotime("-$a day")));
                    $videoid=Db::name('person_xun')
                                        ->field('fen_p')->where($where)->group('day_time')->order('day_time asc')->select();
                      if($videoid){
                            foreach($videoid as $k=>$v){
                                $b+=$v['fen_p'];
                            }
                        }else{
                            $b+=0;
                        }
                }
                $res[]=ceil($b/$jun);
            }
           
            return json($res);
    }
}
