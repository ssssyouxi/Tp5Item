<?php
namespace app\index\controller;
use \think\Controller;
use \think\Request;
use \think\Db;
class Abc extends Base{
    public function index(Request $request){
        $path = $request->path();
        $arr = explode("/",$path);
        $aaa = 111;
        $this->assign("bbb",new Db());
        $this->assign("aaa",$aaa);
        return $this->fetch();
        var_dump($arr);
    }
}