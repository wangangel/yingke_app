<?php
function output_data($datas, $extend_data) {
    $data = array();
    if(!empty($extend_data)) {
        $data = array_merge($data, $extend_data);
       
    }
    if($extend_data == "1"){
        $data['errresult'] = false;
    }
    else{
        $data['errresult'] = true;
    }

    $data['datas'] = $datas;

    if(!empty($_GET['callback'])) {
        echo $_GET['callback'].'('.json_encode($data).')';die;
    } else {
        echo json_encode($data);die;
    }
}

function output_error($message, $extend_data = array()) {
    $datas = array('error' => $message);
    output_data($datas, "1");
}

/*
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
    $config = C('THINK_EMAIL');
    Vendor('PHPMailer.PHPMailerAutoload'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail = new PHPMailer(); //PHPMailer对象
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    $mail->SMTPDebug = 0; // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl'; // 使用安全协议
    $mail->Host = $config['SMTP_HOST']; // SMTP 服务器
    $mail->Port = $config['SMTP_PORT']; // SMTP服务器的端口号
    $mail->Username = $config['SMTP_USER']; // SMTP服务器用户名
    $mail->Password = $config['SMTP_PASS']; // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->AltBody = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    
    if(is_array($attachment)){ // 添加附件

        foreach ($attachment as $file){

            is_file($file) && $mail->AddAttachment($file);

        }

    }

    return $mail->Send() ? true : $mail->ErrorInfo;

}
*/

function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}

/**
 * 用于转换时间格式
 */
function getTimeStr($UnixTime){
    $residuTime = time() - $UnixTime;
    if($residuTime > 86400*3){
        return date("Y-m-d H:i:s", $UnixTime);
    } else {
        $d = ceil($residuTime/86400)-1;
        if($d){
            $h = ceil(($residuTime - ($d*86400))/3600);
            return abs($d).'天'.abs($h).'小时之前';
        } else {
            $h = ceil($residuTime/3600)-1;
            if($h){
                $m = ceil(($residuTime - ($h*3600))/60);
                return abs($h).'小时'.abs($m).'分钟之前';
            } else {
                $m = ceil($residuTime/60);
                return abs($m).'分钟之前';
            }
        }
    }
}

function avatar_exists($member_id){
    $file_name_jpg = getcwd() . '/Public/avatar/avatar-' . $member_id . '.jpg';
    $file_name_png = getcwd() . '/Public/avatar/avatar-' . $member_id . '.png';
    $file_name_gif = getcwd() . '/Public/avatar/avatar-' . $member_id . '.gif';
    //die;
    
    //file_exists($file_name);
    if(file_exists($file_name_jpg)){
        return URL_PUB .'avatar/avatar-' . $member_id . '.jpg';
    }elseif (file_exists($file_name_png)){
        return URL_PUB .'avatar/avatar-' . $member_id . '.png';
    }elseif (file_exists($file_name_gif)){
        return URL_PUB .'avatar/avatar-' . $member_id . '.gif';
    }else{
        return URL_PUB .'avatar/avatar-default.jpg';
    }
}

function avatar_businessman_exists($member_id){
    $file_name_jpg = getcwd() . '/Public/businessman/avatar-' . $member_id . '.jpg';
    $file_name_png = getcwd() . '/Public/businessman/avatar-' . $member_id . '.png';
    $file_name_gif = getcwd() . '/Public/businessman/avatar-' . $member_id . '.gif';

    //file_exists($file_name);
    if(file_exists($file_name_jpg)){
        return URL_PUB .'businessman/avatar-' . $member_id . '.jpg';
    }elseif (file_exists($file_name_png)){
        return URL_PUB .'businessman/avatar-' . $member_id . '.png';
    }elseif (file_exists($file_name_gif)){
        return URL_PUB .'businessman/avatar-' . $member_id . '.gif';
    }else{
        return URL_PUB .'businessman/avatar-default.jpg';
    }
}

/**
 * 求两个已知经纬度之间的距离,单位为米
 * @param lng1,lng2 经度
 * @param lat1,lat2 纬度
 * @return float 距离，单位米
 **/
function getdistance($lng1, $lat1, $lng2, $lat2){
    //将角度转为狐度
    $radLat1 = deg2rad($lat1);  //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
    die;
    if($s < 100){
        return '100';
    }elseif ($s < 200){
        return '200';
    }elseif ($s < 500){
        return '500';
    }elseif ($s < 1000){
        return '1000';
    }elseif ($s < 2000){
        return '2000';
    }elseif ($s < 5000){
        return '5000';
    }elseif ($s < 10000){
        return '10000';
    }elseif ($s < 50000){
        return '50000';
    }elseif ($s < 100000){
        return '100000';
    }else{
        return '100001';
    }

}

/**
 *计算某个经纬度的周围某段距离的正方形的四个点
 *
 *@param lng float 经度
 *@param lat float 纬度
 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
 *@return array 正方形的四个点的经纬度坐标
 */
function returnSquarePoint($lng, $lat,$distance = 0.5){

    $dlng =  2 * asin(sin($distance / (2 * 6378.137)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);
     
    $dlat = $distance/6378.137;
    $dlat = rad2deg($dlat);
     
    return array(
        'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
        'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
        'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
        'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
    );
}

/**
 * 对多位数组进行排序
 * @param $multi_array 数组
 * @param $sort_key需要传入的键名
 * @param $sort排序类型
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return FALSE;
            }
        }
    } else {
        return FALSE;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
}


function sign($data){
    $residuTime = $data['createtime'];
    $time = time();
    if(date('Ymd',$time) === date('Ymd',$data['createtime']))
    {
        $data['sign'] = 1;
        return $data;
    }
    $date = date('Ymd',mktime(0,0,0,date('m',$data['createtime']),date('d',$data['createtime']),date('Y',$data['createtime']))+86400);
    if(date('Ymd',$time) === $date){   
        switch($data['sign_day'])
        {
            case 0:
                $data['sign_money'] = 10;
                break;
            case 1:
                $data['sign_money'] = 15;
                break;
            case 2:
                $data['sign_money'] = 20;
                break;
            case 3:
                $data['sign_money'] = 25;
                break;
            default:
                $data['sign_money'] = 30;
        }
        $data['sign'] = 0;
        //var_dump($data);die; 
        return $data;
         
    }
    $data['sign_money'] = 10;
    $data['sign_day'] = 0;
    $data['sign'] = 0;
    return $data;
}

//在线交易订单支付处理函数
//函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
//返回值：如果订单已经成功支付，返回true，否则返回false；


function checkorderstatus($ordid){
    $Ord=M('orderlist');
    $ordstatus=$Ord->where(array('ordid'=>$ordid))->getField('ordstatus');
    if($ordstatus==1){
        return true;
    }else{
        return false;
    }
}


//处理订单函数
//更新订单状态，写入订单支付后返回的数据
function orderhandle($parameter){
    $ordid=$parameter['out_trade_no'];
    $data['payment_trade_no']      =$parameter['trade_no'];
    $data['payment_trade_status']  =$parameter['trade_status'];
    $data['payment_notify_id']     =$parameter['notify_id'];
    $data['payment_notify_time']   =$parameter['notify_time'];
    $data['payment_buyer_email']   =$parameter['buyer_email'];
    $data['ordstatus']             =1;
    $Ord = M('orderlist');
    $Ord->where(array('ordid'=>$ordid))->save($data);
}


