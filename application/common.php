<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function procmsg($topic, $msg){ //信息回调函数 打印信息\

    echo "Msg Recieved: " . date("r") . "\n";
    echo "Topic: {$topic}\n\n";
    echo "\t$msg\n\n";
    //die;

}
// curl  get 方式提交数据
function curl_get( $url ){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    $output = curl_exec($ch);
    $errno = curl_errno( $ch );
    if ($errno != 0){
        return 'timeout';
    }
    curl_close($ch);
    //
    return $output;
}
// curl post 方式提交数据并返回结果
function curl_post( $url, $data,&$httpCode = null ){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

    $output = curl_exec($ch);
    $errno = curl_errno( $ch );
    if ( null != $httpCode ) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
    curl_close($ch);

    if ($errno != 0){
        return 'timeout';
    }
    //
    return $output;
}
//$data 是json字符串  接收端需要json对象
function http_post( $url, $data ){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data)
        )
    );
    $output = curl_exec($ch);
    $errno = curl_errno( $ch );
    curl_close($ch);
    if ($errno != 0){
        return 'timeout';
    }
    //
    return $output;
}
function curl_img($url,$file){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
    $data = array('picFile' => new \CURLFile($file));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1 );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT,"TEST");
    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    return $result;
}