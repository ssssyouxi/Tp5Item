<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class Spec extends Base
{
    public function _empty(){
        //$templet=input("templet");
        

        // $spec = Db::view('sh_addonspec','aid')
        // ->view('sh_archives','id,title','sh_addonspec.aid=sh_archives.id')
        // ->where('sh_addonspec.aid','spec/$name.htm')
        // ->find();
        // $this->casetype(); 
        //  $this->templet(); 
        // return $this->menu(); 
        // $this->assign("templet",$templet);
        return $this->fetch();   
    }

}

