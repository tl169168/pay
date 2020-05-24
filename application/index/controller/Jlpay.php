<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-07
 * Time: 10:28
 */

namespace app\index\controller;
session_start();

use think\Cache;
use think\Controller;
use think\Db;
use app\index\controller\Jsapi;
use app\index\controller\Wxcof;
use think\Cookie;
use think\Request;
use think\Url;


class Jlpay extends Controller
{
    //测试机构号
    const ORG_CODE = '50263701';
    //正式机构号
    const JL_CODE = '50354097';
    //微信公众号appid
    const APPID = 'wxc0f5c76e80a08dc3';




//**************************************************************************************************************************************************************
    public function index(){
        echo "<h5 align='center'>接口主页</h5>";
    }

    public function key(){
        $data['th']=2;
        $data['one']=1;
        $data['cm']='林嫂';
        $thes=json_encode($data);
        $private_key =file_get_contents(ROOT_PATH.'private_key.txt');

        $public_key = file_get_contents(ROOT_PATH.'public_key.txt');
        $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $pu_key =  openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
        echo $pi_key;echo "<br />";
        echo $pu_key;echo "<br />";
        //$data = "uid=2312&unm=2324&ccr=23&user=234234林少&sing=xfdfsdfsdfewewrfsdfsdfsdfsdfsd";//原始数据

       /* openssl_private_encrypt($data,$encrypted,$pi_key);//私钥加密
        $encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        echo '加密后的内容通常含有特殊字符'.$encrypted,"<br />";

        openssl_public_decrypt(base64_decode($encrypted),$decrypted,$pu_key);//私钥加密的内容通过公钥可用解密出来

        echo "私钥加密的内容通过公钥可用解密出来".$decrypted,"<br />";
            $thiso=json_encode($decrypted);*/

        openssl_public_encrypt($thes,$encrypted,$pu_key);//公钥加密
        $sn = base64_encode($encrypted);
        $data['dn']=$sn;
//        echo '公钥加密'.$sn,"<br />";
//       $cc=openssl_private_decrypt(base64_decode($sn),$decrypted,$pi_key);//私钥解密
//        echo '公钥加密'.$decrypted,"<br />";
        /*$url='http://payapi.tech100.com.cn/index.php/index/Jlpay/pay';
        $d = http_post($url,json_encode($data));
        $h =json_decode($d,true);
        dump($h);*/
            return $this->redirect("http://payapi.tech100.com.cn/index.php/index/Jlpay/pay/?sn=$sn",302);


    }
//********************支付api********************************************************************************************************************************* */
   public function code(){
       if (isset($_GET['code'])){
           $code= $_GET['code'];
           $appid='wxc0f5c76e80a08dc3';
           $secret='4d4d332251d274173b920edceef7a07f';
           $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
           $curl=curl_get($url);
           $res=json_decode($curl,true);
//           dump($res);
           $openid=$res['openid'];
           $this->redirect('http://payapi.tech100.com.cn/index.php/index/jlpay/pay?'.$_SERVER['QUERY_STRING'].'&openid='.$openid);
       }else{
           echo "NO CODE";
       }
   }

