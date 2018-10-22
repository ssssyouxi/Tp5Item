<?php
namespace app\admin\controller;
use think\Controller;
use \think\Request;
use \app\admin\model\Admin; //查表admin
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Cache;

Class login extends Controller{

    //首页展示
    public function index(){

        // dump(Config::pull('captcha'));
        return $this->fetch();
    }

    //验证账号和密码
    public function check(Request $request){
       

        
        if($request ==''){
            $this->error('请输入账号/密码！');
        }

        if(!captcha_check(input("post.captcha"))){
            $this->error('验证码错误，请重新登录！');
           };

        $res = Admin::where(
                    ['userid'=>input("post.userid"),
                        'pwd'=>substr(md5(input("post.pwd")),5,20)])
                    ->find();
                       
        if($res){
            if(input('post.online')){
                // $str = md5(time());
                // Cookie::set('hash',$str,3600*24*10);
                // $arr[$str]=session([
                //     'userid'=>input("post.pwd")
                // ]);
                // $arr[$str]['userid'] = input("post.userid");
                // Cache::set('check',$arr[$str],3600*24*10);
                cookie('id',$res['id'],3600*24*10);
                cookie('user',input("post.userid"),3600*24*10);
                $psw = substr(sha1($res['id'].input("post.userid")),5,20);
                cache($res['id'],$psw,3600*24*10);
            }else{
                // session($res['id'],input("post.userid"));
                cookie('id',$res['id']);
                cookie('user',input("post.userid"));
                $psw = substr(sha1($res['id'].input("post.userid")),5,20);
                cache($res['id'],$psw);
            }
            session('userid',input("post.userid"));
            $this->success('登陆成功，即将进入管理首页', '/admin/index');
        }else{
            $this->error('账号/密码错误，请重新登录');
        }
    }
    public function logout(){
        session('userid',null);
        cookie('id',null);
        cookie('user',null);
        $this->success('已退出登录', '/admin/login');
    }
}