<?php
namespace app\index\controller;
use app\index\model\News;
use app\index\model\Other;
use app\manage\model\Partner as M;
use app\index\model\User_buy as Ubuy;
use app\index\model\User_seller as Useller;
use app\manage\model\Indexdata as data;
use think\Controller;
use think\Session;
use think\Db;
use think\Request;
use think\image;
class Cfile extends Controller
{
    public function uploadfile(){
        $url = $_SERVER['HTTP_HOST'];
        return $this->fetch('index/file',['title'=>$url]);
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


}
