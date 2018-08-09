<?php

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string $key
 */
function user_md5($str, $key = '', $key2 = '') {
    return '' === $str ? '' : md5(sha1($key2 . $str) . $key);
}

function user_sha1($str, $key = '', $key2 = '', $key3 = '') {
    return '' === $str ? '' : md5(sha1($key . $str . $key3) . $key2);
}

/* 字段加密 */

function baseauthcode($str, $type = 1) {
    if ($type == 1) {
        return base64_encode(authcode($str, 'ENCODE', C('DATA_AUTH_KEY'), 0));
    } else {
        return base64_encode(authcode($str, 'DECODE', C('DATA_AUTH_KEY'), 0));
    }
}

/* 字段加密解密 */

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;
    // 密匙
    $key = md5($key ? $key : $_SERVER['HTTP_HOST']);
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
//解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/* 微信字段校验 */

function jsapi_encode($data) {
    ksort($data);
    $str = '';
    foreach ($data as $k => $v) {
        $str .= '&' . $k . '=' . $v;
    }
    $str = ltrim($str, '&');
    return sha1($str);
}

function jsapi_md5($data) {
    ksort($data);
    $str = '';
    foreach ($data as $k => $v) {
        $str .= '&' . $k . '=' . $v;
    }
    $str = ltrim($str, '&');
    return md5($str);
}

/* 系统提示操作 */

function ErrMsg($str = '操作失败！') {
    header("Content-Type: text/html; charset=UTF-8");
    echo "<script>";
    echo "alert('" . $str . "');history.back();";
    echo "</script>";
    exit();
}

function SucMsg($str = '操作成功！', $url) {
    header("Content-Type: text/html; charset=UTF-8");
    echo "<script>";
    echo "alert('" . $str . "');location.href='" . $url . "';";
    echo "</script>";
    exit();
}

function closeMsg($str = '操作失败！', $code = 0) {
    header("Content-Type: text/html; charset=UTF-8");
    echo "<script>";
    echo "var index = parent.layer.getFrameIndex(window.name) || parent.layer.index;";
    echo "parent.layer.msg('" . $str . "');";
    echo "parent.layer.close(index);";
    echo "console.log(index);";
    echo "</script>";
    exit();
}

function jsonMsg($msg = '操作失败', $error_code = 1, $data = NULL,$count=NULL) {
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    $arr = array("error_code" => $error_code, "msg" => $msg, "data" => $data,"count"=>$count);
    die(json_encode($arr));
}

function jsonList($msg = '操作失败', $error_code = 1, $list = array(), $count = 0, $data = NULL) {
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    $arr = array("error_code" => $error_code, "msg" => $msg, "data" => $list, 'count' => $count, 'content' => $data);
    die(json_encode($arr));
}

function jsonMsg2($msg = '操作失败', $error_code = 1, $data = NULL) {
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    $arr = array("code" => $error_code, "msg" => $msg, "data" => $data);
    die(json_encode($arr));
}

/* 生成随机码 */

function getKey($length = 10, $type = 0) {
    $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), array('!', '@', '$', '%', '^', '&', '*'));
    $type == 1 AND $chars = range(0, 9);
    shuffle($chars);
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $type == 1 AND shuffle($chars);
        $password .= $chars[$i];
    }
    return $password;
}

/* 校验手机号码 */

function checkphone($mobilephone) {
    if (preg_match("/^1[3456789]{1}\d{9}$/", $mobilephone)) {
        //验证通过
        return true;
    } else {
        //手机号码格式不对
        return false;
    }
}

//检测金钱
function CheckMoney($C_Money) {
    if ($C_Money == 0)
        return true;
    if (!ereg("^[1-9]{1}[0-9]{0,4}$|[0-9]{1,5}[.][0-9]{1,2}$", $C_Money))
        return false;
    return true;
}

function showMoney($money) {
    $money = ltrim($money, '0');
    $money = preg_replace("/([0-9]+)(\.00)$/i", '${1}', $money);
    return $money;
}

function checkdatetime($dateTime) {
    $unixTime = strtotime($dateTime);
    if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
        return false;
    }
    return TRUE;
}

function getTimeName($dateTime) {
    if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/s", $dateTime)) {
        return substr($dateTime, 11, 5);
    } else {
        return $dateTime;
    }
}

function checkemail($email) {
    return preg_match('/^([0-9A-Za-z\-_\.]+)@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,}([\.][a-z]{2,})*$/i', $email);
}