    public function wxDl()
    {
        $type = $this->request->param('type',0);
        $eid = $this->request->param('eid');
        $noUrl = $this->request->param('noUrl');

        // $eidArrr= Db::table('auto_equipment')->field("eid")->where('dev',$dev)->find();
        // $eid = $eidArrr['eid'];

        //微信授权
        if (empty($openid)){
            $too = new Jsapi();
            $openid = $too->getOpenid();

        }

            $this->redirect('http://payapi.tech100.com.cn/index.php/index/jlpay/pay?'.$_SERVER['QUERY_STRING'].'&openid='.$openid);

    }
    public function pay(){
        $private_key =file_get_contents(ROOT_PATH.'private_key.txt');
        $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $sn=$this->request->param('sn');
        $cc=openssl_private_decrypt(base64_decode($sn),$decrypted,$pi_key);//私钥解密
        $eid = $this->request->param('eid');     //商户号
        if ($eid==null){
            $res['errno'] = 1001;
            $res['Explain'] = "商户号错误";
            return json_encode($res);
        }
        $amount = floatval($this->request->param('amount'));
        if ($amount==null){
            $res['errno'] = 1002;
            $res['Explain'] = "金额错误";
            return json_encode($res);
        }
        $orderId = $this->request->param('orderId');    //订单号
        $url1 = $this->request->param('url');    //回调地址
        $openid = $this->request->param('openid');    //微信openid
        $goodsInfo = $this->request->param('goodsInfo');    //商品信息
        $scene = $this->request->param('secretk');
        if (strlen($eid) != 15 || $amount <= 0){
            return "商户id格式错误或金额错误";
        }
        $thistime =date("Y-m-d H:i:s"); // 当前时间
        $wxpay = 'MicroMessenger';
         $aplipay = 'Alipay';
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(FALSE != strpos($user_agent,$wxpay)) {
            if (empty($openid)){
                $too = new Jsapi();
                $openid = $too->getOpenid();
            }
//            echo $openid;
            $d = Db::table('auto_equipment')->where('eid',$eid)->find(); //查询设备  eid
                $mon = date("Ym");
                $table = 'auto_bills_'.$mon;
                $this->create_table($table);
                $arr['eid'] = $eid;
                $arr['amount'] = $amount;
                $arr['goodsInfo'] = $goodsInfo;
                $arr['scene'] = $scene;
                $arr['status'] = 1;
                $arr['mode']=2;   //微信1   支付宝 2
                $arr['data']=$thistime;
                $arr['gid']=$url1;
                $id = Db::table($table)->insertGetId($arr);
            $mon = date("Ym");
            $orderId = $mon . '_' . uniqid();
            //请求嘉联
            $data['org_code'] = SELF::JL_CODE;
            $data['merch_no'] = $eid;
            $data['openid'] = $openid;
            $data['trade_type'] = 'pay.weixin.jsapi';
//            $data['appid'] ='wxc0f5c76e80a08dc3';
            $data['out_trade_no'] = $orderId;
            $data['order_date'] = date('YmdHis');
            $data['trade_amount'] = $amount;
            $data['goods_name'] = '$goodsInfo';
            $data['notify_url'] = 'http://payapi.tech100.com.cn/index.php/index/Jlpay/notify';
//            $data['notify_url'] = $url1;
            $data['client_ip'] = $_SERVER['REMOTE_ADDR'];
            $data['nonce_str'] = uniqid('',true);
            $s = $this->queryString($data);
            $sign = $this->sign($s);
            $data['sign'] = $sign;
//            dump($data);
               $url = 'https://openapi.jlpay.com/qrcode/trans/unified/order';
            $d = http_post($url,json_encode($data));
            $h =json_decode($d,true);//exit;
//            dump($h);
            if ($h['ret_code'] != '00'){
                return $d;
            }
//          var_dump($h);
//            return $h;
            $ree['merch_no']=$h["merch_no"];
            $ree['order_no']= $h["order_no"];
            $ree['out_trade_no']= $h["out_trade_no"];

            $ree['ret_code']=  '成功';
            $ree['ret_msg']=  $h["ret_msg"];
            $ree['trade_amount']= $h["trade_amount"];
            $ree['pay_info']= $h["pay_info"];
//            dump($ree);
            return json_encode($ree);
//            dump($h);
//           return $this->fetch('pay',['fee'=>$amount,'orderId'=>$orderId,'name'=>$h['pay_info'],'eid'=>$eid]);
        }else{
           $mon = date("Ym");
                $table = 'auto_bills_'.$mon;
                $this->create_table($table);
                $arr['eid'] = $eid;
                $arr['amount'] = $amount;
                $arr['goodsInfo'] = $goodsInfo;
                $arr['scene'] = $scene;
                $arr['status'] = 1;
                $arr['mode']=1;    //微信1   支付宝 2
                $arr['data']=$thistime;
                $arr['gid']=$url1;
                $id = Db::table($table)->insertGetId($arr);
                if ($id == false) {
                    return json_encode(['errno' => ERRNO_ADD_FAILL, 'error' => ERROR_ADD_FAILL]);
                }
                $orderId = $mon . '_' . $id;



            $data['org_code'] = SELF::JL_CODE;
            $data['merch_no'] = $eid;
            // $data['appid'] = SELF::APPID;
//            $data['trade_type'] = 'pay.alipay.native';
            $data['trade_type'] = 'pay.alipay.native';
            $data['out_trade_no'] = $orderId;
            $data['order_date'] = date('YmdHis');
            $data['trade_amount'] = $amount;
            $data['goods_name'] = $goodsInfo;
            $data['notify_url'] =$url1;
            $data['notify_url'] = 'http://payapi.tech100.com.cn/index.php/index/Jlpay/UnifiedPay';
            $data['client_ip'] = $_SERVER['REMOTE_ADDR'];
            $data['nonce_str'] = uniqid('',true);
            //dump($data);
            $s = $this->queryString($data);
            $sign = $this->sign($s);
            $data['sign'] = $sign;
                $url = 'https://openapi.jlpay.com/qrcode/trans/unified/order';
//            $url = 'http://payapi.tech100.com.cn/index.php/index/Jlpay/UnifiedPay';
           $d = http_post($url,json_encode($data));
            $h =json_decode($d,true);
           // dump($h);
            if ($h['ret_code'] != '00'){

                return  $d;
            }
            $ree['merch_no']=$h["merch_no"];
            $ree['order_no']= $h["order_no"];
            $ree['out_trade_no']= $h["out_trade_no"];
            $ree['qrcode_url']= $h["qrcode_url"];
            $ree['ret_code']=  '成功';
            $ree['ret_msg']=  $h["ret_msg"];
            $ree['trade_amount']= $h["trade_amount"];
            return json_encode($ree);
           // $updata= Db::table('auto_bills')->where('eid',$eid)->where('data',$thistime)->update(['status'=>0]); //更新支付成功状态
//            return $this->fetch('payAe',['fee'=>$amount,'orderId'=>$orderId,'ali'=>$h['qrcode_url']]);

        }
//        }else{
//            $re['code']='10028';
//            $re['msg']='签名错误';
//            return json_encode($re);
//        }

    }

    public function bindDir(){

        $merch_no = $this->request->param('merch_no','84937015045K004');
        $data['org_code'] = SELF::JL_CODE;
        $data['merch_no'] = $merch_no;
        $data['jsapi_path'] = 'http://lang.tech100.com.cn/index.php/index/jlpay/';
        // $data['jsapi_path'] = 'http://shop.tech100.com.cn/html/user/singleBuy';
        $data['nonce_str'] = uniqid('',true);
        $s = $this->queryString($data);
        // dump($s);
        $sign = $this->sign($s);
        // dump($sign);
        $data['sign'] = $sign;
        $url = 'https://openapi.jlpay.com/qrcode/trans/merchant/auth';
        $da = http_post($url,json_encode($data));
        //dump($da);
        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['data'] = $da;
        return json_encode($re);
        // return 0;
    }

