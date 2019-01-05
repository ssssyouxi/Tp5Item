<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class Caselist extends Base
{

     public function case_index(){
        $caselt = Db::view('sh_addon19','aid')
        ->view('sh_archives','id,title,litpic','sh_addon19.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir,reid','sh_addon19.typeid = sh_arctype.id')
        ->where('sh_addon19.typeid','in','24,25,26,27,28,29,30')
        //->where(['sh_archives.channel'=>[19],'arcrank'=>0])
        //->where('arcrank', 'in', 0)
        ->paginate(16);
        foreach($caselt as $typedir => $value);
        $page = $caselt->render();
        // $page = str_replace("?page=","/list_24_",$page);
        dump($page);
        exit;
        $this->assign("caselt",$caselt);
        $this->assign("page",$page);
        $this->casetype();
        $this->newstype();  
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function case_list(Request $request){
        $title = input("typedir");
        $tydir="/".input("typedir");
        $tyres = Db::name('arctype')
        ->field('id')
        ->where(['typedir'=>$tydir,'corank'=>0])
        ->find();
        $typeid=$tyres['id'];
        $caselt = Db::view('sh_addon19','aid')
        ->view('sh_archives','id,title,litpic','sh_addon19.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir','sh_addon19.typeid = sh_arctype.id')
       // ->where('sh_addon19.typeid',$typeid)
        ->where(['sh_addon19.typeid'=>$typeid,'arcrank'=>0])
        ->paginate(16);
        $tpid = $typeid;
        $this->assign("caselt",$caselt);
        $this->assign("tpid",$tpid);
        $this->assign("tydir",$tydir);
        $this->assign("title",$title);
        $this->casetype(); 
        $this->newstype(); 
        $this->assign("tyres",$tyres);
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function case_article(Request $request){
        if(input("?id")) {
            $id=input("id"); 
            $res = Db::name('archives')
            ->where('id', $id)
            ->find();
        }
        else{
          $fname=input("filename");
          $res = Db::name('archives')
          ->where(['filename'=>$fname,'arcrank'=>0])
          ->find();
          $id=$res['id'];
        }
        $caseart = Db::view('sh_addon19','*')
        ->view('sh_archives','*','sh_addon19.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addon19.typeid = sh_arctype.id')
        ->where('sh_addon19.aid',$id)
        ->find();
        preg_match('/}(.*){/',$caseart['bigpic'],$arr);
        $caseart['bigpic']=trim($arr[1]);
        $list = Db::view('sh_addon19','aid')
        ->view('sh_archives','id,title,litpic','sh_addon19.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addon19.typeid = sh_arctype.id')
        ->where('sh_addon19.typeid','in','25,26,27,28,29,30')
        ->orderRand()
        ->limit(16)
        ->select();
        $this->assign("caseart",$caseart); 
        $this->assign("list",$list); 
        $this->casetype();
        $this->newstype();  
        return $this->menu(); 
        return $this->fetch();                       
    }

}