function checkIdCard($idcard) {
    $City = array(11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古", 21 => "辽宁", 22 => "吉林", 23 => "黑龙江", 31 => "上海", 32 => "江苏", 33 => "浙江", 34 => "安徽", 35 => "福建", 36 => "江西", 37 => "山东", 41 => "河南", 42 => "湖北", 43 => "湖南", 44 => "广东", 45 => "广西", 46 => "海南", 50 => "重庆", 51 => "四川", 52 => "贵州", 53 => "云南", 54 => "西藏", 61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => "宁夏", 65 => "新疆", 71 => "台湾", 81 => "香港", 82 => "澳门", 91 => "国外");
    $iSum = 0;
    $idCardLength = strlen($idcard);
    //长度验证
    if (!preg_match('/^\d{17}(\d|x)$/i', $idcard) and ! preg_match('/^\d{15}$/i', $idcard)) {
        return false;
    }
    //地区验证
    if (!array_key_exists(intval(substr($idcard, 0, 2)), $City)) {
        return false;
    }
    // 15位身份证验证生日，转换为18位
    if ($idCardLength == 15) {
        $sBirthday = '19' . substr($idcard, 6, 2) . '-' . substr($idcard, 8, 2) . '-' . substr($idcard, 10, 2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        if ($sBirthday != $dd) {
            return false;
        }
        $idcard = substr($idcard, 0, 6) . "19" . substr($idcard, 6, 9); //15to18
        $Bit18 = getVerifyBit($idcard); //算出第18位校验码
        $idcard = $idcard . $Bit18;
    }
    // 判断是否大于2078年，小于1900年
    $year = substr($idcard, 6, 4);
    if ($year < 1900 || $year > 2078) {
        return false;
    }
    //18位身份证处理
    $sBirthday = substr($idcard, 6, 4) . '-' . substr($idcard, 10, 2) . '-' . substr($idcard, 12, 2);
    $d = new DateTime($sBirthday);
    $dd = $d->format('Y-m-d');
    if ($sBirthday != $dd) {
        return false;
    }
    //身份证编码规范验证
    $idcard_base = substr($idcard, 0, 17);
    if (strtoupper(substr($idcard, 17, 1)) != getVerifyBit($idcard_base)) {
        return false;
    }
    return true;
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function getVerifyBit($idcard_base) {
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

//手机号码部分显示
function subphone($phone) {
    return str_replace(substr($phone, 3, 5), '*****', $phone);
}

//身份证号码部分显示
function subidcard($idcard) {
    return str_replace(substr($idcard, 6, 8), '********', $idcard);
}

//计算指定时间到当前时间的时间差
function diff_time($time1, $time2 = '', $type = 's') {
    $endtime = strtotime($time1);
    if (empty($time2)) {
        $starttime = strtotime(date('Y-m-d H:i:s'));
    } else {
        $starttime = strtotime($time2);
    }
    $diff = $endtime - $starttime;
    switch ($type) {
        case 'm':
            $diff = round($diff / 60);
            break;
        case 'h':
            $diff = round($diff / 3600);
            break;
        case 'd':
            $diff = round($diff / 86400);
            break;
        default:
            $diff = $diff;
    }
    return $diff;
}

function ntobr($str) {
    $order = array("\r\n", "\n", "\r");
    $replace = '<br/>';
    return str_replace($order, $replace, $str);
}

//多维数组转换为一维数组
function array_multi2single($array) {
    static $result_array = array();
    foreach ($array as $value) {
        if (is_array($value)) {
            array_multi2single($value);
        } else
            $result_array[] = $value;
    }
    return $result_array;
}

//urlcode转义
function url_encode($str) {
    if (is_array($str)) {
        foreach ($str as $key => $value) {
            unset($str[$key]);
            $str[urlencode($key)] = url_encode($value);
        }
    } else {
        $str = urlencode($str);
    }
    return $str;
}

//二维数组变为一维数组
function array_toOne($arr, $data = array()) {
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $data = array_toOne($v, $data);
            } else {
                $data[$k] = $arr;
            }
        }
    } else {
        $data[] = $arr;
    }
    return $data;
}

function arr_foreach($arr, $tmp = array()) {
    if (!is_array($arr)) {
        return false;
    }
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $tmp = arr_foreach($val, $tmp);
        } else {
            $tmp[$key] = $val;
        }
    }
    return $tmp;
}

//二维数组排序
function array_sort($arr, $keys, $type = 'desc') {
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[] = $arr[$k];
    }
    return $new_array;
}

