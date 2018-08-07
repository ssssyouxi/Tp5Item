<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\admin\model\Addonarticle; //查表addonarticle
use \app\admin\model\Arctype; //查表Arctype
use \app\admin\model\Archives; //查表Archives
#use think\facede\Env;


class News extends Controller
{
    
    //显示首页
    public function company()
    {
        $Company = Addonarticle::alias('a')
                                ->join('sh_archives s','a.aid = s.id')
                                ->join('sh_arctype t','a.typeid = t.id')
                                ->order('senddate','desc')
                                ->where('a.typeid','12')
                                ->select();
        $count = count($Company);
        $this->assign("count",$count);
        $this->assign("company",$Company);
        return $this->fetch();
    }


    //展示新闻详情
    public function article($id)
    {
        $news = Addonarticle::view('sh_addonarticle','typeid,body')
                                ->view('sh_archives','*','sh_addonarticle.aid=sh_archives.id')
                                ->view('sh_arctype','typename','sh_addonarticle.typeid = sh_arctype.id')
                                ->where('sh_addonarticle.aid',$id)
                                ->find();
        $type = Arctype::view('Arctype','id,reid,topid,typename')->select();
        foreach($type as $v){
            $arr[$v['topid']][$v['reid']][] = $v['typename']; 
        }
        dump($arr);
        $this->assign("type",$arr);
        $this->assign("type",$type);
        $this->assign("news",$news);
        return $this->fetch();
    }
}