    // 签名前字符串
    public function queryString($data = []){
        if (!is_array($data) || empty($data)) return ;
        $data = array_filter($data);
        $buff = '';
        ksort($data);
        foreach ($data as $k => $v){
            if ($k != 'sign'){
                $buff .= $k . '=' . $v . '&';
            }
        }
        $buff = substr($buff,0,-1);
        //return $buff;
        // $str = sha1($buff);
        return $buff;

    }
    public function notify(){
        $data = $this->request->post();
        if (empty($data)){
            return 'fail';
        }
        $str = $this->queryString($data);
        $string = file_get_contents(ROOT_PATH.'public.jl.txt');
        $t = SELF::rsaCheck($str,$string,$data['sign']);

        if ($t && $data['status'] == 'S'){
            $arr = explode('_',$data['out_trade_no']);
            $da = Db::table('auto_bills_'.$arr[0])->field("eid,amount,goodsInfo,status,scene,gid")->where("`id`",$arr[1])->find();
            if($da['status'] == 1){
//                return 'success';
                $this->redirect($da['gid'],['ret_code'=>1,'out_trade_no'=>$da['goodsInfo'],'ret_msg'=>'支付成功']);
//                header('Location: ' . $da['gid']);
//                return  \url($da['gid']).$da['status'];
            }
            $co =Db::table('auto_bills_'.$arr[0])->where("`id`",$arr[1])->update(['status'=>1,'order_no'=>$data['order_no']]);
            if ($co == false){
                $this->redirect($da['gid'],['ret_code'=>0,'out_trade_no'=>$da['goodsInfo'],'ret_msg'=>'支付失败']);
//                return 'FAIL';
            }
            $d = Db::table('auto_equipment')->field("dev")->where("eid", $da['eid'])->find();

            if($da['scene'] == 1) {
                $do = config('auto_test');
                $url = $do . '/api/push/data?imei=' . $d['dev'];
                $c = json_decode($da['goodsInfo'], true);
                $n = count($c);
                $a = [];
                foreach ($c as $k => $v) {
                    $a[] = $v['no'] . '_' . $v['qty'];
                }
                $dat = [$data['out_trade_no'], $da['eid'], $n, $a];
                $httpCode = 200;
                $s = curl_post($url, json_encode($dat), $httpCode);

                if ($httpCode == 200) {
                    if ($s == 2000) {

                    }
                } else {
                    Db::table('auto_repeat')->insert(['orderId' => $data['out_trade_no']]);
                }
            }elseif (in_array($da['scene'], [2,3])){
                $do = config('pbyx');
                $url = $do.'/push?project=test'.'&device='.$da['eid'];
                $arrb['type'] = 1;
                $arrb['data'] = ['id'=>$data['out_trade_no']];
                curl_post($url,json_encode($arrb));
            }
            $amou = $da['amount'];
            $eqt = Db::table('auto_eq_total')->where('eid',$da['eid'])->find();
            if (empty($eqt)){
                Db::table('auto_eq_total')->insert(['eid'=>$da['eid'],'total'=>$da['amount'],'bi'=>1,'time'=>date("Y-m-d H:i:s")]);
            }else{
                Db::table('auto_eq_total')
                    ->where('eid',$da['eid'])
                    ->update([
                        'total'  => ['exp',"total+$amou"],
                        'bi' => ['exp','bi+1'],
                    ]);
            }
            return 'success';
        }
        return 'fail';
    }

    //支付签名字符串
    /**
     * @param $string 原始字符串
     * @return string  签名串
     */
    public function sign($string){
        $priKey = file_get_contents(ROOT_PATH.'pri.key.txt');
        $res = openssl_get_privatekey($priKey,'auto_shop');
        openssl_sign($string,$sign,$res);
        openssl_free_key($res);
        // dump($sign);
        return base64_encode($sign);
    }
    public function test22(){
        dump(Cache::get('jjj111'));
        dump(Cache::get('hh'));
        dump(Cache::get('http'));
        dump(Cache::get('ooo'));
        return 0;
    }