function is_mobile() {
    $user_agent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
    //Mobile
    if ((preg_match("/(iphone|ipod|android)/i", strtolower($user_agent))) AND strstr(strtolower($user_agent), 'webkit')) {
        return true;
    } else if (trim($user_agent) == '' OR preg_match("/(nokia|sony|ericsson|mot|htc|samsung|sgh|lg|philips|lenovo|ucweb|opera mobi|windows mobile|blackberry)/i", strtolower($user_agent))) {
        return true;
    } else {//PC
        return false;
    }
}

function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

function html_postdata($url,$data=""){//服务器请求微信接口
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        echo curl_error($ch);
    }

    curl_close($ch);

    return $tmpInfo;
}

function html_post($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    $header = array("content-type: application/json;charset=UTF-8");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //设置头信息的地方
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

//xml转换为数组
function xmlToArray($xml) {
    libxml_disable_entity_loader(true);
    $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $data = json_decode(json_encode($obj), true);
    return $data;
}

//计算时间差
function getTimeDiff($time1, $time2) {
    $arr1 = explode(':', $time1);
    $arr2 = explode(':', $time2);
    $hour1 = $arr1[0];
    $mins1 = $arr1[1];
    $hour2 = $arr2[0];
    $mins2 = $arr2[1];
    if ($time1 > $time2) {
        $difftime = 60 * $hour2 + $mins2 + 24 * 60 - 60 * $hour1 - $mins1;
    } else {
        $difftime = 60 * $hour2 + $mins2 - 60 * $hour1 - $mins1;
    }
    return $difftime;
}

//数字转化为时间段
function numToTime($num, $separator = '-') {
    $start = sprintf("%02d", $num);
    $end = sprintf("%02d", $num + 1);
    return $start . ":00" . $separator . $end . ":00";
}

/**
 * 计算上一个月的今天，如果上个月没有今天，则返回上一个月的最后一天
 * @param type $time
 * @return type
 */
function last_month_today($time) {
    if (empty($time))
        $time = time();
    $last_month_time = mktime(date("G", $time), date("i", $time), date("s", $time), date("n", $time), 0, date("Y", $time));
    $last_month_t = date("t", $last_month_time);
    if ($last_month_t < date("j", $time)) {
        return date("Y-m-t H:i:s", $last_month_time);
    }
    return date(date("Y-m", $last_month_time) . "-d", $time);
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后)
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays($day1, $day2) {
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);
    /*
      if ($second1 < $second2) {
      $tmp = $second2;
      $second2 = $second1;
      $second1 = $tmp;
      } */
    return abs($second1 - $second2) / 86400;
}

function checkWxSign($arr, $signstr) {
    ksort($arr);
    $string1 = '';
    foreach ($arr as $k => $v) {
        if ($v != '' && $k != 'sign') {
            $string1 .= "{$k}={$v}&";
        }
    }
    $sign = strtoupper(md5($string1 . "key=" . C('WX_KEY')));
    if ($signstr != $sign)
        return false;
    return true;
}

/* 从身份证号码中获取生日性别等信息 */

function getIDCardInfo($IDCard) {
    $result = array('error' => 1, 'birthday' => '', 'gender' => '');
    if (!checkIdCard($IDCard)) {
        return $result;
    }
    $tyear = intval(substr($IDCard, 6, 4));
    $tmonth = intval(substr($IDCard, 10, 2));
    $tday = intval(substr($IDCard, 12, 2));
    $result['error'] = 0;
    $result['birthday'] = $tyear . '-' . $tmonth . '-' . $tday;
    $sexint = intval(substr($IDCard, 16, 1));
    $result['gender'] = $sexint % 2;
    return $result;
}

/* 根据出生年月计算年龄 */

function getAge($birthday, $startdate) {
    if (!$startdate) {
        $startdate = date('Y-m-d');
    }
    if (strlen($startdate) == 4) {//只计算年份
        $tyear = date('Y', strtotime($birthday));
        return intval($startdate - $tyear);
    } else {
        list($y1, $m1, $d1) = explode("-", $birthday);
        list($y2, $m2, $d2) = explode("-", $startdate);
        $age = $y2 - $y1;
        if (intval($m2 . $d2) > intval($m1 . $d1)) {
            $age--;
        }
        return $age;
    }
}

//替换
function replacequot($str) {
    return str_replace(",", '"', $str);
}

//空值默认
function mydefault($value, $default) {
    if (empty($value)) {
        return $default;
    } else {
        return $value;
    }
}

