<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;
use think\image;

class Office extends Controller
{
    public function index(){
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $header = $section->addHeader();
        //$header->addWatermark( 'http://tb2.bdstatic.com/tb/static-pcommon/img/search_logo_big_73c81f8.png',['margin-top:100',',margin-left:50']);
        $imageStyle = array('width'=>100, 'height'=>50, 'align'=>'right','bottom'=>0,'position'=>'fixed');
        $section->addImage('http://tb2.bdstatic.com/tb/static-pcommon/img/search_logo_big_73c81f8.png', $imageStyle);

        $section->addText(
            '测试文字'
        );
        $fontStyleName = 'oneUserDefinedStyle';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
        );
        $section->addWatermark();
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(true);
        $fontStyle->setName('Tahoma');
        $fontStyle->setSize(13);
        //$myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
        //$myTextElement->setFontStyle($fontStyle);
        $fileName = "word报表".date("YmdHis");
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition:attachment;filename=".$fileName.".doc");
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');
    }



}
