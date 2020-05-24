<?php
namespace  app\index\controller;


use think\Controller;
use think\Db;

class Login extends Controller{
//登录验证
    public function index(){
        $username = $this->request->param('username');     //用户名
        $password = $this->request->param('password');      //密码
        $db = Db::table('user')->where('name',$username)->where('password',md5($password))->find();
        if ($db==true){
            $res['errno'] = 200;
            $res['Explain'] = "登录成功";
            $res['url']='';
            return json_decode($res);
        }else{
            $res['errno'] = 500;
            $res['Explain'] = "登录失败";
            return json_decode($res);
        }
    }
    //注册
    public  function add(){
        $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $randStr = str_shuffle($str);//打乱字符串
        $secretk = substr($randStr,0,32);//substr(string,start,length);返回字符串32位
        $tel = $this->request->param('tel');      //手机号码
        $sql =Db::table('user')->where('name',$tel)->find();
        if (strlen($tel)!=11){
            $re['error']=1000;
            $re['Explain']="请正确输入手机号码";
            return json_decode($re);
        }
        if ($sql==true){
            $re['error']=1000;
            $re['Explain']="此手机号码已注册,请登录";
            return json_decode($re);
        }
        $password = $this->request->param('password');      //密码
        $code = $this->request->param('code');      //确认密码
        if ($password!=$code){
            $re['error']=1000;
            $re['Explain']="两次输入密码不一样";
            return json_decode($re);
        }
         $Nickname = $this->request->param('Nickname'); // 昵称
        if ($password==null&&$tel==null&&$Nickname==null){
            $re['error']=1000;
            $re['Explain']="请填写完整信息";
            return json_decode($re);
        }
        $user['tel']=$tel;
        $user['pass']=md5($password);
        $user['Nickname']=$Nickname;
        $user['secretk']=md5($secretk);
        $user_insert =Db::table('auto_user')->insert($user);
        if ($user_insert==true){
            $re['error']=0;
            $re['Explain']="注册成功";
            $re['url']='';
            return json_decode($re);
        }else{
            $re['error']=1000;
            $re['Explain']="注册失败";
            return json_decode($re);
        }
    }
}