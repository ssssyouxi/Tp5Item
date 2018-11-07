<?php
namespace app\admin\behavior;

use think\Controller;
use think\Request;
use think\facade\Cookie;
use think\facade\Config;
class Rule extends Controller{
	public function run(Request $request){
		if($request->controller()!='Login'&& !cookie('id')&&!cookie('user')){
            //如果没有进入登录页面并且cookie没有值
            return $this->redirect('/admin/login');//进入登录页面
            
        }elseif($request->controller()=='Login'&&substr(hash("sha256",(cookie('id').cookie('user').Config::get('token'))),5,20)==cache(cookie('id'))&&$request->action()!='logout'){
            //如果进入登陆页面而且cookie中的值和缓存值匹配
            //substr(sha1(cookie('id').cookie('user').Config::get('token')),5,20)==cache(cookie('id'))
            //cookie('sha1')==cache(cookie('id'));
            // session(cache('check')[cookie("hash")]['userid']);
            session('userid',cookie('user'));
            return $this->redirect('/admin/index');//进入首页

        }elseif($request->controller()=='Login'&&session('userid')&&$request->action()=='index'){
            //如果进入登陆页面而且session有值而且方法是首页
            return $this->redirect('/admin/index');//进入首页
            
		}elseif($request->controller()!='Login'&&cookie('id')&&cookie('user')){
            //如果没有进入登录页面并且cookie有值
            session('userid',cookie('user'));//给session设置值
        }
	}
}