function Array_Month($month) {
    /* 方法一 */
    //$month_str='一,二,三,四,五,六,七,八,九,十,十一,十二';
    //$arr=explode(',',$month_str);
    /* 方法二 */
    $arr = array('一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二');
    return $arr[$month - 1];
}

function time2string($second) {
    $day = floor($second / (3600 * 24));
    $second = $second % (3600 * 24);
    $hour = floor($second / 3600);
    $second = $second % 3600;
    $minute = floor($second / 60);
    $second = $second % 60;
    $day = $day ? $day . '天' : '';
    $hour = $hour ? $hour . '时' : ($day && ($hour || $minute || $second) ? '0时' : '');
    $minute = $minute ? $minute . '分' : ($hour && $second ? '0分' : '');
    $second = $second ? $second . '秒' : '';
    return $day . $hour . $minute . $second;
}

function create_guid($namespace = '') {
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash, 0, 8) .
        '-' .
        substr($hash, 8, 24) .
        '-' . substr(strtoupper(md5($namespace)), 0, 30);
    return $guid;
}

function y2f($rmbs) {
    $rmb = floor($rmbs * 10 * 10);
    return $rmb;
}

// $round: float round ceil floor
function f2y($rmbs, $round = 'float') {
    $rmb = floor($rmbs * 100) / 10000;
    if ($round == 'float') {
        $rmb = number_format($rmb, 2, '.', '');
    } elseif ($round == 'round') {
        $rmb = round($rmb);
    } elseif ($round == 'ceil') {
        $rmb = ceil($rmb);
    } elseif ($round == 'floor') {
        $rmb = floor($rmb);
    }
    return floatval($rmb);
}

function getpercent($a, $b) {
    if ($b <= 0)
        return 0;
    $per = round($a / $b, 2);
    return $per * 100;
}

/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1  起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2  终点纬度
 * @param  Int     $unit       单位 1:米 2:公里
 * @param  Int     $decimal    精度 保留小数位数
 * @return Decimal
 */
function getdistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2) {

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;
    if ($unit == 2) {
        $distance = $distance / 1000;
    }
    return round($distance, $decimal);
}

if (!function_exists('array_column')) {

    function array_column($input, $columnKey, $indexKey = NULL) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
        $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
        $result = array();

        foreach ((array) $input AS $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
            }
            if (!$indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key)) ? current($key) : NULL;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }

            $result[$key] = $tmp;
        }
        return $result;
    }

}

function bd_decrypt($arr) {
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $arr['longitude'] - 0.0065;
    $y = $arr['latitude'] - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $data= array();
    $data[] = $z * cos($theta);
    $data[] = $z * sin($theta);
    return $data;
}
function bd_decrypt_list($arr) {
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $arr['longitude'] - 0.0065;
    $y = $arr['latitude'] - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $arr['longitude'] = $z * cos($theta);
    $arr['latitude'] = $z * sin($theta);
    return $arr;
}
function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}


function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}
 function tree($data,$pid=0,$level=1){
    $tree = [];
    foreach($data as $k => $v)
    {
        $v['level']=$level;
        if($v['parentid'] == $pid)
        {        //父亲找到儿子

            $v['children'] = tree($data, $v['id']);
            $tree[] = $v;
        }
    }
    return $tree;
}

function doc_to_pdf($srcfilename,$destfilename){
    try {
        $word = new \COM("word.application") or die("Can't start Word!");
        $word->Visible=0;
        $word->Documents->Open($srcfilename, false, false, false, "1", "1", true);

        $word->ActiveDocument->final = false;
        $word->ActiveDocument->Saved = true;
        $word->ActiveDocument->ExportAsFixedFormat(
            $destfilename,
            17,                         // wdExportFormatPDF
            false,                      // open file after export
            0,                          // wdExportOptimizeForPrint
            3,                          // wdExportFromTo
            1,                          // begin page
            5000,                       // end page
            7,                          // wdExportDocumentWithMarkup
            true,                       // IncludeDocProps
            true,                       // KeepIRM
            1                           // WdExportCreateBookmarks
        );
        $word->ActiveDocument->Close();
        $word->Quit();
        $rs = str_replace('D:/phpStudy/PHPTutorial/WWW/trade',$_SERVER['HTTP_HOST'],$destfilename);
        jsonMsg("成功",0,$rs);
    } catch (\Exception $e) {
       jsonMsg("转换失败");
    }

}
