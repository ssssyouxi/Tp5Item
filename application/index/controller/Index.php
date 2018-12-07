<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\model\Archives; //查表archives
use \app\index\model\Addonarticle; //查表addonarticle
use \app\index\model\Arctype; //查表Arctype
use \app\index\controller\Base;

class Index extends Base
{
    public function index()
    {
        $this->newstype(); 
        $this->casetype(); 
        return $this->menu(); 
        // return $this->fetch();

    }

     //展示产品详情
     public function Aproduct($id)
     {
        $artpro = Addonarticle::view('sh_addonarticle','typeid,body')
                                 ->view('sh_archives','*','sh_addonarticle.aid=sh_archives.id')
                                 ->view('sh_arctype','typename','sh_addonarticle.typeid = sh_arctype.id')
                                 ->where('sh_addonarticle.aid',$id)
                                 ->find();
         $this->assign("artpro",$artpro);
         return $this->fetch();
     }
}