    public function create_table($table){

        $sql = 'CREATE TABLE IF NOT EXISTS `'.$table.'` ('.
            '`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,'.
            '`eid` CHAR(16) DEFAULT NULL,'.
            '`mode` TINYINT(4) DEFAULT 0,'.
            '`field` varchar(10) DEFAULT NULL,'.
            '`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,'.
            '`amount` DECIMAL(7,2) NOT NULL DEFAULT 0,'.
            '`status` TINYINT(4) DEFAULT 0,'.
            '`wx_id` INT(10) DEFAULT 0,'.
            '`scene` TINYINT(4) DEFAULT 1,'.
            '`refund_amount` DECIMAL(7,2) NOT NULL DEFAULT 0,'.
            '`order_no` VARCHAR(32) NOT NULL,'.
            '`goodsInfo` VARCHAR(6400) NOT NULL,'.
            '`gid` varchar(10) DEFAULT NULL,'.
            'PRIMARY KEY (`id`),'.
            'INDEX  (`eid`),'.
            'INDEX  (`wx_id`),'.
            'INDEX  (`time`)'.

            ') ENGINE=INNODB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8';

        Db::execute($sql);
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $public_key 公钥字符串
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    public static function rsaCheck($data, $public_key, $sign)  {
        $search = [
            "-----BEGIN PUBLIC KEY-----",
            "-----END PUBLIC KEY-----",
            "\n",
            "\r",
            "\r\n"
        ];
        $public_key=str_replace($search,"",$public_key);
        $public_key=$search[0] . PHP_EOL . wordwrap($public_key, 64, "\n", true) . PHP_EOL . $search[1];
        $res=openssl_get_publickey($public_key);
        if($res)
        {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
            openssl_free_key($res);
        }else{
            //exit("公钥格式有误!");
            return false;
        }
        return $result;
    }


    //嘉联订单查询订单
    public function jlOrderQuery(){
        $merch_no = $this->request->param('merch_no');
        $out_trade_no = $this->request->param('orderId');
        $data['merch_no'] = "84944037372K014";
        $data['org_code'] = SELF::JL_CODE;
        $data['nonce_str'] = uniqid();
        $data['out_trade_no'] = '5026370120181218105548';
        $s = $this->queryString($data);
        $sign = $this->sign($s);
        $data['sign'] = $sign;
        $url = 'https://openapi.jlpay.com/qrcode/trans/query';
        $dat = http_post($url,json_encode($data));
        dump($dat);
        dump(json_decode($dat,true));
        if (empty($merch_no)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] ='商户不能为空';
            return json_encode($res);
        }
        if (strpos($out_trade_no,'_')){
            $data['merch_no'] = $merch_no;
            $data['org_code'] = SELF::JL_CODE;
            $data['nonce_str'] = uniqid();
            $data['out_trade_no'] = $out_trade_no;
            $s = $this->queryString($data);
            $sign = $this->sign($s);
            $data['sign'] = $sign;
            $url = 'https://openapi.jlpay.com/qrcode/trans/query';
            $dat = http_post($url,json_encode($data));
            dump($dat);
            dump(json_decode($dat,true));
            return 0;
        }
        return 0;

    }
    //退款
    public function refund(){
        $private_key =file_get_contents(ROOT_PATH.'private_key.txt');

        $public_key = file_get_contents(ROOT_PATH.'public_key.txt');
        $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $sn=$this->request->post('sn');
        $cc=openssl_private_decrypt(base64_decode($sn),$decrypted,$pi_key);//私钥解密
        $analy=base64_decode($sn);

        $en2=json_decode($analy,true);
        if ($cc==1) {
            $merch_no = $this->request->param('merch_no');
            if ($merch_no == null) {
                $re['code'] = '3000';
                $re['msg'] = '商户号不能为空';
                return json_encode($re);
            }
            $order_no = $this->request->param('order_no');
            if ($order_no == null) {
                $re['code'] = '3001';
                $re['msg'] = '订单号不能为空';
                return json_encode($re);
            }
            $org_code = $this->request->param('org_code');
            if ($org_code == null) {
                $re['code'] = '3003';
                $re['msg'] = '机构号不能为空';
                return json_encode($re);
            }
            $out_trade_no = $this->request->param('out_trade_no');
            if ($out_trade_no == null) {
                $re['code'] = '3004';
                $re['msg'] = '商户订单号不能为空';
                return json_encode($re);
            }
            $nonce_str = md5(uniqid('', true));        //随机字符串
            $refund_amount = $this->request->param('refund_amount');
            if ($refund_amount == null) {
                $re['code'] = '3004';
                $re['msg'] = '退款金额不能为空';
                return json_encode($re);
            }
            $trade_amount = $this->request->param('trade_amount');
            if ($trade_amount == null) {
                $re['code'] = '3004';
                $re['msg'] = '总金额不能为空';
                return json_encode($re);
            }
            $da['merch_no'] = $merch_no;
            $da['order_no'] = $order_no;
            $da['org_code'] = $org_code;
            $da['out_trade_no'] = $out_trade_no;
            $da['nonce_str'] = $nonce_str;
            $da['refund_amount'] = $refund_amount;
            $da['trade_amount'] = $trade_amount;
            $s = $this->queryString($da);
            $sign = $this->sign($s);
           // dump($sign);
            $da['sign'] = $sign;
            $url = 'https://openapi.jlpay.com/qrcode/trans/refund/order';
            $data = http_post($url, json_encode($da));
            dump($data);
            $h=(json_decode($data, true));
            if ($h['ret_code'] != '00') {
                return $s;
            }
            return $h['status'];
        }else{
            $re['code'] = '3009';
            $re['msg'] = '签名不匹配';
            return json_encode($re);
        }

    }
    public function tuiQuery(){
        $private_key =file_get_contents(ROOT_PATH.'private_key.txt');

        $public_key = file_get_contents(ROOT_PATH.'public_key.txt');
        $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $sn=$this->request->post('sn');
        $cc=openssl_private_decrypt(base64_decode($sn),$decrypted,$pi_key);//私钥解密
        $analy=base64_decode($sn);
        if ($cc==1) {
            $merch_no = $this->request->param('merch_no');
            if ($merch_no == null) {
                $re['code'] = '3000';
                $re['msg'] = '商户号不能为空';
                return json_encode($re);
            }
            $org_code = $this->request->param('org_code');
            if ($org_code == null) {
                $re['code'] = '3003';
                $re['msg'] = '机构号不能为空';
                return json_encode($re);
            }
            $out_trade_no = $this->request->param('out_trade_no');
            if ($out_trade_no == null) {
                $re['code'] = '3004';
                $re['msg'] = '商户订单号不能为空';
                return json_encode($re);
            }
            $nonce_str = md5(uniqid('', true));        //随机字符串
            $da['merch_no'] = $merch_no;
            $da['org_code'] = $org_code;
            $da['out_trade_no'] = $out_trade_no;
            $da['nonce_str'] = $nonce_str;
            $s = $this->queryString($da);
            $sign = $this->sign($s);
            $s = $this->queryString($da);
            $sign = $this->sign($s);
            dump($sign);
            $da['sign'] = $sign;
            $url = 'https://openapi.jlpay.com/qrcode/trans/refund/query';
            $data = http_post($url, json_encode($da));
            dump($data);
            dump(json_decode($data, true));
            if ($h['ret_code'] != '00') {
                return $s;
            }
            return $h['status'];
        }
    }
    //账单
    public function bills(){
        $da = ["agentId" =>  "50263701",
            "date" => '20181127'
        ];
        $st = $this->str($da);
        dump($st);
        return 0;
        // $da['sign_data'] = $st;
        $url = "http://dev.jlpay.com/accountFile/download/{$da['agentId']}/{$da['date']}";
        $d = curl_post($url,$da);
        dump($d);
        return 0;
    }
    //账单签名
    private function str($data=[]){
        if (!is_array($data) || empty($data))return;
        $data = array_filter($data);

        ksort($data);
        $bu = json_encode($data);
        $priKey = file_get_contents(ROOT_PATH.'private.key.txt');
        $res = openssl_get_privatekey($priKey,'auto_shop');
        openssl_sign($bu,$sign,$res,OPENSSL_ALGO_SHA256);
        openssl_free_key($res);
        // dump($sign);

        $signData = base64_encode($sign);
        return $signData;

    }

