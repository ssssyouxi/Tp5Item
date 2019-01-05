<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
use think\facade\Env;
class News extends Base
{
    public function news_index(){
        $companypic = Db::name('archives')
        ->where(['typeid'=>12,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(1)
        ->select();
        foreach($companypic as $litpic => $value){
                $companypic[$litpic]['litpic'] = preg_match('/(.*)-lp.*/',$companypic[$litpic]['litpic'],$arr);
                $companypic[$litpic]['litpic']=trim($arr[1]).'.jpg'; 
                }
        $company = Db::name('archives')
        ->where(['typeid'=>12,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(3)
        ->select();
        $industrypic = Db::name('archives')
        ->where(['typeid'=>13,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(1)
        ->select();
        foreach($industrypic as $litpic => $value){
            $industrypic[$litpic]['litpic'] = preg_match('/(.*)-lp.*/',$industrypic[$litpic]['litpic'],$arr);
            $industrypic[$litpic]['litpic']=trim($arr[1]).'.jpg'; 
            }
        $industry = Db::name('archives')
        ->where(['typeid'=>13,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(3)
        ->select();
        $focuspic = Db::view('sh_addonspec','aid,templet')
        ->view('sh_archives','id,title,shorttitle,litpic,senddate,click,description','sh_addonspec.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonspec.typeid = sh_arctype.id')
        ->where('sh_addonspec.typeid',39)
        ->order('id', 'desc')
        ->find();
        preg_match('/\/([^.]*)./',$focuspic['templet'],$arr);
        $focuspic['templet']=trim($arr[1]); 
        $Knowledgepic = Db::name('archives')
        ->where(['typeid'=>14,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(1)
        ->select();
        foreach($Knowledgepic as $litpic => $value){
            $Knowledgepic[$litpic]['litpic'] = preg_match('/(.*)-lp.*/',$Knowledgepic[$litpic]['litpic'],$arr);
            $Knowledgepic[$litpic]['litpic']=trim($arr[1]).'.jpg'; 
            }
        $Knowledge = Db::name('archives')
        ->where(['typeid'=>14,'arcrank'=>0])
        ->field('title,id,senddate,litpic,click,description')
        ->where('flag','p')
        ->order('id', 'desc')
        ->limit(3)
        ->select();
        $this->assign("companypic",$companypic);
        $this->assign("company",$company);
        $this->assign("industrypic",$industrypic);
        $this->assign("industry",$industry);
        $this->assign("focuspic",$focuspic);
        $this->assign("Knowledgepic",$Knowledgepic);
        $this->assign("Knowledge",$Knowledge);
        $this->casetype(); 
        $this->newstype(); 
        $this->faq(); 
        return $this->menu();
        return $this->fetch();   
    }
    public function news_list(Request $request){
        $tydir="/".input("typedir");
        $tyres = Db::name('arctype')
        ->field('id')
        ->where(['typedir'=>$tydir,'corank'=>0])
        ->find();
        if(!$tyres['id']){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
        }
        $typeid=$tyres['id'];
        $newlist = Db::view('sh_addonarticle','aid')
        ->view('sh_archives','id,title,litpic,senddate,click,description','sh_addonarticle.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir','sh_addonarticle.typeid = sh_arctype.id')
       // ->where('sh_addonarticle.typeid',$typeid)
        ->where(['sh_addonarticle.typeid'=>$typeid,'arcrank'=>0])
        ->paginate(6,false,['query' => ['typeid'=>$typeid]]);
        
        $newid = Db::name('arctype')
        ->where('id', $typeid)
        ->find();
        $this->assign("newlist",$newlist);
        $this->assign("newid",$newid);
        $this->casetype();
        $this->newstype(); 
        $this->faq();  
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function news_focus(){
        $spec = Db::view('sh_addonspec','aid,templet')
        ->view('sh_archives','id,title,shorttitle,litpic,senddate,click,description','sh_addonspec.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonspec.typeid = sh_arctype.id')
        //->where('sh_addonspec.typeid',39)
        ->where(['sh_addonspec.typeid'=>39,'arcrank'=>0])
        ->order('id', 'desc')
        ->find();
        preg_match('/\/([^.]*)./',$spec['templet'],$arr);
        $spec['templet']=trim($arr[1]);  
        $newlist4 = Db::view('sh_addonspec','aid,templet')
        ->view('sh_archives','id,title,shorttitle,litpic,senddate,click,description','sh_addonspec.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonspec.typeid = sh_arctype.id')
        ->where('sh_addonspec.typeid',39)
        ->order('id', 'desc')
        ->paginate(6);
        // dump($newlist4);
        $page = $newlist4->render();
        $hui = $newlist4->all();
        foreach($hui as $templet => $value){
                $hui[$templet]['templet'] = preg_match('/\/([^.]*)./',$hui[$templet]['templet'],$arr);
                $hui[$templet]['templet']=trim($arr[1]); 
                }
        $newid = Db::name('arctype')
        ->where('id', 39)
        ->find();
        $this->assign("spec",$spec);
        $this->assign("hui",$hui);
        $this->assign("page",$page);
        $this->assign("newid",$newid);
        $this->casetype();
        $this->newstype(); 
        $this->faq();  
        return $this->menu(); 
        return $this->fetch();  
    }
    public function news_article(Request $request){
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
        dump($id);
        // $resa = Db::name('archives')
        // ->where('id', $id-1)
        // ->find();
        // $resb = Db::name('archives')
        // ->where('id', $id+1)
        // ->find();
        
        $artnew = Db::view('sh_addonarticle','*')
        ->view('sh_archives','*','sh_addonarticle.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonarticle.typeid = sh_arctype.id')
        ->where('sh_addonarticle.aid',$id)
        ->find(); 

        
        if(!$artnew['title']){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
        }
        $resa = Db::view('sh_addonarticle','*')
        ->view('sh_archives','id,title,filename','sh_addonarticle.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir','sh_addonarticle.typeid = sh_arctype.id')
        ->where('sh_addonarticle.aid' ,'<',$id)
        ->where('sh_addonarticle.typeid',$artnew['typeid'])
        ->limit(1)
        ->order('sh_addonarticle.aid','desc')
        ->find(); 
        $resb = Db::view('sh_addonarticle','*')
        ->view('sh_archives','id,title,filename','sh_addonarticle.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir','sh_addonarticle.typeid = sh_arctype.id')
        ->where('sh_addonarticle.aid' ,'>',$id)
        ->where('sh_addonarticle.typeid',$artnew['typeid'])
        ->limit(1)
        ->order('sh_addonarticle.aid','asc')
        ->find();           
        Db::name("archives")->where("id",$id)->setInc('click');
    $this->assign("artnew",$artnew);
    $this->assign("res",$res);
    $this->assign("resa",$resa);
    $this->assign("resb",$resb);
    $this->casetype(); 
    $this->newstype(); 
    $this->faq();   
    return $this->menu(); 
    return $this->fetch();                       
}


}

