<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;

class Index extends Controller{
    public function index(){
        return $this->fetch();
    }
    public function welcome(){      //我的桌面
        $name =Session::get('name');
        $data=date("Y-m-d H:i:s");
        $this->assign('date',$data);
        $this->assign('name',$name);
        return $this->fetch();
    }
    public function memberlist(){    //会员列表
        $db_auto_user=Db::table('auto_user')->select();
        $this->assign('auto_user',$db_auto_user);
         return $this->fetch();
    }
    public function memberadd(){        //添加
        return $this->fetch();
    }
    public function unicode(){           //加密秘钥
         $name=Session::get('name');
         $data=Db::table('auto_user')->where('tel',$name)->find();
         $this->assign('data',$data);
         return $this->fetch();
    }
    public function orderlist(){      //订单列表
         return $this->fetch();
    }
    public function memberedit(){       //订单列表修改
        return $this->fetch();
    }
    public function memberpassword(){
        return $this->fetch();
    }
    public function orderadd(){      //订单添加
         return $this->fetch();
    }
}