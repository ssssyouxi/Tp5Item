<?php
namespace app\admin\behavior;

use think\Controller;
use think\Request;
use think\facade\Cookie;
use think\facade\Config;
class Rule extends Controller{
	public function run(Request $request){

        dump(cookie('date').cookie('user').Config::get('token'));
        if(!session('?userid')){

            //判断有无cookie
            if((cookie('?id') && cookie('?user')) && (time()-cookie('date')<=3600*24*10) && password_verify(cookie('date').cookie('user').Config::get('token'),cache(cookie('id')))){

                // password_verify(cookie('date').cookie('user').Config::get('token'),cache(cookie('id')));
                session('userid',cookie('user'));

            } elseif($request->controller()!='Login') { 
                
                return $this->redirect('/admin/login');
            }
        } elseif($request->controller()=='Login' && $request->action()=='index') {
            
            return $this->redirect('/admin/index');
        }
	}
}