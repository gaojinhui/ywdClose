<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Request;
use think\image;
header("Content-Type: text/html;charset=utf-8");
class Pay extends Controller
{
   //用户购买课程
    public function index(Request $request)
    {
        $param=$request->param();
       $appid   ='wx6208c2b1012d08cc';
       $openid  = $param['openid'];
       $mch_id  ='1504953611';
       $key     ='3603f87c11e37c1e8ef2b4557ce67585';
       // 生成 订单信息 
       //生成订单号
        $orderid = time().rand(1000000000,9999999999);
        //查询用户id
        $where['openid']=$openid;
        $person=Db::name('person')->field('id')->where($where)->find();
        $viptype=$param['viptype'];//订单 分类
        $pricez=$param['pricez'];//订单价格 
        $videoId=$param['videoId'];
        $data['r_order'] = $orderid; //唯一订单号
        $data['r_money']=$pricez;//标价金额
        $data['r_mid']=$person['id'];//购买用户id
        $data['r_state']=1;//支付状态待支付1未支付2已经支付
        $data['r_strtime']=time();//订单生成时间
        $data['evaluate']=2;//1已经评价2待 评价
        $data['type']=2;//1已经观看2未观看
        $data['r_video']=$videoId;//单节课程id
        if($viptype==0)
        {
            $data['series']=0;//0只能看自己 买的视频1观看全部 课程2只能全部推荐 课程3有全部热门课程
        }
        elseif($viptype==1)
        {
            // 购买全部课程
            $data['series']=1;//0只能看自己 买的视频1观看全部 课程2只能全部推荐 课程3有全部热门课程
        }
        elseif($viptype==2)
        {
            // 购买推荐课程
            $data['series']=2;//0只能看自己 买的视频1观看全部 课程2只能全部推荐 课程3有全部热门课程
        }
        elseif($viptype==3)
        {
            // 购买热门课程
            $data['series']=3;//0只能看自己 买的视频1观看全部 课程2只能全部推荐 课程3有全部热门课程
        }
        
        $wwwr['r_mid']=$person['id'];//购买用户id
        $wwwr['series']=$data['series'];
        $pan=Db::name('order')->where($wwwr)->find();
        if($pan){
            if($pan['r_kcdqtime']>=time()){
                $data['r_kcdqtime']=$pan['r_kcdqtime']+7776000;//课程到期时间三个 月
            }else{
                $data['r_kcdqtime']=time()+7776000;//课程到期时间三个 月
            }
            //$data['r_endtime']=0;
            $data['pinglun']=1;
            $rs=Db::name('order')->where($wwwr)->update($data);
        }else{
            $data['r_kcdqtime']=time()+7776000;//课程到期时间三个 月
            $rs=Db::name('order')->insert($data);
        }
       $out_trade_no = $orderid;
       //支付金额
       $total_fee = $pricez*100;
      // $total_fee = 1;
       $body = "智能体育视频购买";
       $total_fee = floatval($total_fee);
       $type=1;
       //A('WeixinPay');
       $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$type);
       $return=$weixinpay->pay();
       //返回订单交易号码
       $return['out_trade_no'] = $out_trade_no;
       return json($return);
   }
   //小程序充值支付回调
   public function payment()
   {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
        $xmlObj=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
        $xmlArr=json_encode($xmlObj);
        $ggg=json_decode($xmlArr,true);
        $out_trade_no=$ggg['out_trade_no']; //订单号
        $result_code=$ggg['result_code']; //状态
        if($result_code == 'SUCCESS')
        {
            $where['r_order']=$out_trade_no;
            $data['r_state']=2;
            $data['r_endtime']=time();
            $res=Db::name('order')->where($where)->update($data);
            echo "success";  
        }
        else
        {
            echo 'fail';
        }
        //file_put_contents('gaojinhui_wxpay.log',$out_trade_no,FILE_APPEND);  
   }
  // 用户普通会员充值
   public function ordinary_vip(Request $request)
   {
            $param=$request->param();
            $appid   ='wx6208c2b1012d08cc';
            $openid  = $param['openid'];
            $mch_id  ='1504953611';
            $key     ='3603f87c11e37c1e8ef2b4557ce67585';
            //生成订单号
            $orderid = time().rand(1000000000,9999999999);
            $out_trade_no = $orderid;
           //支付金额
           $total_fee = $param['price']*100;
          // $total_fee = 1;
           $body = "智能体育普通会员充值";
           $total_fee = floatval($total_fee);
           $type=2;
           //A('WeixinPay');
           $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$type);
           $return=$weixinpay->pay();
           //返回订单交易号码
           $return['out_trade_no'] = $out_trade_no;
           return json($return);
   }
   public function payvip(){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
            $xmlObj=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
            $xmlArr=json_encode($xmlObj);
            $ggg=json_decode($xmlArr,true);
            $out_trade_no=$ggg['out_trade_no']; //订单号
            $result_code=$ggg['result_code']; //状态
            $total_fee=$ggg['total_fee']/100;//用户付的钱数
            $openid=$ggg['openid'];//用户openid
            if($result_code == 'SUCCESS')
            {
                $where['openid']=$openid;
                $money=Db::name('person')->field('c_money')->where($where)->find();
                $data['c_money']=$money+$total_fee;
                $res=Db::name('order')->where($where)->update($data);
                echo "success";  
            }
            else
            {
                echo 'fail';
            }
            //file_put_contents('gaojinhui_wxpay.log',$out_trade_no,FILE_APPEND); 
   }
   // 用户钻石会员充值
   public function type_vip(Request $request)
   {
            $param=$request->param();
            $appid   ='wx6208c2b1012d08cc';
            $openid  = $param['openid'];
            $mch_id  ='1504953611';
            $key     ='3603f87c11e37c1e8ef2b4557ce67585';
            //生成订单号
            $orderid = time().rand(1000000000,9999999999);
            $out_trade_no = $orderid;
           //支付金额
           $total_fee = $param['price']*100;
           //$total_fee = 1;
           $body = "智能体育钻石会员充值";
           $total_fee = floatval($total_fee);
           if($param['typeid']==1){
                $type=3;
           }elseif($param['typeid']==2){
                $type=4;
           }elseif($param['typeid']==3){
                $type=5;
           }
           //A('WeixinPay');
           $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$type);
           $return=$weixinpay->pay();
           //返回订单交易号码
           $return['out_trade_no'] = $out_trade_no;
           return json($return);
   }
   // 全部课程回调
   public function vip_back(){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
            $xmlObj=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
            $xmlArr=json_encode($xmlObj);
            $ggg=json_decode($xmlArr,true);
            $out_trade_no=$ggg['out_trade_no']; //订单号
            $result_code=$ggg['result_code']; //状态
            $total_fee=$ggg['total_fee']/100;//用户付的钱数
            $openid=$ggg['openid'];//用户openid
            if($result_code == 'SUCCESS')
            {
                $where['openid']=$openid;
                $person=Db::name('person')->field('id')->where($where)->find();
                $www['r_mid']=$person['id'];
                $www['series']=1;
                $res=Db::name('order')->where($www)->find();
                if($res){
                    //判断订单状态时间到期了当前时间+3个月   灭到期表中是间+3个月
                    if($res['r_kcdqtime']>=time()){
                            $d['r_kcdqtime']=$res['r_kcdqtime']+7776000;
                    }else{
                            $d['r_kcdqtime']=time()+7776000;
                    }
                    $tr=Db::name('order')->where($www)->update($d);
                }else{
                    //添加新订单
                    $dg['r_order']=$out_trade_no;
                    $dg['r_money']=$total_fee;
                    $dg['r_mid']=$person['id'];
                    $dg['r_state']=2;
                    $dg['r_strtime']=time();
                    $dg['r_endtime']=time();
                    $dg['r_kcdqtime']=time()+7776000;
                    $dg['evaluate']=2;
                    $dg['series']=1;
                    $dg['type']=2;
                    $dg['pinglun']=1;
                    $tr=Db::name('order')->insert($dg);
                }
                echo "success";  
            }
            else
            {
                echo 'fail';
            }
   }
   // 全部推荐课程回调
   public function vip_back_tui(){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
            $xmlObj=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
            $xmlArr=json_encode($xmlObj);
            $ggg=json_decode($xmlArr,true);
            $out_trade_no=$ggg['out_trade_no']; //订单号
            $result_code=$ggg['result_code']; //状态
            $total_fee=$ggg['total_fee']/100;//用户付的钱数
            $openid=$ggg['openid'];//用户openid
            if($result_code == 'SUCCESS')
            {
                $where['openid']=$openid;
                $person=Db::name('person')->field('id')->where($where)->find();
                $www['r_mid']=$person['id'];
                $www['series']=2;
                $res=Db::name('order')->where($www)->find();
                if($res){
                    //判断订单状态时间到期了当前时间+3个月   灭到期表中是间+3个月
                    if($res['r_kcdqtime']>=time()){
                            $d['r_kcdqtime']=$res['r_kcdqtime']+7776000;
                    }else{
                            $d['r_kcdqtime']=time()+7776000;
                    }
                    $tr=Db::name('order')->where($www)->update($d);
                }else{
                    //添加新订单
                    $dg['r_order']=$out_trade_no;
                    $dg['r_money']=$total_fee;
                    $dg['r_mid']=$person['id'];
                    $dg['r_state']=2;
                    $dg['r_strtime']=time();
                    $dg['r_endtime']=time();
                    $dg['r_kcdqtime']=time()+7776000;
                    $dg['evaluate']=2;
                    $dg['series']=2;
                    $dg['type']=2;
                    $dg['pinglun']=1;
                    $tr=Db::name('order')->insert($dg);
                }
                echo "success";  
            }
            else
            {
                echo 'fail';
            }
   }
   // 全部热门课程回调
   public function vip_back_host(){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
            $xmlObj=simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
            $xmlArr=json_encode($xmlObj);
            $ggg=json_decode($xmlArr,true);
            $out_trade_no=$ggg['out_trade_no']; //订单号
            $result_code=$ggg['result_code']; //状态
            $total_fee=$ggg['total_fee']/100;//用户付的钱数
            $openid=$ggg['openid'];//用户openid
            if($result_code == 'SUCCESS')
            {
                $where['openid']=$openid;
                $person=Db::name('person')->field('id')->where($where)->find();
                $www['r_mid']=$person['id'];
                $www['series']=3;
                $res=Db::name('order')->where($www)->find();
                if($res){
                    //判断订单状态时间到期了当前时间+3个月   灭到期表中是间+3个月
                    if($res['r_kcdqtime']>=time()){
                            $d['r_kcdqtime']=$res['r_kcdqtime']+7776000;
                    }else{
                            $d['r_kcdqtime']=time()+7776000;
                    }
                    $tr=Db::name('order')->where($www)->update($d);
                }else{
                    //添加新订单
                    $dg['r_order']=$out_trade_no;
                    $dg['r_money']=$total_fee;
                    $dg['r_mid']=$person['id'];
                    $dg['r_state']=2;
                    $dg['r_strtime']=time();
                    $dg['r_endtime']=time();
                    $dg['r_kcdqtime']=time()+7776000;
                    $dg['evaluate']=2;
                    $dg['series']=3;
                    $dg['type']=2;
                    $dg['pinglun']=1;
                    $tr=Db::name('order')->insert($dg);
                }
                echo "success";  
            }
            else
            {
                echo 'fail';
            }
   }
    public function callback(){
        $token           = 'supermandothis2018';
        if($token == I('post.token')){
            $member_id       = I('post.member_id');
            $recharge_num   =  I('post.recharge_count');
            $out_trade_no   = I('post.out_trade_no');
            $post_data=array(
                'member_id'=>$member_id,
                'money'=>$recharge_num,
                'order_state'=>1,
                'ordernum'=>$out_trade_no,
                'posttime'=>time(),
                'payway'=>'小程序账户充值',
            );
            $recharge=M('recharge');
            $res=$recharge->add($post_data);

            $result         = M('zj_member')->where(array('member_id'=>$member_id))->setInc('member_money',$recharge_num);
            if($result){
                $data['msg']    = '充值成功';
                $data['status'] = '1';
            }else{
                $data['msg']    = '充值失败';
                $data['status'] = '0';
            }
            $this->ajaxReturn($data);
        }
    }

    /* @author 鲍
     * 用户咨询付费
     * */
    public function pay(){
        $appid   =C('WXPAY.appid');
        $openid  =I('openid');
        $question_id=I('question_id');
        $mch_id  =C('WXPAY.mch_id');
        $key     =C('WXPAY.key');
        $out_trade_no = $mch_id. time();
        //支付金额
        $total_fee = I('money');
        $body = "用户提问付费";
        $total_fee = floatval($total_fee*100);
        A('WeixinPay');
        $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
        $return=$weixinpay->wxsmall_pay($question_id,$total_fee);
        //返回订单交易号码
        $return['out_trade_no'] = $out_trade_no;
        $this->ajaxReturn($return);
    }
    /* @author 鲍
     * 提问支付成功回调
     * */
    public function notify_question(){
        Vendor('WxSmall.WxPayPubHelper');
        $notify = new \Notify_pub();

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        //echo $returnXml;
        $log_name= "./Public/wxsmall_notify_url.log";//log文件路径
        log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
        if($notify->checkSign() == TRUE)
        {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【业务出错】:\n".$xml."\n");
            }
            else{
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name,"【支付成功】:\n".$xml."\n");
            }
            //商户自行增加处理流程,
            //例如：更新订单状态
            //例如：数据库操作
            //例如：推送支付完成信息
            $data=$notify->data;
            $attach=explode('|',$data['attach']);
            $question_id=$attach[0];//问题id
            $money=$attach[1];//金额，是乘以100以后的
            $question = M('question');
            $step=$question->where('id='.$question_id)->getField('step');
            if($step!=3){
                if($money==$data['total_fee']){
                    $info=M('question')->where('id='.$question_id)->field('member_id')->find();
                    $post_data=array(
                        'member_id'=>$info['member_id'],
                        'money'=>($data['total_fee']/100),
                        'order_state'=>1,
                        'ordernum'=>$data['out_trade_no'],
                        'posttime'=>time(),
                        'payway'=>'小程序咨询医生',
                    );
                    $recharge=M('recharge');
                    $res=$recharge->add($post_data);
                    if($res){
                        $res_question = $question->where('id='.$question_id)->setField('step',3);
                        if ($res_question) {
                            $nim_info = $question->where('id='.$question_id)->find();
                            SendTempmsg($nim_info['doc_id'], $question_id, $content = '', 0);
                            //$msg = $this->SendMessage($question_id); //发送短信验证码
                        }
                    }
                }
                else{
                    log_result($log_name,$question_id."【支付失败】\n");
                }
            }

        }
    }
}