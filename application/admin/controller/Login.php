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
            return $this->error('请输入账号/密码！');
        }

        if(!captcha_check(input("post.captcha"))){
            return $this->error('验证码错误，请重新登录！');
           };

        // dump(input("post."));
        $res = Admin::alias('a')
                    ->field('a.id,a.userid,a.pwd,a.usertype,a.uname')
                    ->join(Config::get('database.prefix')."admintype s",'a.usertype=s.rank')
                    ->field('s.typename')
                    ->where(
                    ['a.userid'=>input("post.userid"),
                        'a.pwd'=>substr(md5(input("post.pwd")),5,20)])
                    ->find();
                    
        // dump($res);
        
        if($res){
            if(input('?post.online')){
                // $str = md5(time());
                // Cookie::set('hash',$str,3600*24*10);
                // $arr[$str]=session([
                //     'userid'=>input("post.pwd")
                // ]);
                // $arr[$str]['userid'] = input("post.userid");
                // Cache::set('check',$arr[$str],3600*24*10);
                cookie('id',$res['id'],3600*24*10);
                $user = input("post.userid");
                cookie('user',$user,3600*24*10);
                $date = time();
                cookie('date',$date,3600*24*10);
                // dump($date.$user.Config::get('token'));
                $pwd = password_hash($date.$user.Config::get('token'),PASSWORD_DEFAULT);
                cache($res['id'],$pwd,3600*24*10);
                cookie('tk',$pwd,3600*24*10);
            }else{
                // session($res['id'],input("post.userid"));
                cookie('id',$res['id']);
                $user = input("post.userid");
                cookie('user',$user);
                $date = time();
                cookie('date',$date);
                $pwd = password_hash($date.$user.Config::get('token'),PASSWORD_DEFAULT);
                // $pwd = substr(hash("sha256",($res['id'].input("post.userid").Config::get('token'))),12,62);
                cache($res['id'],$pwd);
            }
            $ip=$request->ip();
            Admin::where('id',$res['id'])->update(['logintime'=>time(),'loginip'=>$ip]);
            session('userid',$user);
            session('usertype',$res['usertype']);
            session('typename',$res['typename']);
            session('uname',$res['uname']);
            $this->success('登陆成功，即将进入管理首页', '/admin/index');
        }else{
            $this->error('账号/密码错误，请重新登录');
        }
    }
    public function logout(){
        session('userid',null);
        session('usertype',null);
        session('typename',null);
        cookie('id',null);
        cookie('user',null);
        cookie('date',null);
        cookie('tk',null);
        $this->success('已退出登录', '/admin/login');
    }
}