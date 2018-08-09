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
        //$where['v.id']=$param['id'];
        if($param['check']==1){
            $w['pid']=$param['id'];
        }
        else
        {
            $w['id']=$param['id'];
        }
        $rs=Db::name('video')->where($w)->find();
        $data['num']=$rs['num']+1;//视频点击量+1
        $rts=Db::name('video')->where($w)->update($data);//更新视频点击量
        // 判断课程是不是免费课程
        $attr = explode(',',$rs['urlList']);
        // foreach($attr as  $k=>$v){
        //     $urlList[$k]=explode('|',$v);
        //     $urlList[$k]['title']=$rs['title'];
        // }
        // $urlList[count($urlList)+1][0]=$rs['name'];
        // $urlList[count($urlList)][1]=$rs['url'];
        // $urlList[count($urlList)]['title']=$rs['title'];
        if(!empty($attr[0])){
            $i=1;
            foreach($attr as  $k=>$v){
               $urlList[$i]=explode('|',$v);
               $urlList[$i]['title']=$rs['title'];
               $i++;
            }
        }
        $urlList[0][0]=$rs['name'];
        $urlList[0][1]=$rs['url'];
        $urlList[0]['title']=$rs['title'];
        if(empty($urlList[0][0]))
        {
            $urlList='';
        }
        if($rs['audi']==1){
            //可以免费看
            return json(array('code'=>4,'list'=>$rs,'urlList'=>$urlList));
        }else{
                //必须付费看
                $where['p.openid']=$param['openid'];
                $where['o.r_state']=2;
                $where['o.r_kcdqtime']=array('egt',time());
                //查找用户订单是否存在
                $res=Db::name('person')
                        ->alias('p')
                        ->join("guard_order o","o.r_mid=p.id")
                        ->where($where)
                        ->find();
                if($res)
                    {
                        $p['type']=1;
                        $wp['r_order']=$res['r_order'];
                        $r=Db::name('order')
                        ->where($wp)
                        ->update($p);
                        //用户订单存在判断用户订单等级
                        if($res['series']==0){
                            //用户只能看购买的本节课程
                            if($res['r_video']==$param['id']){
                                //用户购买过本课程可以看
                                return json(array('code'=>0,'list'=>$rs,'urlList'=>$urlList));
                            }else{
                                //没买过不能看
                                return json(array('code'=>5,'list'=>$rs,'urlList'=>$urlList));
                            }
                        }elseif($res['series']==1){
                            //1观看全部 课程
                            return json(array('code'=>1,'list'=>$rs,'urlList'=>$urlList));
                        }elseif($res['series']==2){
                            //2只能全部推荐 课程     判断本节课程是否属于推荐课程
                            if($rs['fen_id']==2){
                                return json(array('code'=>2,'list'=>$rs,'urlList'=>$urlList));
                            }else{
                                return json(array('code'=>5,'list'=>$rs,'urlList'=>$urlList));
                            }
                        }elseif($res['series']==3){
                            //3有全部热门课程  判断本节课程是否属于热门课程
                            if($rs['fen_id']==1){
                                return json(array('code'=>3,'list'=>$rs,'urlList'=>$urlList));
                            }else{
                                return json(array('code'=>5,'list'=>$rs,'urlList'=>$urlList));
                            }
                        }
                    }
                    else
                    {
                        //用户没有购买过本节课程
                        return json(array('code'=>5,'list'=>$rs,'urlList'=>$urlList));
                    }
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
    /*观看完视频进行标记*/
    public function watch_out(Request $request)
    {
        $param=$request->param();
        $where['openid']=$param['openid'];
        $where['video_id']=$param['video_id'];
        $rs=Db::name('video_type')->where($where)->find();
        // 判断视频是否已经标记过如果未标记则进行标记如果标记过直接返回
        if($rs)
        {

        }
        else
        {
                $data['openid']=$param['openid'];
                $data['pinglun']=1;//默认评论满意
                $data['evaluate']=2;//没有评论
                $data['video_id']=$param['video_id'];
                $rs=Db::name('video_type')->insert($data);
        }
    }
    /*查看视频的全部评价*/
    public function evalt(Request $request)
    {
         $param=$request->param();
         $where['r_video']=$param['id'];
         $rs=Db::name('order')->field('pinglun')->where($where)->select();
         $a=60;//满意
         $b=50;//不满意
         $c=40;//一般
         foreach($rs as $v){
            if($v['pinglun']==1){
                $a=$a+1;
            }elseif($v['pinglun']==2){
                $b=$b+1;
            }elseif($v['pinglun']==3){
                $c=$c+1;
            }
         }
         $array['a']=$a;
         $array['b']=$b;
         $array['c']=$c;
         return json($array);
    }
    /*查看我的评价*/
    public function myping(Request $request)
    {
        $param=$request->param();
        $where['v.video_id']=$param['id'];
        $where['p.openid']=$param['openid'];
        $res=Db::name('video_ping')
                ->alias('v')
                ->join("guard_person p","p.id=v.user_id")
                ->where($where)
                ->find();
        if(!$res){
            $res['evaluate']=1;
        }
        return json($res);
    }
    /*评价视频*/
    public function pingjia(Request $request)
    {
        $param=$request->param();
        $where['openid']=$param['openid'];//用户openid
        $ping=$param['id'];//用户提交 的 评论分类1满意  2一般  3  不满意
        $res=Db::name('person')->field('id')->where($where)->find();//找出用户id
        $wh['id']=$param['videoid'];//视频 id
        $rs=Db::name('video')->where($wh)->find();//找出视频现有的评价
        if($ping==1)
        {
            $data['video_man']=$rs['video_man']+1;
        }
        else if($ping==2)
        {
            $data['video_yiban']=$rs['video_yiban']+1;
        }
        else if($ping==3)
        {
            $data['video_buman']=$rs['video_buman']+1;           
        }
        $rs=Db::name('video')->where($wh)->update($data);
        $w['user_id']=$res['id'];
        $w['video_id']=$wh['id'];
        $rts=Db::name('video_ping')->where($w)->find();
        if($rts)
        {
            $d['evaluate']=$ping;
            $d['createTime']=time();
            $r=Db::name('video_ping')->where($w)->update($d);
        }
        else
        {
            $d['user_id']=$res['id'];
            $d['video_id']=$wh['id'];
            $d['evaluate']=$ping;
            $d['createTime']=time();
            $r=Db::name('video_ping')->insert($d);
        }
        // 判断用户是否购买 过本课程 
        $wr['r_video']=$param['videoid'];
        $wr['r_mid']=$res['id'];
        $result=Db::name('order')->where($wr)->find();
        if($result){
                $r['evaluate']=1;
                $tr=Db::name('order')->where($wr)->update($r);
        }
        return json(array('code'=>1));
    }
    /*上传用户反馈的视频*/
    public function upload_video(Request $request)
    {
         $s3 = new \Aws\S3\S3Client([
            'version' => '2006-03-01',
            'region'  => 'us-west-2',
            'credentials' => [
                'key'    => '74e13b42-6c66-43e9-a147-f24225badf90',
                'secret' => 'df778d4b-506c-4ea7-bcf5-c8173c44ef59',
            ],
            'endpoint' => 'http://cosapi-wz.chinacache.com'
        ]);
        $param=$request->param();
        $myfile=$_FILES["file"]; 
        $tmp=$myfile['tmp_name']; 
        $data['openid']=$param['openid'];
        $where['openid']=$param['openid'];
        $w['p.openid']=$param['openid'];
        $w['o.r_state']=2;
        $w['o.r_kcdqtime']=array('egt',time());
        $data['ids']=$param['ids'];
         $bucket = 'fckjai-User-uploaded-video';
        $key ='ok_'.time().'.mp4';  
        $tr=$s3->putObject([
            'Bucket' => $bucket,
            'Key'    => $key,
            'SourceFile'   =>$tmp
        ]);
        $data['url']=$tr['ObjectURL']; 
        $data['body']='1';
        $data['stat']=2;
        $data['ftime']=time();
        if($param['c_type']=='4-4'){
            $data['videoid']='4-4';
        }else{
            $data['videoid']=$param['videoid'];
        }
        $data['type']=$param['c_type'];
        $data['emailGroup']=$param['emailGroup'];
        $data['title']=$param['title'];
        $num=Db::name('email')->insert($data);
         if($num)
         {
            // 用户反馈视频上传成功判断用户是否购买过本课程决定给用户返现
            $g=Db::name('person')
                    ->alias('p')
                    ->join("guard_order o","o.r_mid=p.id")
                    ->where($w)
                    ->find();
            if($g)
            {
                //判断本视频是否开通了返现
                $wh['id']=$param['videoid'];
               $res=Db::name('video')->field('fan_xian,return_amount')->where($wh)->find();
                if($res['fan_xian']==1)
                {
                    //开通了返现判断用户一共交了多少钱已经返了多少钱是否继续返现
                    $rs=Db::name('person')->where($where)->find();
                    if(($rs['c_money']-$rs['balance'])>$res['return_amount']){
                        $d['balance']=$rs['balance']+$res['return_amount'];
                        $r=Db::name('person')->where($where)->update($d);
                        $money=$res['return_amount'];
                    }
                } 
                else
                {
                    $money=0;
                }
             }
             else
             {
                    $money=0;
             }
            
            //如果找到用户数据更新没找到添加
                // $data['time']=strtotime(date('Y-m-d'));
                // $data['z_time']=strtotime( "previous monday" );
                // $data['m_time']=mktime(0,0,0,date('m'),1,date('Y'));
            // if($rtt)
            // {
                
            // }
            // else
            // {
            //     $person=Db::name('person')->where($where)->find();
            //     $data['score']=1;
            //     $data['uid']=$person['id'];
            //     $tr=Db::name('family')->where($whe)->insert($data);
            // }
        }
        //用户完成四步训练分数+10;默认给我加
            $whe['openid']=$where['openid'];
            $rtt=Db::name('person')
                          ->where($whe)->find();
            $d['fen']=$rtt['fen']+10;
                $tr=Db::name('person')
                          ->where($whe)->update($d);
        //记录用户每天完成
        $xun['openid']=$param['openid'];
        $xun['day_time']=strtotime(date('Y-m-d'));//当天零点时间
        $pres=Db::name('person_xun')->where($xun)->find();
        if($pres){
                $pdata['fen_p']=$pres['fen_p']+10;
                $prts=Db::name('person_xun')->where($xun)->update($pdata);
        }else{
            $pdata['openid']=$param['openid'];
            $pdata['day_time']=strtotime(date('Y-m-d'));//当天零点时间
            $pdata['fen_p']=10;
            $pdata['videoid']=$param['videoid'];
            $prs=Db::name('person_xun')->insert($pdata);
        }
        return json($money);
    }
    /*完成第三步列出反馈供用户选择*/
    public function vide_re(Request $request)
    {
        $param=$request->param();
        $where['video_id']=$param['videoid'];
        $rs=Db::name('video_return')->where($where)->find();
        if($rs){
            $res=Db::name('video_return')->where($where)->select();
        }else{
            $where['video_id']=0;
            $res=Db::name('video_return')->where($where)->select();
        }
        return json($res);
    }
    /*课程查询*/
    public function search(Request $request)
    {
        $param=$request->param();
        $key=$param['inputvalue'];
        $where="name like '%".$key."%' or remark like '%".$key."%'";
        $rs=Db::name('video')->where($where)->select();
        foreach($rs as $k=>$v)
        {
            $rs[$k]['image']=str_replace("\\","/",$v['image']);
        }
        return json($rs);
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
        $data['r_state']=1;//支付状态待支付1未支付2已经支付
        $data['r_strtime']=time();//订单生成时间
        $data['r_kcdqtime']=time();//课程到期时间
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
       // die;
        $dat=array(
                'appid'=>'wx6208c2b1012d08cc',//小程序id
                'body'=>'购买商品支付',//商品描述
                'mch_id'=>'1504953611 ',//商户号
                'nonce_str'=>$this->nonce_str(),//随机字符串
                'notify_url'=>'https://zkwx.lxuemall.com/index/video/payment',//通知地址
                'openid'=>'owdcn40DFpMLtepKktReEFtHhrDk',
                'out_trade_no'=>$orderid,//商户订单号
                'spbill_create_ip'=>$_SERVER['SERVER_ADDR'],//终端ip
                'total_fee'=>10,//标价金额
                'trade_type'=>'JSAPI',//交易类型
            );dump($dat);
        $sign=$this->sign($dat);//签名
        $dat['sign']=$sign;
        $xmldata=$this->xml($dat);//将数组转化为aml
        $post_xml = '<xml>
           <appid>wx6208c2b1012d08cc</appid>
           <body>1234556788</body>
           <mch_id>1504953611</mch_id>
           <nonce_str>'.$this->nonce_str().'</nonce_str>
           <notify_url>https://zkwx.lxuemall.com/index/video/payment</notify_url>
           <openid>'.$openid.'</openid>
           <out_trade_no>'.$orderid.'</out_trade_no>
           <spbill_create_ip>'.$_SERVER['SERVER_ADDR'].'</spbill_create_ip>
           <total_fee>10</total_fee>
           <trade_type>JSAPI</trade_type>
           <sign>'.$sign.'</sign>
        </xml> ';
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res=$this->http_request($url,$post_xml);//调用支付接口
        dump($res);
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
    function http_request($url, $data = null, $headers = array())
        {
            $curl = curl_init();
            if (count($headers) >= 1) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt($curl, CURLOPT_URL, $url);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
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
    // 课程顶级分类
    public  function  videoType()
    {
        $where['parentid']=112;
        $res=Db::name('menu')->field('id,title')->where($where)->select();
        return json($res);
    }
    // 训练计划
    public function  xunlian(Request $request)
    {
        $param=$request->param();
        $where['video_id']=$param['videoid'];
        $w['openid']=$param['openid'];
        $w['videoid']=$param['videoid'];
        $res=Db::name('program_person')->where($w)->select();
        if(!$res){
            $res=Db::name('program')->where($where)->select();
        }else{
            // 如果我的训练计划存在查看 我的训练 记录 
            $wp['openid']=$param['openid'];
            $rts=Db::name('record')->where($wp)->order('id desc')->find();
            if($rts){
                //判断 训练记录是不是超过了 24小时超 过 了直接训练 记录+1训练下一天如果为7开始 第一天
                if($rts['day'] < 7 && $rts['day']>=1){
                    //判断训练记录的时间 跟现在的时间差是不是在 24下小时内超出训练第二天
                    $time=time();
                    $rtime=$time-$rts['daytime'];
                    if($rtime>=86400){
                        // 开始下 一天的训练
                        if($rts['day']==7){
                            $w['day']=1;
                            $data['day']=1;
                            $data['openid']=$param['openid'];
                            $data['daytime']=time();
                            $data['videoid']=$param['videoid'];
                            $rs=Db::name('record')->insert($data); 
                        }else{
                            $w['day']=$rts['day']+1;
                            // 同时记录下 来 训练 记录
                            $data['day']=$rts['day']+1;
                            $data['openid']=$param['openid'];
                            $data['daytime']=time();
                            $data['videoid']=$param['videoid'];
                            $rs=Db::name('record')->insert($data); 
                        }
                        $res=Db::name('program_person')->where($w)->select();
                    }else{
                        // 继续 当天的训练 
                        $w['day']=$rts['day'];
                        $res=Db::name('program_person')->where($w)->select();
                    }
                }
            }else{
                //如果训练 记录不存在直接拿第一天 的计划开始 训练 
                $w['day']=1;
                $res=Db::name('program_person')->where($w)->select();
                $data['day']=1;
                if(!$res){
                    // 如果第一天的训练计划没有 查找 第二天的
                    $w['day']=2;
                    $data['day']=2;
                    $res=Db::name('program_person')->where($w)->select();
                    if(!$res){
                        // 如果第二天 的不 存在查找第三天的
                         $w['day']=3;
                         $data['day']=3;
                        $res=Db::name('program_person')->where($w)->select();
                        if(!$res){
                            // 如果第三天 的不 存在查找第四天的
                             $w['day']=4;
                             $data['day']=4;
                            $res=Db::name('program_person')->where($w)->select();
                            if(!$res){
                                // 如果第四天 的不 存在查找第五天的
                                 $w['day']=5;
                                 $data['day']=5;
                                $res=Db::name('program_person')->where($w)->select();
                                if(!$res){
                                    // 如果第五天 的不 存在查找第六天的
                                     $w['day']=6;
                                     $data['day']=6;
                                    $res=Db::name('program_person')->where($w)->select();
                                    if(!$res){
                                        // 如果第六天 的不 存在查找第七 天的
                                         $w['day']=7;
                                         $data['day']=7;
                                        $res=Db::name('program_person')->where($w)->select();
                                    }
                                }
                            }
                        }
                    }
                }
                $data['openid']=$param['openid'];
                $data['daytime']=time();
                $data['videoid']=$param['videoid'];
                $rs=Db::name('record')->insert($data);
            }
        }
        $time='';
        $i=1;
        foreach($res  as $v){
            if($i==count($res)){
                $time+=$v['group_time']*$v['group_num']+$v['group_rest']*($v['group_num']-1);
            }else{
                $time+=$v['group_time']*$v['group_num']+$v['group_rest']*$v['group_num'];
            }
            $i++;
        }
        $result[0]=$res;
        $result[1]=$time;
        return json($result);
    }
    // 上传视频页面获取观看视频的名称
    public function uploadVideoArticle(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['videoId'];
            $rs=Db::name('video')->where($where)->find();
            $attr = explode(',',$rs['urlList']);
            if(empty($attr[0])){
                $urlList[0][0]=$rs['name'];
                $urlList[0][1]=$rs['url'];
                $urlList[0]['title']=$rs['title'];
            }else{
                $i=1;
                foreach($attr as  $k=>$v){
                    $urlList[$i]=explode('|',$v);
                    $urlList[$i]['title']=$rs['title'];
                    $i++;
                }
                $urlList[0][0]=$rs['name'];
                $urlList[0][1]=$rs['url'];
                $urlList[0]['title']=$rs['title'];
            }
            $urlList['length']=count($urlList);
            return json($urlList);
    }
    // 
    public function jsonTitle(Request $request)
    {
        $param=$request->param();
        $where['id']=$param['videoid'];
        $res=Db::name('menu')->field('title')->where($where)->find();
        return json($res);
    }
    public function jsonTitletr(Request $request)
    {
            $param=$request->param();
            $where['pid']=$param['videoid'];
            $rs=Db::name('video')->where($where)->find();
            $attr = explode(',',$rs['urlList']);
            foreach($attr as  $k=>$v){
                $urlList[$k]=explode('|',$v);
                $urlList[$k]['title']=$rs['title'];
            }
            $urlList[count($urlList)][0]=$rs['name'];
            $urlList[count($urlList)-1][1]=$rs['url'];
            $urlList[count($urlList)-1]['title']=$rs['title'];
            return json($urlList);
    }
}