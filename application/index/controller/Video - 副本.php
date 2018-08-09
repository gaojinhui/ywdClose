<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;
use think\image;

class Video extends Controller
{
    /*用户点击课程进入播放页面*/
    public function purchase(Request $request)
    {
        $param=$request->param();
        $where['v.id']=$param['id'];
        $w['id']=$param['id'];
        $where['p.openid']=$param['openid'];
        $where['o.r_state']=2;
        $where['o.r_orderstate']=0;
        $where['o.r_kcdqtime']=array('egt',time());
        //判断用户是否购买过本节课程
        $res=Db::name('person')
                ->alias('p')
                ->join("guard_order o","o.r_mid=p.id")
                ->join("guard_video v","v.id=o.r_video")
                ->where($where)
                ->find();
        if($res)
        {
            //用户已经购买过本节课程可以直接观看
            $rsult=Db::name('video')->where($w)->find();
            $data['num']=$rsult['num']+1;
            $rts=Db::name('video')->where($w)->update($data);
            $res['image']=str_replace("\\","/",$res['image']);
            return json(array('code'=>1,'list'=>$res));
        }
        else
        {
            //用户没有购买过本节课程
            $rgs=Db::name('video')->where($w)->find();
            $rgs['image']=str_replace("\\","/",$rgs['image']);
            return json(array('code'=>0,'list'=>$rgs));
        }
    }
    /*点击购买进入提交订单页面*/
    public function videoPay(Request $request)
    {
        $param=$request->param();
        $where['id']=$param['id'];
        $rs=Db::name('video')->where($where)->find();
        $rs['image']=str_replace("\\","/",$rs['image']);
        return json($rs);
    }
    /*评价视频*/
    public function pingjia(Request $request)
    {
        $param=$request->param();
        $where['r_mid']=Session::get('user');
        $where['r_orderstate']=1;
        $rs=Db::name('order')->field('series')->where($where)->find();
        if($rs['series']==2){
            $where['r_orderstate']=0;
            $where['r_video']=$param['video_id'];
            $data['evaluate']=1;
            $data['pinglun']=$param['pinglun'];
            $re=Db::name('video')->where($where)->update($data);
        }else{
            $data['uid']=Session::get('user');
            $data['pinglun']=$param['pinglun'];
            $data['evaluate']=1;
            $data['vide0_id']=$param['video_id'];
            $rs=Db::name('video_type')->where($data)->find();
            if($rs){
                $rs=Db::name('video_type')->update($data);
            }else{
                $rs=Db::name('video_type')->insert($data);
            }
        }
    }
    /*观看完视频进行标记*/
    public function watch_out(Request $request)
    {
        $param=$request->param();
        $where['r_mid']=Session::get('user');
        $where['r_video']=$param['video_id'];
        $where['r_orderstate']=1;
        $rs=Db::name('order')->field('series')->where($where)->find();
        if($rs['series']==2){
                $where['r_orderstate']=0;
                $data['type']=1;
                $re=Db::name('video')->where($where)->update($data);
        }else{
                $data['uid']=Session::get('user');
                $data['pinglun']=$param['pinglun'];
                $data['evaluate']=1;
                $data['vide0_id']=$param['video_id'];
                $rs=Db::name('video_type')->where($data)->find();
                if($rs){
                    $rs=Db::name('video_type')->update($data);
                }else{
                    $rs=Db::name('video_type')->insert($data);
                }
        }
    }
    /*查看视频的全部评价以及自己的评价*/
    public function evalt(Request $request)
    {
         $param=$request->param();
         $where['r_video']=$param['video_id'];
         $where['r_orderstate']=0;
         $rs=Db::name('order')->field('pinglun')->where($where)->select();
         $re=Db::name('video_type')->field('pinglun')->where($where)->select();
         $a='';//满意
         $b='';//不满意
         $c='';//一般
         foreach($rs as $v){
            if($v['pinglun']==1){
                $a=$a+1;
            }elseif($v['pinglun']==2){
                $b=$b+1;
            }elseif($v['pinglun']==3){
                $b=$b+1;
            }
         }
         foreach($re as $v){
            if($v['pinglun']==1){
                $a=$a+1;
            }elseif($v['pinglun']==2){
                $b=$b+1;
            }elseif($v['pinglun']==3){
                $b=$b+1;
            }
         }
         $w['r_mid']=Session::get('user');
         $w['r_orderstate']=1;
         $res=Db::name('order')->field('series')->where($w)->find();
         if($res['series']==2){
            $where['r_mid']=Session::get('user');
            $d=Db::name('order')->field('series')->where($where)->find();
         }else{
            $where['r_mid']=Session::get('user');
            $d=Db::name('video_type')->field('series')->where($where)->find();
         }
         $array['a']=$a;
         $array['b']=$b;
         $array['c']=$c;
         $array['d']=$d;
         echo json_encode($array);
    }
    /*上传用户反馈的视频*/
    public function upload_video()
    {
        $myfile=$_FILES["file"];  
        $tmp=$myfile['tmp_name'];  
        $a='uploads/'.time().'.mp4';  
        $path=ROOT_PATH .'public/'.$a ;  
        $data['f_img']=$a;  
        $data['use_id']=Session::get('user');
        if(!move_uploaded_file($tmp,$path)) die('视频上传失败');  
        $num=Db::name('email')->insert($data);
    }
    /*完成第三步列出反馈供用户选择*/
    public function vide_re(Request $request)
    {
        $param=$request->param();
        $where['video_id']=$param['video_id'];
        $rs=Db::name('video_return')->where($where)->find();
        if($rs){
            $res=Db::name('video_return')->where($where)->select();
        }else{
            $where['video_id']=0;
            $res=Db::name('video_return')->where($where)->select();
        }
        echo json_encode($res);
    }
    /*课程查询*/
    public function search(Request $request)
    {
        $param=$request->param();
        $key=$param['key'];
        $where="name like '%".$key."%' or remark like '%".$key."%'";
        $rs=Db::name('video')->where($where)->select();
        echo json_encode($rs);
    }
    /*课程列表显示*/
    public function video_list(Request $request)
    {
        $param=$request->param();
        if(empty($param)){
            $pid=146;
            $fen_id=151;
        }else{
            $pid=$param['pid'];
            $fen_id=$param['fen_id'];
        }
        $where['pid']=$pid;
        $where['fen_id']=$fen_id;
        $rs=Db::name('video')->where($where)->select();
        echo json_encode($rs);
    }
    /*点击购买发起支付*/
    public function payment(Request $request)
    {
        $param=$request->param();
        $openid=$param['openid'];
        $pricez=$param['pricez'];
        $viptype=$param['viptype'];
        $videoId=$param['videoId'];
        //生成订单号
        $orderid = time().rand(1000000000,9999999999);
        //查询用户id
        $where['openid']=$openid;
        $person=Db::name('person')->field('id')->where($where)->find();
        $data['r_order'] = $orderid; //唯一订单号
        $data['r_money']=$pricez;//标价金额
        $data['r_mid']=$person['id'];//购买用户id
        $data['r_state']=1;//支付状态待支付
        $data['r_strtime']=time();//订单生成时间
        $data['r_kcdqtime']=time();//课程到期时间
        $data['r_orderstate']=1;//订单的主ID 【0.子订单 1.主订单】
        $data['evaluate']=2;//1已经评价2待 评价
        $data['type']=2;//1已经观看2未观看
        if($viptype==0)
        {
            $data['series']=2;//1有全部观看的权利2只能看自己买的
            // 只购买单节课程
            $data['r_video']=$videoId;//单节课程id
        }
        elseif($viptype==1)
        {
            // 购买全部课程
            $data['series']=1;//1有全部观看的权利2只能看自己买的
        }
        elseif($viptype==2)
        {
            // 购买推荐课程
            $data['r_video']=$videoId;//单节课程id
        }
        elseif($viptype==3)
        {
            // 购买热门课程
            $data['r_video']=$videoId;//单节课程id
        }
        $rs=Db::name('order')->insert($data);
        die;
        $data=array(
                'appid'=>'',//小程序id
                'body'=>'购买商品支付',//商品描述
                'mch_id'=>'',//商户号
                'nonce_str'=>$this->nonce_str(),//随机字符串
                'notify_url'=>'http:',//通知地址
                'out_trade_no'=>$orderid,//商户订单号
                'openid'=>"'".$openid."'",
                'spbill_create_ip'=>'',//终端ip
                'total_fee'=>$pricez,//标价金额
                'trade_type'=>'JSAPI',//交易类型
            );
        $sign=$this->sign($data);//签名
        $data['sign']=$sign;
        $xmldata=$this->xml($data);//将数组转化为aml
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res=$this->http_request($url,$xmldata);//调用支付接口
        $request=$this->getxml($res);//将xml转化为数组
        //判断返回结果
        if($result['RETURN_CODE']=='SUCCESS')
        {
            $time=time();
            $info='';
            $info=array(
                    'appId'=>'',//小程序id
                    'timeStamp'=>"".$time."",//时间戳
                    'nonceStr'=>$this->nonce_str(),//随机字符串长度为32个字符串以下
                    'package'=>'prepay_id='.$result['PREPAY_ID'],//统一下单接口返回的prepay_id参数值，提交格式如：prepay_id=*
                    'signType'=>'MD5'//签名算法，暂支持MD5
                );
            $info['paySign']=$this->sign($info);
            $list=array('status'=>0,'msg'=>'success','list'=>$info);
        }else{
            $list=array('status'=>0,'msg'=>'fail');
        }
        return json($list);
    }
    // 随机32位字符串
    public function nonce_str()
    {
        $result = '';
        $str='QWERTYUIOPLKJHGFDSAZXCVBNMqwertyuiopasdfghjklzxcvbnm';
        for($i=0;$i<32;$i++)
        {
            $result .= $str[rand(0,48)];
        }
        return $result;
    }
    // 计算签名
    public function sign($data)
    {
        $key='';
        // 步骤一：按字典序排列参数
        ksort($data);
        $buff="";
        foreach($data as $k=>$v)
        {
            if($k!='sign' && $v!='' && !is_array($v))
            {
                $buff .= $k."=".$v."&";
            }
        }
        $buff=trim($buff,"&");
        // 步骤二：在string后面拼接key
        $string=$buff."&key=".$key;
        // 步骤三：MD5加密
        $string=md5($string);
        // 所有字符转化为大写
        $sign=strtoupper($string);
        return $sign;
    }
    // 返回xml数据
    public  function xml($data)
    {
        ksort($data);
        // 进行拼接数据
        $data_xml = "<xml>";
        foreach($data as $key=>$val)
        {
            if(is_numeric($val))
            {
                $data_xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
            else
            {
                $data_xml .= "<" . $key . "><![CDATA[" . $val . "]]</" . $key . ">";
            }
        }
        $data_xml .= "</xml>";
        return $data_xml;
    }
    public function http_request($url, $method = 'get', $data = '') 
    {
        $ch = curl_init(); //初始化
        $headers = array('Accept-Charset: utf-8');
        //设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url); //指定请求的URL
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method)); //提交方式
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //不验证SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //不验证SSL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //设置HTTP头字段的数组
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE5.01;Windows NT 5.0)'); //头的字符串

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); //自动设置header中的Referer:信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //提交数值
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //是否输出到屏幕上,true不直接输出
        $temp = curl_exec($ch); //执行并获取结果
        curl_close($ch);
        return $temp; //return 返回值
}
// 获取xml
    public function getxml($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p,$xml,$vals,$index);
        xml_parser_free($p);
        $data= "";
        foreach($index as $key=>$value)
        {
            if($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]['tag']];
            $value= $vals[$value[0]['value']];
            $data[$tag]=$value;
        }
        return $data;
    }
    //我的课程
    public function myVideo(Request $request)
    {
        $param=$request->param();
        $where['p.openid']=$param['openid'];
        $where['o.r_state']=2;
        $where['o.r_orderstate']=0;
        $where['o.r_kcdqtime']=array('egt',time());
        $rs=Db::name('person')
                ->alias('p')
                ->join("guard_order o","o.r_mid=p.id")
                ->join("guard_video v","v.id=o.r_video")
                ->where($where)
                ->limit(0,4)
                ->select();
        foreach($rs as $k=>$v)
        {
            $rs[$k]['image']=str_replace("\\","/",$v['image']);
        }
        return json($rs);
    }
    /*会员种类列表*/
    public function vipType()
    {
        $res=Db::name('vip')->select();
        return json($res);
    }
}