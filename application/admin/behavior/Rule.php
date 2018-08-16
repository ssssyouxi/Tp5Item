<?php
namespace app\admin\behavior;

use think\Controller;
use think\Request;
class Rule extends Controller{
	public function run(Request $request){
		if($request->controller()!='Login'&&!session('userid')){
            //如果没有进入登录页面并且session没有值
            return $this->redirect('/admin/login');//进入登录页面
            
        }elseif($request->controller()=='Login'&&cache('check')[cookie("hash")]!=""){
            //如果进入登陆页面而且cookie中的值和缓存值匹配
            session(cache('check')[cookie("hash")]['userid']);
            return $this->redirect('/admin/index');//进入首页

        }elseif($request->controller()=='Login'&&session('userid')&&$request->action()=='index'){
            //如果进入登陆页面而且session有值而且方法是首页
			return $this->redirect('/admin/index');//进入首页
		}
	}
}