<?php
namespace app\user\controller;
use app\user\model\Agreement;
use app\user\model\Factoring;
use app\user\model\Gathering;
use app\user\model\Logbook;
use app\user\model\Myfinancing as F;
use app\user\model\Mytender;
use app\user\model\News;
use app\user\model\Payment;
use app\user\model\Payrec as P;
use app\user\model\Financeable as Fin;
use app\user\model\Profile;
use app\user\model\Receivable;
use app\user\model\User;
use app\user\model\Userpay;
use app\user\model\Userpayverify as Verify;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Index extends author
{
    public function index()
    {
        $list = News::all(function($query){
            $query->where(['newsType'=>3])->limit(3)->order('id', 'asc');
        });
        $uid = Session::get('userinfo')['uid'];
        $userinfo =User::get($uid);
        $auth_status=$userinfo->profile->auth_status;
        $member_status=$userinfo->profile->member_type;
        $this->assign("list",$list);
        $city =json_decode(html_postdata("http://ip.taobao.com/service/getIpInfo.php?ip=114.215.168.20"),true);

        $this->assign("result",array_merge($userinfo->profile->toArray(),$userinfo->toArray()));
        return $this->fetch("user/ucenter",['auth_status'=>$auth_status,'member_status'=>$member_status]);
    }

    public function bangca(){
        return view("user/bindCA");
    }
    public function loginout(){
        Session::delete('userinfo');
        //jsonMsg("退出成功","0");
        return $this->redirect('/');
    }
    public function contract_inquire(){
        return view("user/contract_inquire");
    }
    public function contract_contracts(){

        return view("user/contract_contracts");
    }
    public function contract_add_day(){
        $trans_type =Db::name("transport_type")->select();
        $traded_type =Db::name("traded_type")->select();
        $price_type =Db::name("price_type")->select();
        $unit_price =Db::name("unit_price")->select();
        $quality_type =Db::name("quality_type")->select();
        $user_tpl =Db::name("user_tpl")->select();
        $this->assign("trans_type",$trans_type);
        $this->assign("traded_type",$traded_type);
        $this->assign("price_type",$price_type);
        $this->assign("unit_price",$unit_price);
        $this->assign("quality_type",$quality_type);
        $this->assign("user_tpl",$user_tpl);
        return $this->fetch("user/contract_add_day",["title"=>'增加日常合同']);
    }
    public function contract_add_year(){
        $trans_type =Db::name("transport_type")->select();
        $traded_type =Db::name("traded_type")->select();
        $price_type =Db::name("price_type")->select();
        $unit_price =Db::name("unit_price")->select();
        $quality_type =Db::name("quality_type")->select();
        $user_tpl =Db::name("user_tpl")->select();
        $this->assign("trans_type",$trans_type);
        $this->assign("traded_type",$traded_type);
        $this->assign("price_type",$price_type);
        $this->assign("unit_price",$unit_price);
        $this->assign("quality_type",$quality_type);
        $this->assign("user_tpl",$user_tpl);
        return $this->fetch("user/contract_add_year",["title"=>'增加年度合同']);
    }

    public function add_contract(Request $request){
        if($request->isAjax()){
            $user = new Agreement();
            $rs=$user->save_agreement();
            if($rs){jsonMsg("提交成功","0");}else{jsonMsg("提交失败","1");}
        }else{
            jsonMsg("非法提交","1");
        }
    }
    public function save_template(Request $request){
        if($request->isAjax()){
            $user = new Agreement();
            $rs=$user->save_tpl();
            if($rs){jsonMsg("保存成功","0");}else{jsonMsg("保存失败","1");}
        }else{
            jsonMsg("非法提交","1");
        }
    }
    public function get_template(Request $request){
        if($request->isAjax()){
            $user = new Agreement();
            $rs=$user->get_tpl();
            if($rs){jsonMsg("获取成功","0",$rs);}else{jsonMsg("获取模板失败","1");}
        }else{
            jsonMsg("非法提交","1");
        }
    }
    public function get_contract_con(Request $request){
        if($request->isAjax()){
            $user = new Agreement();
            $rs=$user->get_contract();
            jsonMsg("success","0",$rs);
        }else{
            jsonMsg("非法提交","1");
        }
    }
    public function show_financing(){
        return view("user/financing");
    }
    public function show_contract_list(){
        return view("user/contract_list");
    }
    public function show_iframe(){
        $uid = Session::get('userinfo')['uid'];
        $userinfo =User::get($uid);
        $auth_status=$userinfo->profile->auth_status;
        $member_status=$userinfo->profile->member_type;
        return view("user/iframe",['auth_status'=>$auth_status,'member_status'=>$member_status]);
    }
    public function show_main(){
        return view("user/financing_main");
    }
    public function show_uncleared(){
        return view('user/financing_uncleared');
    }
    public function show_payrec(){
        return view('user/financing_payrec',["title"=>'还款记录']);
    }
    public function show_myfinancing(){
        return view('user/financing_myfinancing',["title"=>'我的融资']);
    }
    public function show_financeable(){
        return view('user/financing_financeable',["title"=>'可融资合同']);
    }
    public function show_factoring(){
        return view('user/financing_factoring',["title"=>'保理合同']);
    }
    public function show_apply(){
        return view('user/financing_apply',["title"=>'当前融资申请']);
    }
    public function show_accrue_financing(){
        return view('user/financing_accruefin',["title"=>'累积融资']);
    }
    public function show_accrue_payment(){
        return view('user/financing_accrue_payment',["title"=>'累积还款']);
    }

    public function show_accure_interest(){
        return view('user/financing_accure_interest',["title"=>'累积未结清']);
    }
    public function show_uncleared_interest(){
        return view('user/financing_uncleared_interest',["title"=>'未结清']);
    }
    public function get_myfinancing(Request $request){//获取我的融资列表
        if($request->isAjax()){
            $rs = F::all();
             jsonMsg("success",0,$rs);
        }else{
             jsonMsg("非法提交");
        }
    }
    public function get_payrec(Request $request){//获取我的还款记录
        if($request->isAjax()){
            $rs = P::all();
            jsonMsg("success",0,$rs);
        }else{
            jsonMsg("非法提交");
        }
    }
    public function get_financeable(Request $request){//可融资合同列表
        if($request->isAjax()){
            $rs = Fin::all();
            if($rs) {
                collection($rs)->append(['status_text','type_text','endtime_text'])->toArray();
            }
            jsonMsg("success",0,$rs);
        }else{
            jsonMsg("非法提交");
        }
    }

    public function get_financeable_detail(Request $request){
        $rs = Fin::get($request->param('id'));
        $result = $rs->append(['status_text','type_text','endtime_text','starttime_text'])->toArray();
        $this->assign("result",$result);
        $this->assign('unit_type',$rs->unitprice->unit_type);
        $this->assign('weight_type',$rs->unitweight->unit_type);
        return $this->fetch('user/financing_financeable_detail',['title'=>'融资合同详情']);
    }

    public function get_factoring(Request $request){
        if($request->isAjax()){
            $rs = Factoring::all();
            if($rs) {collection($rs)->append(['starttime_text','endtime_text'])->toArray();}
            jsonMsg("success",0,$rs);
        }else{jsonMsg("非法提交");}
    }

    public function uploadhandle(){
        $file = request()->file('file');
        if($file){
            $info = $file->rule('date')->validate(['ext'=>'doc,docx,xlsx,xls,pptx,ppt'])->move(ROOT_PATH . 'public/doc');
            if($info){
                $destfilename='D:/phpStudy/PHPTutorial/WWW/trade/public/pdf/'.$info->getFilename();
                if($info->getExtension()=='docx'){
                    $destfilename=str_replace('.docx','.pdf',$destfilename);
                }else{
                    $destfilename=str_replace('.doc','.pdf',$destfilename);
                }
                $srcfilename='D:/phpStudy/PHPTutorial/WWW/trade/public/doc/'.$info->getSaveName();
                //dump($srcfilename.'--'.$destfilename);
                doc_to_pdf($srcfilename,$destfilename);
                //dump($srcfilename.'---------'.$destfilename);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function show_mytender(){
        return view("user/my_tender");
    }
    public function mytender_list($status=1,Request $request){
        if($request->isAjax()){
            $list = Mytender::all(['uid'=>Session::get('userinfo')['uid']]);
            if($list){
                $list = collection($list)->append(['status_text','starttime_text','endtime_text'])->toArray();
            }
            jsonMsg("success",0,$list);

        }else{
            jsonMsg("非法操作");
        }
    }

    public function addfile(){
        $file = request()->file('file');
        if($file){
            $info = $file->rule('date')->validate(['ext'=>'doc,docx,PDF,rar,zip'])->move(ROOT_PATH . 'public/file');
            if($info){
                $rs = 'public/file/'.$info->getSaveName();
                jsonMsg("success",0,$rs);
            }else{
                jsonMsg("上传失败",1,$file->getError());
            }
        }
    }

    public function show_addtender(){
        return view("user/show_tender");
    }
    public function addtender(Request $request){
        $param = $request->param();
        $where['number']='ZB'.date("Ymd");
        $where['uid']=Session::get('userinfo')['uid'];
        isset($param['starttime']) AND $where['starttime']=strtotime($param['starttime']);
        isset($param['fileurl']) AND $where['fileurl']=$param['fileurl'];
        isset($param['title']) AND $where['title']=$param['title'];
        isset($param['endtime']) AND $where['endtime']=strtotime($param['endtime']);
        isset($param['kbfs']) AND $where['kbfs']=intval($param['kbfs']);
        isset($param['zbfs']) AND $where['zbfs']=intval($param['zbfs']);
        isset($param['cgfs']) AND $where['cgfs']=intval($param['cgfs']);
        isset($param['delivery'])AND $where['delivery']=strtotime($param['delivery']);
        isset($param['address']) AND $where['address']=$param['address'];
        isset($param['editorValue']) AND $where['introduce']=$param['editorValue'];
        $tender = new Mytender();
        $rs= $tender->save($where);
        if($rs){jsonMsg("操作成功",0);}else{jsonMsg('操作失败');}
    }

    public function show_tenderdetail(Request $request){
        $id = $request->param("id");
        $tender = Mytender::get(['id'=>$id]);
        $company = $tender->profile->company;
        $arr=$tender->append(['kbfs_text','zbfs_text','cgfs_text','starttime_text','endtime_text'])->toArray();
        $arr['company']=$company;
        $arr['day']=ceil(($arr['endtime']-$arr['starttime'])/(24*3600));
        $this->assign("result",$arr);
        return $this->fetch("user/tender_detail");
    }

    public function tender_status(Request $request){
        $id = $request->param('id');
        $status = $request->param('status');
        $tender = new Mytender();
        $rs= $tender->save(['status'=>$status],['id'=>$id]);
        if($rs){jsonMsg("success",0);}else{jsonMsg("error",1);}
    }
    public function show_account(Request $request){
        $uid = Session::get('userinfo')['uid'];
        $user = User::get($uid);
        $userprofile =Profile::get($uid);
        $user = $user->append(['status_text'])->hidden(['password'])->toArray();
        $profile = $userprofile->append(['auth_text','member_type_text','trade_text'])->toArray();
        $userinfo = array_merge($user,$profile);
        $this->assign("result",$userinfo);
        return $this->fetch("user/account");
    }

    public function upload(){
        $file = request()->file('image');
        if($file){
            $info = $file->validate(['size'=>600000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                jsonMsg("操作成功",0,"/public/uploads/".$info->getSaveName());
            }else{
                jsonMsg( $file->getError());
            }
        }
    }
    public function update_userinfo(Request $request){
        if($request->isAjax()){
            $param = $request->param();
            $uid=Session::get('userinfo')['uid'];
            $userinfo =User::get($uid);
            isset($param['user_phone']) AND $userinfo->profile->user_phone=$param['user_phone'];
            isset($param['company']) AND $userinfo->profile->company=$param['company'];
            isset($param['company_short']) AND $userinfo->profile->company_short=$param['company_short'];
            isset($param['trade']) AND $userinfo->profile->trade=$param['trade'];
            isset($param['txtfrom']) AND $userinfo->profile->city=$param['txtfrom'];
            isset($param['txtfrom']) AND $userinfo->username=$param['username'];
            isset($param['txtfrom']) AND $userinfo->profile->company_address=$param['address'];
            isset($param['companyphone']) AND $userinfo->profile->company_phone=$param['companyphone'];
            isset($param['email']) AND $userinfo->profile->email=$param['email'];
            isset($param['fax']) AND $userinfo->profile->fax=$param['fax'];
            isset($param['bank_address']) AND $userinfo->profile->bank_address=$param['bank_address'];
            isset($param['bank_number']) AND $userinfo->profile->bank_number=$param['bank_number'];
            isset($param['cardid']) AND $userinfo->profile->cardid=$param['cardid'];
            isset($param['corporation']) AND $userinfo->profile->corporation=$param['corporation'];
            isset($param['establishment']) AND $userinfo->profile->licence=$param['establishment'];
            isset($param['website']) AND $userinfo->profile->website=$param['website'];
            isset($param['content']) AND $userinfo->profile->company_introudce=$param['content'];
            isset($param['licensesimg']) AND  $userinfo->profile->licenceimg=$param['licensesimg'] AND $userinfo->profile->auth_status=2;
            isset($param['cardimg']) AND $userinfo->profile->cardimg=$param['cardimg'] AND $userinfo->profile->member_type=2;
            $rs=$userinfo->profile->save();
            if($rs){jsonMsg("success",0);}else{jsonMsg("信息没有做更新",1);}
        }else{jsonMsg("非法操作");}
    }
    public function show_pay_apply(){
        $uid=Session::get('userinfo')['uid'];
        $apply_wait_num =Userpay::where(['uid'=>$uid,'status'=>1])->count();
        $apply_finish_num =Userpay::where(['uid'=>$uid,'status'=>2])->count();
        $apply_num=$apply_wait_num+$apply_finish_num;
        return view("user/pay_apply",['title'=>"支付申请",'finish_num'=>$apply_finish_num,'wait_num'=>$apply_wait_num,'apply_num'=>$apply_num]);
    }
    public function handle_pay_apply(Request $request,$status=0){
        $uid=Session::get('userinfo')['uid'];
        $where['uid'] = $uid;
        if($status>0){$where['status'] = $status;}
        if($request->isAjax()){
            $rs =Userpay::where($where)->select();
            if($rs){
                $rs=collection($rs)->append(['paytime_text','status_text','paystatus_text'])->toArray();
            }
            isset($rs) and jsonMsg("success",0,$rs);
        }else{
            jsonMsg("非法请求");
        }
    }
    public function handle_pay_search(Request $request,$starttime,$endtime,$status=1){
        if(!$request->isAjax()){jsonMsg("非法请求");}
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        empty($starttime) AND jsonMsg('没有开始时间');
        empty($endtime) AND jsonMsg('没有开始时间');
        $paylist =new Userpay();
        $rs = $paylist
            ->where('apply_time','between',"$starttime,$endtime")
            //->where('status','eq',$status)
            ->select();
       if($rs){
           $rs=collection($rs)->append(['paytime_text','status_text','paystatus_text'])->toArray();
           jsonList("success",0,$rs,count($rs));
       }else{
           jsonMsg('error',1);
       }
    }

    public function show_pay_verify(){
        return view("user/pay_verify");
    }
    public function pay_verify_list(Request $request){
        if(!$request->isAjax()){jsonMsg("非法请求");}
        $uid=Session::get('userinfo')['uid'];
        $list = Verify::all(['uid'=>$uid]);
        if($list){
            $list=collection($list)->append(['paytime_text','status_text','paystatus_text'])->toArray();
            jsonList("success",0,$list,count($list));
        }else{
            jsonMsg("error");
        }
    }

    public function show_receivable(){
        return view("user/receivable",['title'=>'收款确认']);
    }
    public function get_receiptlist(){
        $uid=Session::get('userinfo')['uid'];
        $list = Receivable::all(['uid'=>$uid]);
        if($list){
            $list=collection($list)->append(['paytime_text','status_text','paystatus_text','typestatus_text'])->toArray();
            jsonList("success",0,$list,count($list));
        }else{
            jsonMsg("error");
        }
    }
    public function search_receiptlist(Request $request,$starttime,$endtime){
        if(!$request->isAjax()){jsonMsg("非法请求");}
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        empty($starttime) AND jsonMsg('没有开始时间');
        empty($endtime) AND jsonMsg('没有开始时间');
        $paylist =new Receivable();
        $rs = $paylist
            ->where('apply_time','between',"$starttime,$endtime")
            ->select();
        if($rs){
            $rs=collection($rs)->append(['paytime_text','status_text','paystatus_text'])->toArray();
            jsonList("success",0,$rs,count($rs));
        }else{
            jsonMsg('暂时没有数据',1);
        }

    }

    public function show_paymentlist(){
        return view("user/payments",['title'=>'收支列表']);
    }
    public function  get_paymentlist(){
        $uid=Session::get('userinfo')['uid'];
        $list = Payment::all(['uid'=>$uid]);
        if($list){
            $list=collection($list)->append(['paytime_text','status_text','paystatus_text','typestatus_text','finishtime_text'])->toArray();
            jsonList("success",0,$list,count($list));
        }else{
            jsonMsg("error");
        }
    }

    public function search_payment(Request $request,$starttime="",$endtime=""){
        if(!$request->isAjax()){jsonMsg("非法请求");}
        empty($starttime) AND jsonMsg('没有开始时间');
        empty($endtime) AND jsonMsg('没有开始时间');
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        $paylist =new Payment();
        $rs = $paylist
            ->where('apply_time','between',"$starttime,$endtime")
            ->select();
        if($rs){
            $rs=collection($rs)->append(['paytime_text','status_text','paystatus_text','typestatus_text','finishtime_text'])->toArray();
            jsonList("success",0,$rs,count($rs));
        }else{
            jsonMsg('暂时没有数据',1);
        }

    }
    public function get_gathering_list(Request $request){
        if(!$request->isAjax()){jsonMsg("非法访问");}
        $uid=Session::get('userinfo')['uid'];
        $list = Gathering::all(['uid'=>$uid]);
        if($list){
            $list=collection($list)->append(['paytime_text','status_text','paystatus_text','typestatus_text','finishtime_text'])->toArray();
            jsonList("success",0,$list,count($list));
        }else{
            jsonMsg("error");
        }
    }

    public function show_log_book(){
        return view("user/log_book");
    }
    public function show_settlementlog(){
        return view("user/log_settlment");
    }
    public function get_logbook(Request $request){
        if(!$request->isAjax()){jsonMsg("非法请求");}
        $uid=Session::get('userinfo')['uid'];
        $list = Logbook::all(['uid'=>$uid]);
        if($list){
            $list=collection($list)->append(['time_text','status_text'])->toArray();
            jsonList("success",0,$list,count($list));
        }else{
            jsonMsg("error");
        }
    }

    public function search_logbok(Request $request,$starttime="",$endtime=""){
        if(!$request->isAjax()){jsonMsg("非法请求");}
        empty($starttime) AND jsonMsg('没有开始时间');
        empty($endtime) AND jsonMsg('没有开始时间');
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        $paylist =new Logbook();
        $rs = $paylist
            ->where('sign_time','between',"$starttime,$endtime")
            ->select();
        if($rs){
            $rs=collection($rs)->append(['time_text','status_text'])->toArray();
            jsonList("success",0,$rs,count($rs));
        }else{
            jsonMsg('暂时没有数据',1);
        }
    }

}