    //微信公众号绑定支付目录
//    public function bindDir(){
//        $data['org_code'] = SELF::JL_CODE;
//        $data['merch_no'] = '84944035965K003';
//        $data['jsapi_path'] = 'http://lang.tech100.com.cn/home/index.php/index/UnifiedPay/';
//        $data['nonce_str'] = uniqid('',true);
//        $s = $this->queryString($data);
//        dump($s);
//        $sign = $this->sign($s);
//        dump($sign);
//        $data['sign'] = $sign;
//        $url = 'https://openapi.jlpay.com/qrcode/trans/merchant/auth';
//        $da = http_post($url,json_encode($data));
//        dump($da);
//        return 0;
//    }
    //订单信息
    public function orderGoodsInfo(){
        $orderId = $this->request->param('orderId');
        if (empty($orderId)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] =ERROR_PARAM_INVALID;
            return json_encode($res);
        }
        //  $da = Db::table('auto_order_goods')->where('orderid',$orderId)->select();
        $arr = explode('_',$orderId);
        $dat = Db::table('auto_bills_'.$arr[0])->field("amount,goodsInfo")->where('id',$arr[1])->find();
        $domain = $this->request->domain();
        $tem = '/home/uploads/';
        $dir = $domain.$tem;
        $da = json_decode($dat['goodsInfo'],true);
        foreach ($da as $k => $v){
            $d = Db::table('auto_goods')->field("name,img,code")->where('id',$v['gid'])->find();
            $da[$k]['name'] = $d['name'];
            $da[$k]['code'] = $d['code'];
            if (!empty($d['img'])) $da[$k]['img'] = $dir.$d['img'];
        }
        $re['amount'] = $dat['amount'];

        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['data'] = $da;
        return json_encode($re);
    }
    //订单重新发请求
    public function orderRepeat(){
        $orderId = $this->request->param('oderId');
        if (empty($orderId)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] =ERROR_PARAM_INVALID;
            return json_encode($res);
        }
        $arr = explode('_',$orderId);
        $da = Db::table('auto_repeat')->where('orderId',$orderId)->find();
        if (empty($da)){
            $res['errno'] =ERRNO_ACTION_INVALID;
            $res['error'] =ERROR_ACTION_INVALID;
            return json_encode($res);
        }
        if ($da['flag'] == 1){
            $res['errno'] =1022;
            $res['error'] ='该订单已发过出货请求';
            return json_encode($res);
        }
        $dat = Db::table('auto_bills_'.$arr[0])->field("eid,goodsInfo")->where("`id`",$arr[1])->find();
        $c = json_decode($dat['goodsInfo'],true);
        $n = count($c);
        $a = [];
        foreach ($c as $k => $v){
            $a[] = $v['no'].'_'.$v['qty'];
        }
        // $dat = ['data'=>['orderId'=>$data['out_trade_no'],'eid'=>$da['eid'],'count'=>$n,'goodsInfo'=>$a]];
        $dat = [$orderId,$dat['eid'],$n,$a];
        $d = Db::table('auto_equipment')->field('dev,Machine_type')->where('eid',$dat['eid'])->find();

        $do = config('auto_test');
        $url = $do . '/api/push/data?imei=' . $d['dev'];

        switch ($d['Machine_type']) {
            case 0:
                $s = curl_post($url, json_encode($data));
                break;
            case 2:
                foreach ($go as $k => $v){
                    $a.= $v['no'].'_'.$v['qty'];
                }
                $s = curl_post($url,$da['orderId'].','.$eid.','.$n.','.$a);
                break;
            default:
                $s = curl_post($url, json_encode($data));
                break;
        }


        curl_post($url,json_encode($dat));
        Db::table('auto_repeat')->where('orderId',$orderId)->update(['flag'=>1]);
        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;

        return json_encode($re);
    }
    //
    public function test7(){
        $d = Db::table('auto_goods')->where('eid','5231564897854621')->limit(1)->select();
        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['data'] = $d;
        return json_encode($re);
        dump(json_encode($d));
        // dump($d);
        return 0;
        $da = Db::table('auto_bills_201811')->where('id',104)->find();
        dump($da);
        $c = json_decode($da['goodsInfo'],true);
        $n = count($c);
        $a = [];
        foreach ($c as $k => $v){
            $a[] = $v['no'].'_'.$v['qty'];
            //$a[]['qty'] = $v['qty'];

        }
        $data = ['201811_104',$da['eid'],$n,$a];
        dump($data);
        $b = json_encode($data);
        dump($b);
        dump(json_decode($b,true));
        return 0;
    }
    //退款申请
    public function refundApply(){

        $orderId = $this->request->param('orderId');
        $des = $this->request->param('des');
        if (!strpos($orderId,'_')){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] = '订单号错误';
            return json_encode($res);
        }
        if(empty($des)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] = '请输入信息';
            return json_encode($res);
        }
        $arr = explode('_',$orderId);
        $da = Db::table('auto_bills_'.$arr[0])->field("amount,eid,status,scene")->where('id',$arr[1])->find();
        if (empty($da) || $da['status'] == 0){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] = '订单无效';
            return json_encode($res);
        }
        $d = Db::table('auto_refund')->where('orderId',$orderId)->find();
        if (empty($d)){
            $dat = Db::table('auto_equipment')->field("mid")->where('eid',$da['eid'])->find();
            $id =Db::table('auto_refund')->insertGetId(['total'=>$da['amount'],'orderId'=>$orderId,'des'=>$des,'mid'=>$dat['mid'],'ctime'=>time(),'scene'=>$da['scene'],'eid'=>$da['eid']]);
            if ($id == false){
                $re['error'] = ERROR_ADD_FAILL;
                $re['errno'] = ERRNO_ADD_FAILL;

                return json_encode($re);
            }
            $re['error'] = ERROR_SUCCESS;
            $re['errno'] = ERRNO_SUCCESS;

            return json_encode($re);
        }
        $res['errno'] = ERRNO_ALREADY_APPLY;
        $res['error'] = ERROR_ALREADY_APPLY;
        return json_encode($res);
    }
    //订单查询
    public function orderQuery(){
        $orderId = $this->request->param('orderId');
        if (!strpos($orderId,'_')){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] = '订单号错误';
            return json_encode($res);
        }
        $arr = explode('_',$orderId);
        $da = Db::table('auto_bills_'.$arr[0])->field("scene,status,order_no")->where('id',$arr[1])->find();
        if (empty($da) || $da['status'] == 0){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] = '订单无效';
            return json_encode($res);
        }
        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['data'] = $da;
        return json_encode($re);
    }
    //从微信公众号生成订单
    public function createWxOrder(){
        $eid = $this->request->param('eid');
        $amount = floatval($this->request->param('amount'));
        $scene =$this->request->param('scene',5);
        $buy = $this->request->param('buyInfo');
        $wx_id = $this->request->param('wx_id');

        if (strlen($eid)!== 16 || $amount<=0 || empty($buy) || empty($wx_id)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] =ERROR_PARAM_INVALID;
            return json_encode($res);
        }

        $buy = json_decode($buy,true);
        foreach ($buy as $v){
            if (floatval($v['price']) <= 0){
                $res['errno'] = ERRNO_PARAM_INVALID;
                $res['error'] ='价格设置错误';
                return json_encode($res);
            }
            if (intval($v['qty']) <= 0){
                $res['errno'] = ERRNO_PARAM_INVALID;
                $res['error'] ='购买数量格式错误';
                return json_encode($res);
            }
            if (intval($v['num']) <=0 || intval($v['num'])< intval($v['qty'])){
                $res['errno'] = ERRNO_PARAM_INVALID;
                $res['error'] ="编号{$v['no']}库存不够";
                return json_encode($res);
            }
        }
        $d = Db::table('auto_equipment')->field("mid")->where('eid',$eid)->find();
        if (empty($d['mid'])){
            $res['errno'] = ERRNO_NOT_EXIST;
            $res['error'] =ERROR_NOT_EXIST;
            return json_encode($res);
        }

        $da = Db::table('auto_merchant')->field('jl_merchant,status')->where('id',$d['mid'])->find();
        if ($da['status'] != 1 || empty($da['jl_merchant'])){
            $res['errno'] = ERRNO_MERCHANT_FORBIDDEN;
            $res['error'] =ERROR_MERCHANT_FORBIDDEN;
            return json_encode($res);
        }
        $mon = date("Ym");
        $table = 'auto_bills_'.$mon;
        $this->create_table($table);
        $arr['eid'] = $eid;
        $arr['amount'] = $amount;
        $arr['goodsInfo'] = json_encode($buy);
        $arr['scene'] = $scene;
        $arr['mode'] = 1;
        $arr['fie']= 'shop';
        $arr['wx_id'] = $wx_id;
        $id = Db::table($table)->insertGetId($arr);
        if ($id == false) {
            return json_encode(['errno' => ERRNO_ADD_FAILL, 'error' => ERROR_ADD_FAILL]);
        }
        $out_trade_no = $mon . '_' . $id;
        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['orderId'] = $out_trade_no;
        $re['amount'] = $amount;
        return json_encode($re);
    }
    //在微信公众号里支付
    public function wxPay(){
        $eid = $this->request->param('eid');
        $amount = floatval($this->request->param('amount'));
        $orderId = $this->request->param('orderId');
        $openid = $this->request->param('openid');
        if (strlen($eid) != 16 || $amount <= 0 ||empty($openid) || !strpos($orderId,'_')){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] =ERROR_PARAM_INVALID;
            return json_encode($res);
        }
        $a = explode('_',$orderId);
        $o = Db::table('auto_bills_'.$a[0])->field("eid,status")->where('id',$a[1])->find();
        if (empty($o)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] ='该订单号无效';
            return json_encode($res);
        }
        if($o['status'] != 0){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] ='该订单已支付';
            return json_encode($res);
        }

        $d = Db::table('auto_equipment')->field("mid")->where('eid',$eid)->find();
        if (empty($d['mid'])){
            $res['errno'] = ERRNO_NOT_EXIST;
            $res['error'] =ERROR_NOT_EXIST;
            return json_encode($res);
        }
        $da = Db::table('auto_merchant')->field('jl_merchant,status')->where('id',$d['mid'])->find();
        if ($da['status'] != 1 || empty($da['jl_merchant'])){
            $res['errno'] = ERRNO_MERCHANT_FORBIDDEN;
            $res['error'] = ERROR_MERCHANT_FORBIDDEN;
            return json_encode($res);
        }
        // $amount = 10;
        $data['org_code'] = SELF::JL_CODE;
        $data['merch_no'] = $da['jl_merchant'];

        $data['trade_type'] = 'pay.weixin.jsapi';
        $data['openid'] = $openid;
        $data['out_trade_no'] = $orderId;
        $data['order_date'] = date('YmdHis');
        $data['trade_amount'] = $amount*100;
        $data['goods_name'] = '自动贩卖机店';
        $data['notify_url'] = 'http://payapi.tech100.com.cn/home/index.php/index/jlpay/notify';
        $data['client_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['nonce_str'] = uniqid('',true);
        //dump($data);
        $s = $this->queryString($data);
        $sign = $this->sign($s);
        $data['sign'] = $sign;
        //dump($data);
        //    $url = 'https://openapi.jlpay.com/qrcode/trans/unified/order';
        $url = 'http://lang.tech100.com.cn/home/index.php/index/Jlpay/UnifiedPay';
        $d = http_post($url,json_encode($data));
        //dump($d);
        $h =json_decode($d,true);
        if ($h['ret_code'] != '00'){
            return $d;
        }
        // dump($h);
        // return 0;
        return $this->fetch('wxpay',['fee'=>$amount,'orderId'=>$orderId,'name'=>$h['pay_info'],'eid'=>$eid]);
    }
    //使用取件码
    public function codeOut(){
        $code = $this->request->param('code');
        $eid = $this->request->param('eid');
        if (isset($_SESSION[$code.$eid])) {
            if (time()-$_SESSION[$code.$eid]<5) {
                $res['errno'] = 1007;
                $res['error'] ='请不要重复使用';
                return json_encode($res);
            }
        }
        $_SESSION[$code.$eid] = time();
        if (strlen($eid) != 16 || strlen($code) != 13){
            $res['errno'] = 1007;
            $res['error'] ='取件码无效';
            return json_encode($res);
        }
        $where['eid'] = $eid;
        $where['code'] = $code;
        $da = Db::table('auto_code')->field("eid,status,orderId")->where($where)->find();
        if(empty($da) || $da['status'] != 1 ){
            $res['errno'] = 1007;
            $res['error'] = '取件码已使用1';
            return json_encode($res);
        }
        $arr = explode('_',$da['orderId']);
        $dat = Db::table('auto_bills_'.$arr[0])->field("status,goodsInfo")->where('id',$arr[1])->find();
        if ($dat['status'] != 1){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] ='该订单号失效';
            return json_encode($res);
        }
        $go = json_decode($dat['goodsInfo'],true);
        if (empty($go)){
            $res['errno'] = ERRNO_PARAM_INVALID;
            $res['error'] ='该订单购买信息无效';
            return json_encode($res);
        }
        $d = Db::table('auto_equipment')->alias('e')->field('e.dev,c.use_server,e.Machine_type')->join("auto_eq_cate c","e.cid=c.id")->where('e.eid',$eid)->find();
        // dump($d);
        if (empty($d)){
            $res['errno'] =ERRNO_NOT_EXIST;
            $res['error'] ='设备已注销请联系管理员';
            return json_encode($res);
        }
        $n = count($go);
        $a = [];
        $b = [];
        foreach ($go as $k => $v){
            $a[] = $v['no'].'_'.$v['qty'];
            $b[]['no'] =intval($v['no']);
        }
        $do = config('auto_test');
        $url = $do . '/api/push/data?imei='.$d['dev'];
        $s = curl_post($url,'online');
        if ($s != 'online'){
            return '设备不在线,请勿购买';
        }


        $url = $do . '/api/push/data?imei=' . $d['dev'];
        $data = [$da['orderId'],$eid,$n,$a];
        Db::table('auto_code')->where('code',$code)->update(['status'=>2]);


        switch ($d['Machine_type']) {
            case 0:
                $s = curl_post($url, json_encode($data));
                break;
            case 2:
                foreach ($go as $k => $v){
                    $a.= $v['no'].'_'.$v['qty'];
                }
                $s = curl_post($url,$da['orderId'].','.$eid.','.$n.','.$a);
                break;
            default:
                $s = curl_post($url, json_encode($data));
                break;
        }
        if ($s == 2000) {
            Db::table('auto_code')->where('code',$code)->update(['status'=>0]);
            $re['error'] = ERROR_SUCCESS;
            $re['errno'] = ERRNO_SUCCESS;
            $re['data'] = ['orderId'=>$da['orderId']];
            return json_encode($re);
        }

        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['data'] = ['msg' => '出货失败'];
        return json_encode($re);


    }

    //出票
    public function outTicket(){
        $eid = $this->request->param('eid');
        $printId = $this->request->param('printNotifyId');
        $qty = $this->request->param('ticketStock');
        $status = $this->request->param('result');
        if (strlen($eid) != 16 || $printId <= 0 || !isset($status)){
            $res['errno'] =ERRNO_PARAM_INVALID;
            $res['error'] =ERROR_PARAM_INVALID;
            return json_encode($res);
        }

        $di = Db::table("auto_cp_out")->where("id",$printId)->update(['status'=>$status]);
        if ( $di == false){
            $res['errno'] =ERRNO_ACTION_INVALID;
            $res['error'] =ERROR_ACTION_INVALID;
            return json_encode($res);
        }
        $re['error'] = ERROR_SUCCESS;
        $re['errno'] = ERRNO_SUCCESS;
        $re['data'] = ['eid'=>$eid,'printNotifyId'=>$printId,'result'=>$status,'ticketStock'=>$qty];
        return json_encode($re);
    }

    //格子总销量
    public function totalNum(){
        $db = DB::table('auto_goods')->field('eid')->where('id in (select max(id) from auto_goods group by eid having count(eid)>1)')->select();
        $dearr[0] = '';
        for ($i=0; $i < count($db); $i++) {
            // var_dump($db[$i]['eid']);
            $dbc = DB::table('auto_equipment')->where('eid',$db[$i]['eid'])->find();
            if (empty($dbc)) {
                $dearr[$k] = $db[$i]['eid'];
                $k++;
            }
        }
        for ($i=0; $i < count($dearr); $i++) {
            $dedb = DB::table('auto_goods')->where('eid',$dearr[$i])->delete();
        }

        var_dump($db);
    }

    public function UnifiedPay()
    {
        $data = input('param.');
        $url = 'https://openapi.jlpay.com/qrcode/trans/refund/order';
        $d = http_post($url,json_encode($data));
        return $d;
    }



}
//ENGINE=InnoDB AUTO_INCREMENT=3 UNION=(lang_orders_0,lang_orders_1,lang_orders_2,lang_orders_3,lang_orders_4,lang_orders_5,lang_orders_6,lang_orders_7,lang_orders_8,lang_orders_9) INSERT_METHOD=LAST DEFAULT CHARSET=utf8;
/*
 * CREATE TABLE `auto_bills_201811` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eid` char(16) NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(7,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `order_no` varchar(32) NOT NULL,
  `goodsInfo` varchar(6400) NOT NULL,
  `refund_amount` decimal(7,2) NOT NULL,
  `scene` tinyint(3) unsigned NOT NULL,
  `wx_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `eid` (`eid`,`time`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8;
 *
 *
 *
 *
 *
 */

