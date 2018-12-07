<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
class Base extends Controller
{
    protected function menu()
    {
        $mobilelist = Db::name('archives')
                        ->where(['typeid'=>1,'arcrank'=>0])
                        ->field('title,id,filename')
                        ->order('id', 'desc')
                        ->select();
        $stationarylist = Db::view('sh_archives','typeid,title,id,filename')
                        ->view('sh_arctype','typedir','sh_archives.typeid = sh_arctype.id')
                        ->where(['typeid'=>[2,3,4,5,6],'arcrank'=>0])
                        ->order('id', 'desc')
                        ->select(); 
        $milllist = Db::name('archives')
                        ->where(['typeid'=>7,'arcrank'=>0])
                        ->field('title,id,filename')
                        ->order('id', 'desc')
                        ->select();
        $aggregatelist = Db::name('archives')
                        ->where(['typeid'=>8,'arcrank'=>0])
                        ->field('title,id,filename')
                        ->order('id', 'desc')
                        ->select();
        $screeninglist = Db::name('archives')
                        ->where(['typeid'=>9,'arcrank'=>0])
                        ->field('title,id,filename')
                        ->order('id', 'desc')
                        ->select();
        $feedinglist = Db::name('archives')
                        ->where(['typeid'=>10,'arcrank'=>0])
                        ->field('title,id,filename')
                        ->order('id', 'desc')
                        ->select();
        $newslist = Db::view('sh_archives','title,id,senddate,typeid')
                        ->view('sh_arctype','typedir','sh_archives.typeid = sh_arctype.id')
                        ->where(['typeid'=>[12,13,14],'arcrank'=>0])
                        ->order('id', 'desc')
                        ->select(); 
                       
        $prolist = Db::view('sh_addonarticle','typeid,body')
                                 ->view('sh_archives','*','sh_addonarticle.aid=sh_archives.id')
                                 ->view('sh_arctype','typename','sh_addonarticle.typeid = sh_arctype.id')
                                 ->find();


        $this->assign("mobilelist",$mobilelist);
        $this->assign("stationarylist",$stationarylist);
        $this->assign("milllist",$milllist);
        $this->assign("aggregatelist",$aggregatelist);
        $this->assign("screeninglist",$screeninglist);
        $this->assign("feedinglist",$feedinglist);
        $this->assign("newslist",$newslist);
        $this->assign("prolist",$prolist);
        // dump($prolist);
        return $this->fetch();

    }
    public function mobile()
    {
        $proli = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
        ->where(['sh_addonimages18.typeid'=>[1],'arcrank'=>0])
        ->select(); 
        $proli2 = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir','sh_addonimages18.typeid = sh_arctype.id')
        ->where(['sh_addonimages18.typeid'=>[2,3,4,5,6],'arcrank'=>0])
        ->order('id', 'desc')
        ->select(); 
        $proli3 = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
        ->where(['sh_addonimages18.typeid'=>[7],'arcrank'=>0])
        ->order('weight', 'asc')
        ->select(); 
        $this->assign("proli",$proli);
        $this->assign("proli2",$proli2);
        $this->assign("proli3",$proli3);
    }
    public function casetype()
    {
        $casetype = Db::name('arctype')
        ->where('id','in','25,26,27,28,29,30')
        ->field('typename,id,typedir')
        ->select();
        // foreach($casetype as $typedir => $value){
        //     $casetype[$typedir]['typedir'] = preg_match('/\/(.*)/',$casetype[$typedir]['typedir'],$arr);
        //     $casetype[$typedir]['typedir']=trim($arr[1]); 
        //     }
        $this->assign("casetype",$casetype); 
    }
    public function newstype()
    {
        $newtype = Db::name('arctype')
        ->where('id','in','12,13,39,14')
        ->field('typename,id,typedir')
        ->select();
        $this->assign("newtype",$newtype); 
    }
    public function faq()
    {
        $faq = Db::name('archives')
        ->where(['typeid'=>14,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(5)
        ->select();
        $this->assign("faq",$faq);
    }

}
