<?php

function element($key, $arr = array() , $default_value = "") {
    if (is_array($arr) && isset($arr[$key])) return $arr[$key];
    return $default_value;
}


// 不区分大小写的in_array实现
function in_array_case($value,$array){
    return in_array(strtolower($value),array_map('strtolower',$array));
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 *  unicode编码转为utf-8汉字
 *
 * \u65f6\u95f4\u6233\u5df2\u7ecf\u8fc7\u671f  ==>  时间戳已经过期
 *
 * @param  [type] $jsonstr [description]
 * @return [type]          [description]
 */
function utf8json($jsonstr){
    $jsonstr = ereg_replace("\\\\u([a-z0-9]{4})", "%%\\1", $jsonstr);
    return preg_replace_callback("/%%(\w{4})/u", function($matches){ return unicode_to_utf8($matches[1]); }, $jsonstr);
}

/**
 * unicode_to_utf8("65f6") => 时
 * unicode_to_utf8("95f4") => 间
 *
 * @param  [type] $unicode_str [unicode编码]
 * @return [type]              [单个汉字]
 */
function unicode_to_utf8($unicode_str) {
    $utf8_str = '';
    $code = intval(hexdec($unicode_str));
    $ord_1 = decbin(0xe0 | ($code >> 12));
    $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
    $ord_3 = decbin(0x80 | ($code & 0x3f));
    $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
    return $utf8_str;
}

/**
 * 数组转化为符合SQL的字符串
 * 示范：
 *  $data = array ('id' => 1234,'name'=>'lishun','msg'=>"I'm LiShun");
 *  data2sql($data)        ===> `id`='1234' , `name`='lishun' , `msg`='I\'m LiShun'
 *  data2sql($data, "and") ===> `id`='1234' and `name`='lishun' and `msg`='I\'m LiShun'
 *
 * @param  array $data    要操作的数组
 * @param  string $joinstr 连接字符串
 * @return string          [description]
 */
function data2sql($data, $joinstr=","){
    $result = array();
    foreach($data as $key=>$value){
        $key = addslashes($key);
        $value = addslashes($value);
        $result[]= "`$key`='$value'";
    }
    return implode(" $joinstr ", $result);
}



/**
 * 发送文本邮件
 *
 * $recipient
 *  为string时, 收件人只能一个直接填写邮件地址
 *  为array时，收件人可以一个或多个
 *  array( array("to1@eyu.com", "收件人1"), array("to2@eyu.com", "收件人2"), array("to3@eyu.com", "收件人3"))
 *
 *  $cc参数用法同 $recipient
 *
 * @param  string $subject   邮件标题
 * @param  string $body      邮件内容
 * @param  array|string  $recipient 收件人
 * @param  array|string  $cc        抄送
 * @return bool            是否发送成功
 */
function sendMail($subject, $body, $recipient, $cc = null) {

    $mail = newClass("application/common/phpmailer/phpmailer");
    $mail->CharSet = C('mail.mail_charset');
    $mail->SMTPDebug = C('mail.mail_smtpdebug');
    $mail->Host = C('mail.mail_host');
    if (C('mail.mail_smtpauth')) {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
    }
    $mail->Username = C('mail.mail_username');
    $mail->Password = C('mail.mail_password');
    $mail->Port = C('mail.mail_port');

    $mail->From = C('mail.mail_from');
    $mail->FromName = C('mail.mail_fromname');

    if (is_string($recipient)) $recipient = array(array($recipient, preg_replace("/@.+$/", "", $recipient)));
    foreach($recipient as $to) {
        $mail->addAddress($to[0], "=?UTF-8?B?". base64_encode($to[1]) ."?=");
    }

    if (is_string($cc)) $cc = array(array($cc, preg_replace("/@.+$/", "", $cc)));
    if (is_array($cc)){
        foreach($cc as $to) {
            $mail->addCC($to[0], "=?UTF-8?B?". base64_encode($to[1]) ."?=");
        }
    }

    $mail->Subject = "=?UTF-8?B?".base64_encode($subject) ."?=";
    $mail->Body = $body;

    return $mail->send();
}


/**
 * 检测是否是合法的邮箱地址
 *
 * @link http://www.whatwg.org/specs/web-apps/current-work/#e-mail-state-(type=email)
 * @param  string  $address 邮箱地址
 * @return boolean          [description]
 */
function isEmail($address){
    return (boolean)preg_match(
        '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
        '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
        $address
    );
}