/*
CREATE TABLE `lang_orders_arr` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
 `eid` char(16) NOT NULL DEFAULT '' COMMENT '设备号',
 `orderNO` char(32) NOT NULL DEFAULT '' COMMENT '订单号',
 `orderId` char(32) DEFAULT NULL,
 `payFrom` tinyint(2) unsigned NOT NULL COMMENT '支付方式:1微信码付、2微信公众号付、11支付宝码付、21云闪付',
 `shopPrice` decimal(7,2) unsigned NOT NULL COMMENT '价格',
 `aisleId` int(11) unsigned NOT NULL COMMENT '货道号',
 `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '购买时间',
 `isPrize` tinyint(1) unsigned NOT NULL COMMENT '中奖',
 `isDelivery` tinyint(1) unsigned NOT NULL COMMENT '出货状态',
 `isPay` tinyint(1) unsigned NOT NULL COMMENT '支付状态:0未支付、1已支付、2部分退款、3全额退款',
 `refund_num` decimal(7,2) unsigned NOT NULL COMMENT '退款金额',
 `payMod` tinyint(1) unsigned NOT NULL COMMENT '购买模式:0普通购买、1幸运购',
 `payNum` tinyint(5) unsigned NOT NULL COMMENT '购买数量',
 `wx_id` int(11) unsigned NOT NULL COMMENT '微信id',
 `goodsId` int(11) unsigned NOT NULL COMMENT '商品id',
 `delivery_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '出货时间',
 PRIMARY KEY (`id`),
 KEY `_eid` (`eid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
*/

?>