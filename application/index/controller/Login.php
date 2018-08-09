<?php
namespace app\index\controller;
use app\index\model\User;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use think\Image;
class Login extends Controller
{
    public function __construct()
    {
        $this->url=\think\Config::get('sendUrl');
        $this->url1=\think\Config::get('smsConf');
    }
    /*小程序的授权登陆*/
    public function login(Request $request)
    {
        $param=$request->param();
        $data['nickName']  =$param['nickName'];
        $data['avatarUrl'] =$param['avatarUrl'];
        $data['gender'] =$param['gender'];
        $data['province'] =$param['province'];
        $data['city'] =$param['city'];
        $data['country'] =$param['country'];
        $data['phone']='';
        $code   =   $param['code'];
        $encryptedData   =   $param['encryptedData'];
        $iv   =   $param['iv'];
        $appid  =  "wxd79931c8cd111492" ;
        $secret =   "61b8c8a07b5583e8d89483129f8ee282";

        $URL = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";

        $apiData=file_get_contents($URL);
        $api=json_decode($apiData,true);
        $data['openid']=$api['openid'];
        $where['openid']=$api['openid'];
        // 查找用户信息判定是新用户还是老用户
        $res=Db::name('person')->where($where)->select();
        if($res){
            /*老用户更新用户的信息*/
            //$rs=Db::name('person')->where($where)->update($data);
         }else{
            /*用户第一次登陆*/
            $result=Db::name('person')->insert($data);
            $res=Db::name('person')->where($where)->select();
         }
        /*存储用户信息*/
        return json($res);
    }
    /*验证用户信息的完整性选择展示第一个页面*/
    public function start(Request $request){
        $param=$request->param();
        $where['openid']=$param['openid'];
        $res=Db::name('person')->field('id,openid,phone,birthday,height,fater_height,mother_height,foot_leng,foot_type,heart_rate,left_eye,right_eye,sex')->where($where)->select();
        return json($res);
    }
    /*短信验证手机号码*/
     public function VerifyCode(Request $request){
        $param=$request->param();
        //获取页面传来的号码
        $tel = $param['phone'];
        //实例化配置里面的变量
        $sendUrl = $this->url['smsurl'];
        $smsConf = $this->url1;
        //随机生成6位随机的验证码
        $CheckCode= rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        //存入session可以全局调用
        Cookie::set($tel.'_code',$CheckCode,1800); //压入缓存  半小时内有效
        //组装发送的数据数据
        $smsConf['mobile'] = $tel;
        $smsConf['tpl_value'] = "#code#=".$CheckCode."&#company#=鹦鹉岛";
        $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
        $aa = $this->Sms($content);
        if ($aa) {
            echo json_encode(array("code"=>1,"message"=>"验证码已发送!",'code1'=>$CheckCode));exit;
        }else{
            echo json_encode(array("code"=>0,"message"=>"发送失败,请重新获取!"));exit;
        }
    }
    public function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
     
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
    public function Sms($content){
        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                //echo "短信发送成功,短信ID：".$result['result']['sid'];
                return true;
            }else{
                //状态非0，说明失败
                $msg = $result['reason'];
                //echo "短信发送失败(".$error_code.")：".$msg;
                return false;
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            //echo "请求发送短信失败";
            return false;
        }
    }
    /*存储用户的生日与性别*/
    public function birthday(Request $request)
    {
        $param=$request->param();
        $data['birthday']=$param['birthday'];
        $data['sex']=$param['sex'];
        $where['openid']=$param['openid'];
        $rs=Db::name('person')->where($where)->update($data);
        if ($rs) {
            return json(array("code"=>1,"message"=>"存储成功!"));exit;
        }else{
            return json(array("code"=>0,"message"=>"存储失败!"));exit;
        }
    }
    /*存储用户的手机号*/
    public function phone_save(Request $request)
    {
        $param=$request->param();
        $data['phone']=$param['phone'];
        $code=$param['code'];
        $where['openid']=$param['openid'];
        //Cookie::set($tel.'_code',$CheckCode,1800);
        //$code1=Cookie::get('17080139387_code');
        $code1=$param['code1'];
        if($code==$code1)
        {
            $rs=Db::name('person')->where($where)->update($data);
            if ($rs) {
                return json(array("code"=>1,"message"=>"存储成功!"));exit;
            }else{
                return json(array("code"=>0,"message"=>"存储失败!"));exit;
            }
        }else{
            return json(array("code"=>2,"message"=>"验正码错误!"));exit;
        }
        
    }
    /*获取完成计划的人数*/
    public function plan()
    {
        $rs=Db::name('family')->field('sum(ok)')->select();
        $count=count($rs);
        return json(array("plan"=>$count));
    }
    /*选择体能测评项目*/
    public function choice()
    {
        $where['pid']=0;
        $rs=Db::name('grade')->where($where)->select();
        return json($rs);
    }
    /*选择具体年级*/
    public function choice_grade(Request $request)
    {
        $param=$request->param();
        $where['pid']=$param['id'];
        $rs=Db::name('grade')->where($where)->select();
        return json($rs);
    }
    /*根据年级选择测试的项目*/
    public function choice_project(Request $request)
    {
            $param=$request->param();
            $where['gradeid']=$param['gradeid'];
            $where['sex']=$param['sex'];
            $rs=Db::name('test_project')->where($where)->select();
            return json($rs);
    }
    /*选择测试的数据*/
    public function choice_data(Request $request)
    {
            $param=$request->param();
            $field=$param['value'];
            $where['sex']=$param['sex'];
            $where['gradeid']=$param['gradeid'];
            $rs=Db::name('standard')->field("$field")->where($where)->order('id asc')->limit(0,1)->find();
            $rts=Db::name('standard')->field("$field")->where($where)->order('id desc')->limit(0,1)->find();
            // foreach($rs as $k=>$v)
            // {
                $res['minres']=$rs[$field];
                $res['maxres']=$rts[$field];
            // }
            return json($res);
    }
    /*根据用户提交的信息进行测评并且保存测评结果
    返回测评分值与描述并推荐课程根据测评分数*/    
    public function choise_return(Request $request){
            $param=$request->param();
            $value=$param['value'];
            $where="".$value.">=".$param['selectvalue']." and sex=".$param['sex']." and gradeid=".$param['grade'];
            $rs=Db::name('standard')->field('score')->where($where)->order('id asc')->find();
            $whe['openid']=$param['openid'];
            $res=Db::name('person')->field('id')->where($whe)->find();
            /*存储用户的测评结果*/
            $data['value']=$param['value'];
            $data['user_id']=$res['id'];
            $data['fen']=$rs['score'];
            $data['user_fen']=$param['selectvalue'];
            $data['gradeid']=$param['grade'];
            $data['sex']=$param['sex'];
            $data['c_time']=time();
            $data['s_time']=strtotime(date('Y-m-d'));//当天零点时间
            $res=Db::name('user_result')->insert($data);
            /*根据用户的测评推荐用户观看的视频*/
            $wh['val']=$data['value'];
            $result=Db::name('video')->where($wh)->limit(0,4)->select();
            foreach($result as $k=>$v)
            {
                $result[$k]['image']=str_replace("\\","/",$v['image']);
            }
            $json['fen']=$rs['score'];
            $json['video']=$result;
            return json($json);
    }
    // 个人中心查看测评结果详细
    public function person_return(Request $request)
    {
        $param=$request->param();
        $where['u.id']=$param['grade'];
        $where['p.openid']=$param['openid'];
        $res=Db::name('user_result')
                        ->alias('u')
                        ->where($where)
                        ->join("guard_person p","p.id=u.user_id")
                        ->find();
        /*根据用户的测评推荐用户观看的视频*/
        $wh['val']=$res['value'];
        $result=Db::name('video')->where($wh)->limit(0,4)->select();
        foreach($result as $k=>$v)
        {
            $result[$k]['image']=str_replace("\\","/",$v['image']);
        }
        $json['fen']=$res;
        $json['video']=$result;
        return json($json);
    }
    // 个人中心页面查看测评结果
    public function c_request(Request $request)
    {
        $param=$request->param();
        $where['openid']=$param['openid'];
        $rs=Db::name('person')->field('id,sex')->where($where)->find();
        $whe['user_id']=$rs['id'];
        $w['u.user_id']=$rs['id'];
        $w['s.sex']=$rs['sex'];
        $res=Db::name('user_result')->field('gradeid')->where($whe)->find();
        $w['s.gradeid']=$res['gradeid'];
        $res=Db::name('user_result')
                ->alias('u')
                ->field('u.id,u.c_time,u.user_id,u.value,u.fen,u.user_fen,u.gradeid,u.sex,s.test_project')
                ->join("guard_test_project s","s.value=u.value")
                ->where($w)->select();
        foreach($res as $k=>$v){
            $res[$k]['c_time']=date('Y:m:d H:i:s',$v['c_time']);
        }
        return json($res);
    }
}
