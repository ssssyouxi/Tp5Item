<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class Solution extends Base
{
    public function solution_index(){
        $solutionlist = Db::view('sh_addonarticle22','*')
        ->view('sh_archives','*','sh_addonarticle22.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonarticle22.typeid = sh_arctype.id')
        ->where('sh_addonarticle22.typeid',20)
        ->select(); 
        $this->assign("solutionlist",$solutionlist);
        $this->casetype(); 
        $this->newstype();  
        return $this->menu();
        return $this->fetch();   
    }
    public function solution_article(Request $request){
        if(input("?id")) {
            $id=input("id"); 
            $res = Db::name('archives')
            ->where('id', $id)
            ->find();
            if(!$res){
                abort(404, '页面异常');
                // return view(Env::get('app_path') . 'index/404.html',404);
            }
        }
        else{
          $fname=input("filename");
          $res = Db::name('archives')
          ->where(['filename'=>$fname,'arcrank'=>0])
          ->find();
          $id=$res['id'];
          if(!$res){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
        }
        }

        $artpro = Db::view('sh_addonarticle22','*')
        ->view('sh_archives','*','sh_addonarticle22.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonarticle22.typeid = sh_arctype.id')
        ->where('sh_addonarticle22.aid',$id)
        ->find();                   
        $relist = Db::name('archives')
        ->where(['typeid'=>[1,2,7,8,10],'arcrank'=>0])
        ->field('title,litpic,id')
        ->limit(3)
        ->order('id', 'desc')
        ->select();

    $this->assign("relist",$relist);
    $this->assign("artpro",$artpro);
    $this->assign("res",$res);
    $this->casetype(); 
    $this->newstype(); 
    return $this->menu(); 
    return $this->fetch();                       
}

}

