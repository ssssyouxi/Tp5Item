<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\admin\model\Addonarticle; //查表addonarticle
use \app\admin\model\Archives; //查表archives
use \app\admin\model\Admin; //查表admin
#use think\facede\Env;

class Index extends Controller
{
    public function index()
    {

        
        $ArtCount = Addonarticle::where('typeid','>',11)->count('aid'); //文章总数
        $todayArt =  Archives::whereBetweenTime('pubdate','today')->count(); //今日文章
        $yesterdayArt =  Archives::whereBetweenTime('pubdate','yesterday')->count(); //昨日文章
        $weekArt =  Archives::whereBetweenTime('pubdate','week')->count(); //本周文章
        $monthArt =  Archives::whereBetweenTime('pubdate','month')->count(); //本月文章
        
        $ProCount = Addonarticle::where('typeid','<=',11)->count('aid'); //产品总数

        $UserCount = Admin::count("id"); //用户总数

        $adminCount = Admin::where('usertype','10')->count(); //管理员总数

        $this->assign("countArt",$ArtCount);
        $this->assign("todayArt",$todayArt);
        $this->assign("yesterdayArt",$yesterdayArt);
        $this->assign("weekArt",$weekArt);
        $this->assign("monthArt",$monthArt);
        $this->assign("proCount",$ProCount);
        $this->assign("userCount",$UserCount);
        $this->assign("adminCount",$adminCount);
        $this->assign('name', 'thinkphp');

        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
