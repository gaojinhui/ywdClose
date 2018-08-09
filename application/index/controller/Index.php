<?php
namespace app\index\controller;
use app\index\model\User;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Index extends Controller
{
    /*首页banner图*/
    public function banner()
    {
       $rs=Db::name('banner')->field('pic')->select();
       return json($rs);
    }
    /*教育心得栏目*/
    public function heart_classtype()
    {
        $where['id']=145;
        $res=Db::name('menu')->field('title')->where($where)->find();
        return json($res);
    }
    /*教育心得*/
    public function heart()
    {
        $where['menu_id']=145;
        $rs=Db::name('newslist')->where($where)->limit(0,4)->select();
        return json($rs);
    }
    /*教育心得列表*/
    public function heart_list(Request $request)
    {
            $param=$request->param();
            $where['menu_id']=145;
            $limit=$param['limit'];
            $res=Db::name('newslist')->where($where)->limit(0,$limit)->select();
            foreach($res as $k=>$v)
            {
                $res[$k]['newsContent']=substr(strip_tags($v['newsContent']),0,99);
                // $res[$k]['newsTime']=ceil((time()-$v['newsTime'])/3600);
                $res[$k]['newsTime']=date('Y-m-d',$v['newsTime']);
            }
            return json($res);
    }
    /*教育心得详细信息*/
    public function heart_article(Request $request)
    {
            $param=$request->param();
            $where['id']=$param['id'];
            $rs=Db::name('newslist')->where($where)->find();
            $data['num']=$rs['num']+1;
            $res=Db::name('newslist')->where($where)->update($data);
            $rts=Db::name('newslist')->where($where)->find();
            $rts['newsTime']=date("Y-m-d H:i:s",$rts['newsTime']);
            return json($rts);
    }
    /*热门课程*/
    public function host_video(Request $request)
    {
        $param=$request->param();
        if(isset($param['limit']))
        {
            $limit=$param['limit'];
            $where['fen_id']=1;
            $where1['pid']=146;
            $res1=Db::name('video')->where($where1)->order('rand()')->find();
            $where2['pid']=166;
            $res2=Db::name('video')->where($where2)->order('rand()')->find();
            $where3['pid']=170;
            $res3=Db::name('video')->where($where3)->order('rand()')->find();
            $wt['fen_id']=1;
            $wt['parentid']=array('neq',172);
            $rts=Db::name('video')->where($wt)->order('rand()')->find();
            $res[0]=$res1;
            $res[1]=$res2;
            $res[2]=$res3;
            $res[3]=$rts;
        }
        if(isset($param['active']))
        {
            //$where['fen_id']=$param['active'];
            if(isset($param['typeid']))
            {
                if($param['typeid']!=0){
                    $where['pid']=$param['typeid'];
                }else{
                    $where['id']=array('neq',0);
                    $where['pid']=array('not in','173,174,175');
                }
            }
            else
            {
                $where['parentid']=170;
            }
            $res=Db::name('video')->where($where)->select();
        }
        foreach($res as $k=>$v)
        {
            $res[$k]['image']=str_replace("\\","/",$v['image']);
        }
        return json($res);
    }
    // 判断用户有没有反馈过 视频
    public function  user_email(Request $request)
    {
        $param=$request->param();
        $where['openid']=$param['openid'];
        $where['type']=2;
        $res=Db::name('email')->where($where)->find();
        if($res){
            $data['code']=1;
        }else{
            $data['code']=2;
        }
        return json($data);
    }
    // 评测标题列表
    public function coreTitle()
    {
        $where['parentid']=172;
        $res=Db::name('menu')->field('id,title,parentid')->where($where)->select();
        return json($res);
    }
}
