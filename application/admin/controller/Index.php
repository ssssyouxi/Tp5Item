<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;


class Index extends Controller
{
    public function index()
    {
        $AddonArt = Db::name("addonarticle");
        $Archives = Db::name("archives");
        $ArtCount = $AddonArt->count("aid");
        $todayArt =  $Archives->whereBetweenTime("pubdate",'today');
        $this->assign("countArt",$ArtCount);
        $this->assign("todayArt",$todayArt);
        $this->assign('name', 'thinkphp');

        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
