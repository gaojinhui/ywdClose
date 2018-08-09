<?php
namespace app\index\controller;
use app\index\model\User;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Family extends Controller
{
    /*家庭成员排行*/
    public function rankings(Request $request)
    {
        $param=$request->param();
        $where['p.openid']=$param['openid'];
        // 找出我的分数
        $my=Db::name('person')->alias('p')->field('fen')->where($where)->find();
        $rs=Db::name('person')
                    ->alias('p')
                    ->field('y.name,f.score')
                    ->join("guard_family f","f.uid=p.id")
                    ->join("guard_family_from y","f.name_id=y.id")
                    ->where($where)->order('f.score desc')->select();
        $rs[count($rs)+1]['name']='我';
        $rs[count($rs)]['score']=$my['fen'];
        $date = array_column($rs, 'score');
        array_multisort($date,SORT_DESC,$rs);
        return json($rs);
    }
    /*添加家庭成员页面已有的家庭成员
        列出家庭成员点击完成
    */
    public function existence(Request $request)
    {
        $param=$request->param();
        $where['p.openid']=$param['openid'];
        $rs=Db::name('person')
                    ->alias('p')
                    ->join("guard_family f","f.uid=p.id")
                    ->join("guard_family_from y","f.name_id=y.id")
                    ->where($where)->order('y.id desc')->select();
        return json($rs);
    }
    /*家庭成员完成增加分数*/
    public function family_score(Request $request)
    {
            $param=$request->param();
            $where['openid']=$param['openid'];
            $arr=$param['arr'];
            $arr=substr($arr,0,strlen($arr)-1);
            $a=explode(',', $arr);dump($a);
            //查找用户id
            $personId=Db::name('person')->field('id')->where($where)->find();
            foreach($a as $v){
                $w['name_id']=$v;
                $w['uid']=$personId['id'];
                $rs=Db::name('family')->field('score')->where($w)->find();
                $data['score']=10;
                $rs=Db::name('family')->where($w)->update($data);
            }
            if($rs){
                return json(array('code'=>1,'message'=>'恭喜已经完成今日计划'));
            }else{
                return json(array('code'=>0,'message'=>'提交失败请重新提交'));
            }
    }
    /*家庭成员选择列表*/
    public function choice_list()
    {
            $rs=Db::name('family_from')->limit(0,6)->select();
            return json($rs);
    }
    /*添加家庭成员*/
    public function add_family(Request $request)
    {
            $param=$request->param();
            $why['openid']=$param['openid'];
            // 根据传过来的用户openid查找用户id
            $res=Db::name('person')->field('id')->where($why)->find();
            // 用户传过来的家庭成员id   json格式数据转换成数组
            $lists=json_decode($param['familyId']);
            // 根据用户id查询用户已经添加的家庭成员
            $where['uid']=$res['id'];
            $rts=Db::name('family')->where($where)->select();
            $list=array();
            if($rts)
            {
                if($lists)
                {
                    $rgs=Db::name('family')->where($where)->delete();
                    foreach($lists as $v)
                    {
                        $where['name_id']=$v;
                        $rgs=Db::name('family')->insert($where);
                    }
                    if($rgs){
                        return json(array('code'=>1,'message'=>'添加成功'));
                    }else{
                        return json(array('code'=>0,'message'=>'添加失败'));
                    }
                }
                else
                {
                   $rgs=Db::name('family')->where($where)->delete();
                    if($rgs)
                    {
                        return json(array('code'=>2,'message'=>'取消成功'));  
                    } 
                }
            }
            else
            {
                // 如果用户还没有添加过家庭成员
                foreach($lists as $v)
                {
                    $where['name_id']=$v;
                    $rgs=Db::name('family')->insert($where); 
                }
                if($rgs){
                    return json(array('code'=>1,'message'=>'添加成功'));
                }else{
                    return json(array('code'=>0,'message'=>'添加失败'));
                }
            }
    }
    public function deep_in_array($value, $array) {   
        foreach($array as $item) {   
            if(!is_array($item)) {   
                if ($item == $value) {  
                    return true;  
                } else {  
                    continue;   
                }  
            }   
                
            if(in_array($value, $item)) {  
                return true;      
            } else if($this->deep_in_array($value, $item)) {  
                return true;      
            }  
        }   
        return false;   
    }
    /*所有家庭进行排行*/
    public function ranking()
    {
        //查找所有用户
        $res=Db::name('person')->field('id,fen,nickName,openid')->select();
             $rs=Db::name('person')
                ->alias('p')
                ->field('p.nickName,sum(f.score),f.uid')
                ->join('guard_family f','p.id=f.uid')
                ->group('f.uid')
                ->order('sum(f.score)')
                ->select();
        foreach($res as $k=>$v)
        {
            foreach($rs  as $val)
            {
                if($v['id']==$val['uid'])
                {
                    $res[$k]['fen']=$v['fen']+$val['sum(f.score)'];
                }
            }
        }
            foreach($res as $k=>$v)
            {
                if($v['fen']==NULL)
                {
                        $v['fen']=0;
                }
                $res[$k]['sum']=$v['fen'];
            }
        $date = array_column($res, 'sum');
        array_multisort($date,SORT_DESC,$res);
        $nu=1;
        foreach($res as $k=>$v)
        {
            $res[$k]['nu']=$nu;
            $nu++;
        }
        return json($res);
    }
    /*家庭总分数*/
    public function family_scor(Request $request)
    {
            $param=$request->param();
            $where['p.openid']=$param['openId'];
            // 找出我的id与我的得分
            $w=Db::name('person')
                ->alias('p')
                ->where($where)
                ->find();
            $rs=Db::name('person')
                ->alias('p')
                ->field('p.*,sum(f.score)')
                ->join('family f','p.id=f.uid')
                ->where($where)
                ->group('uid')
                ->select();
            if($rs)
            {
                $rs[0]['sum']=$rs[0]['sum(f.score)']+$w['fen'];
            }
            else
            {
                $rs[0]=$w;
                $rs[0]['sum']=$w['fen'];
            }
            $res=Db::name('family')->field('uid,sum(score)')->group('uid')->select();
            $a=0;
           foreach($res as $v){
                $a=$a+1;
                if($v['uid']==$w['id']){
                    break;
                }
            }
            //$r=$rs[0]['sum(score)'];
            //$rs[0]['person_core']=$a;
            //$rs[0]['sum']=$rs[0]['sum(f.score)'];
            return json($rs);
    }
}
