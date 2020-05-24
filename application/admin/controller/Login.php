<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class Login extends Controller{
    public function login(){
        return $this->fetch();
    }
    public function adder(){
        return $this->fetch();
    }
    public function index(){
        $username = $this->request->param('username');     //用户名
        $password = $this->request->param('password');      //密码
        Session::set('name',$username);
        $db = Db::table('auto_user')->where('tel',$username)->where('pass',md5($password))->find();
        if ($db){
          return $this->success('登录成功','Index/index');
           // return '登录失败';
        }else{
            return $this->success('账户或用户名错误','login');
//            $res['errno'] = 500;
//            $res['Explain'] = "登录失败";
//            return $res;
        }
    }
    //注册
    public  function add(){

        $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $randStr = str_shuffle($str);//打乱字符串
        $time=date("Y-m-d H:i:s");
        $secretk = substr($randStr,0,8);//substr(string,start,length);返回字符串32位
        $yzm=$this->request->param('yzm');
        $tel = $this->request->param('tel');      //手机号码
        $sql =Db::table('auto_user')->where('tel',$tel)->find();
        if (strlen($tel)!=11){
            $re['error']=1000;
            $re['Explain']="请正确输入手机号码";
            return $re;
        }
        if ($sql==true){
            $re['error']=1001;
            $re['Explain']="此手机号码已注册,请登录";
            return $re;
        }
        $password = $this->request->param('pwd');      //密码
        $code = $this->request->param('code');      //确认密码
        if ($password!=$code){
            $re['error']=1002;
            $re['Explain']="两次输入密码不一样";
            return $re;
        }
        $Nickname = $this->request->param('Nickname'); // 昵称
        if ($password==null&&$tel==null&&$Nickname==null){
            $re['error']=1003;
            $re['Explain']="请填写完整信息";
            return $re;
        }
        if(!captcha_check($yzm)){
            $re['error']=1;
            $re['Explain']="验证码错误";
            return $re;
        }
        $user['tel']=$tel;
        $user['pass']=md5($password);
        $user['Nickname']=$Nickname;
        $user['secretk']=md5($secretk);
        $user['add_time']=$time;
        $user_insert =Db::table('auto_user')->insert($user);
        if ($user_insert==true){
            $re['error']=0;
            $re['Explain']="注册成功";
            return $re;
        }else{
            $re['error']=1000;
            $re['Explain']="注册失败";
            return $re;
        }
    }
    public function mdsuccess(){
       return $this->fetch();
    }
}
