<?php
namespace app\user\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Request;
use think\image;
class Addfile extends Controller
{
    public function uploadfile(){
        $url = $_SERVER['HTTP_HOST'];
        return $this->fetch('user/file',['title'=>$url]);
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
