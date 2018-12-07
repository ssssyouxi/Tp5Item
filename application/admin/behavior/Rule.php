<?php
namespace app\admin\behavior;

use think\Controller;
use think\Request;
use think\facade\Cookie;
use think\facade\Config;
use \app\admin\model\Admin; //查表admin
class Rule extends Controller{
	public function run(Request $request){

        // dump(cookie('date').cookie('user').Config::get('token'));
        if(!session('?userid')){

            //判断有无cookie
            if((cookie('?id') && cookie('?user')) && (time()-cookie('date')<=3600*24*10) && password_verify(cookie('date').cookie('user').Config::get('token'),cache(cookie('id')))){

                // password_verify(cookie('date').cookie('user').Config::get('token'),cache(cookie('id')));
                $res = Admin::alias('a')
                    ->field('a.id,a.userid,a.pwd,a.usertype')
                    ->join(Config::get('database.prefix')."admintype s",'a.usertype=s.rank')
                    ->field('s.typename')
                    ->where(['a.userid'=>cookie('user')])
                    ->find();
                
                if($res){
                    session('usertype',$res['usertype']);
                    session('typename',$res['typename']);
                }else{
                    session('usertype',"未知");
                    session('typename',"未知");
                }
                $ip=$request->ip();
                Admin::where('id',$res['id'])->update(['logintime'=>time(),'loginip'=>$ip]);
                session('userid',cookie('user'));

            } elseif($request->controller()!='Login') { 
                
                return $this->redirect('/admin/login');
            }
        } elseif($request->controller()=='Login' && $request->action()=='index') {
            
            return $this->redirect('/admin/index');
        }
	}
}