<?php
namespace app\user\model;
use think\Model;
use Phinx\Db\Table;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use think\Image;
class Agreement extends Model
{
    public function save_agreement(){
        $param = input();
        $where['contract_id']="HNYC".date('Ymd');
        if(isset($param['arr'])){
            if(is_array($param['arr'])){
                $str='';
                foreach ($param['arr'] as $item){
                    $str =$str.$item."-";
                }
                $where['year_statement'] = $str;
                $where['type']=2;
            }
        }else{
            $where['type']=1;
        }
        if(isset($param['seller'])){$where['seller']=$param['seller'];}else{jsonMsg("出卖人不能为空","1");}
        if(isset($param['contract_number'])){$where['contract_number']=$param['contract_number'];}else{jsonMsg("合同编号不能为空，请仔细检查","1");}
        if(isset($param['buyer'])){$where['buyer']=$param['buyer'];}else{jsonMsg("买受人不能为空，请仔细检查","1");}
        if(isset($param['transport'])){$where['transport']=$param['transport'];}else{jsonMsg("运输方式不能为空，请仔细检查","1");}
        if(isset($param['loading_place'])){$where['loading_place']=$param['loading_place'];}else{jsonMsg("装车地不能为空，请仔细检查","1");}
        if(isset($param['unloaded'])){$where['unloaded']=$param['unloaded'];}else{jsonMsg("卸车地不能为空，请仔细检查","1");}
        if(isset($param['delivery_type'])){$where['dtype']=$param['delivery_type'];}else{jsonMsg("交易方式不能为空，请仔细检查","1");}
        if(isset($param['sign_date'])){$where['sign_date']=$param['sign_date'];}else{jsonMsg("签订日期不能为空，请仔细检查","1");}
        if(isset($param['price_type'])){$where['price_type']=$param['price_type'];}else{jsonMsg("价格类型不能为空，请仔细检查","1");}
        if(isset($param['consignor'])){$where['consignor']=$param['consignor'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['goods_name'])){$where['goods_name']=$param['goods_name'];}else{jsonMsg("商品名称不能为空，请仔细检查","1");}
        if(isset($param['consignee'])){$where['consignee']=$param['consignee'];}else{jsonMsg("收货人不能为空，请仔细检查","1");}
        if(isset($param['price'])){$where['price']=$param['price'];}else{jsonMsg("价格不能为空，请仔细检查","1");}
        if(isset($param['quality_index'])){$where['quality_index']=$param['quality_index'];}else{jsonMsg("质量指标不能为空，请仔细检查","1");}
        if(isset($param['quality_info'])){$where['quality_info']=$param['quality_info'];}else{jsonMsg("质量描述，请仔细检查","1");}
        if(isset($param['quantity'])){$where['quantity']=$param['quantity'];}else{jsonMsg("数量不能为空，请仔细检查","1");}
        if(isset($param['template'])){$where['template']=$param['template'];}
        if(isset($param['content'])){$where['template_info']=$param['content'];}else{jsonMsg("合同内容不能为空，请仔细检查","1");}
        if(isset($param['seller_name'])){$where['seller_name']=$param['seller_name'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_address'])){$where['seller_address']=$param['seller_address'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_egal'])){$where['seller_egal']=$param['seller_egal'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_agent'])){$where['seller_agent']=$param['seller_agent'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_phone'])){$where['seller_phone']=$param['seller_phone'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_fax'])){$where['seller_fax']=$param['seller_fax'];}
        if(isset($param['seller_postcode'])){$where['seller_postcode']=$param['seller_postcode'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_bank_deposit'])){$where['seller_bank_deposit']=$param['seller_bank_deposit'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_bank_number'])){$where['seller_bank_number']=$param['seller_bank_number'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_compay'])){$where['seller_compay']=$param['seller_compay'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_address1'])){$where['seller_address1']=$param['seller_address1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_egal1'])){$where['seller_egal1']=$param['seller_egal1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_agent1'])){$where['seller_agent1']=$param['seller_agent1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_phone1'])){$where['seller_phone1']=$param['seller_phone1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_fax1'])){$where['seller_fax1']=$param['seller_fax1'];}
        if(isset($param['seller_postcode1'])){$where['seller_postcode1']=$param['seller_postcode1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_bank_deposit1'])){$where['seller_bank_deposit1']=$param['seller_bank_deposit1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_bank_number1'])){$where['seller_bank_number1']=$param['seller_bank_number1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['seller_tax_number1'])){$where['seller_tax_number1']=$param['seller_tax_number1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_name'])){$where['buyer_name']=$param['buyer_name'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_address'])){$where['buyer_address']=$param['buyer_address'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_egal'])){$where['buyer_egal']=$param['buyer_egal'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_agent'])){$where['buyer_agent']=$param['buyer_agent'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_phone'])){$where['buyer_phone']=$param['buyer_phone'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_fax'])){$where['buyer_fax']=$param['buyer_fax'];}
        if(isset($param['buyer_postcode'])){$where['buyer_postcode']=$param['buyer_postcode'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_bank_deposit'])){$where['buyer_bank_deposit']=$param['buyer_bank_deposit'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_bank_number'])){$where['buyer_bank_number']=$param['buyer_bank_number'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_tax_number'])){$where['buyer_tax_number']=$param['buyer_tax_number'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_compay'])){$where['buyer_compay']=$param['buyer_compay'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_address1'])){$where['buyer_address1']=$param['buyer_address1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_egal1'])){$where['buyer_egal1']=$param['buyer_egal1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_agent1'])){$where['buyer_agent1']=$param['buyer_agent1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_phone1'])){$where['buyer_phone1']=$param['buyer_phone1'];}else{jsonMsg("有尚未填写的内容，请仔细检查","1");}
        if(isset($param['buyer_fax1'])){$where['buyer_fax1']=$param['buyer_fax1'];}
        if(isset($param['buyer_postcode1'])){$where['buyer_postcode1']=$param['buyer_postcode1'];}else{jsonMsg("邮编不能为空，请仔细检查","1");}
        if(isset($param['buyer_bank_deposit1'])){$where['buyer_bank_deposit1']=$param['buyer_bank_deposit1'];}else{jsonMsg("开户银行不能为空，请仔细检查","1");}
        if(isset($param['buyer_tax_number1'])){$where['buyer_tax_number1']=$param['buyer_tax_number1'];}else{jsonMsg("纳税登记号不能为空，请仔细检查","1");}
        if(isset($param['buyer_bank_number1'])){$where['buyer_bank_number1']=$param['buyer_bank_number1'];}else{jsonMsg("账号不能为空，请仔细检查","1");}
        if(isset($param['status'])){$where['status']=$param['status'];}else{jsonMsg("未获取到状态","1");}
        if(isset(Session::get("userinfo")['uid'])){$where['uid']=Session::get("userinfo")['uid'];}else{jsonMsg("登陆过期，请重新登陆","1");}
        $userinfo = Db::name("agreement")->insert($where);
        return $userinfo;
    }
    public function save_tpl(){
        $param = input();
        if(isset(Session::get("userinfo")['uid'])){$where['uid']=Session::get("userinfo")['uid'];}else{jsonMsg("登陆过期，请重新登陆","1");}
        if(isset($param['title'])){$where['title']=$param['title'];}else{jsonMsg("请输入模板标题","1");}
        if(isset($param['content'])){$where['tpl_text']=$param['content'];}else{jsonMsg("请输入模板内容后提交","1");}
        $rs = Db::name("user_tpl")->insert($where);
        return $rs;
    }
    public function get_tpl(){
        $param = input();
        if(isset(Session::get("userinfo")['uid'])){$where['uid']=Session::get("userinfo")['uid'];}else{jsonMsg("登陆过期，请重新登陆","1");}
        //var_dump(isset($param['id']));
        if(!empty($param['id'])){$where['id']=$param['id'];}else{jsonMsg("请选择模板","1");}
        $rs = Db::name("user_tpl")->where($where)->field("uid",true)->find();
        return $rs;
    }
    public function get_contract(){
        if(empty(Session::get("userinfo")['uid'])){jsonMsg("登录过期需要重新登录",2);}
        $where['uid']=Session::get("userinfo")['uid'];
        $rs =Db::name("agreement")->where($where)->select();
        return $rs;
    }

}