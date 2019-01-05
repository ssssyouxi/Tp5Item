<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class Product extends Base
{
    // 展示产品详情
     public function artproduct(Request $request){
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
          
          if(!$res){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
          }
          $id=$res['id'];
        }
       
            $artpro = Db::view('sh_addonimages18','*')
            ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
            ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
            ->where('sh_addonimages18.aid',$id)
            ->find();  
               if(stristr($artpro['bpic'],"{dede")!==false){
                preg_match('/}(.*){/',$artpro['bpic'],$arr);
                $artpro['bpic']=trim($arr[1]);
               }           
            if($artpro['outputsize']!==""){
                $artpro['outputsize']= '<strong>'.str_replace(':','&nbsp;:</strong>&nbsp;',$artpro['outputsize']).'</br>';
            }
            
            $artpro['application']= '<strong>'.str_replace(':','&nbsp;:</strong>&nbsp;',$artpro['application']);
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
    public function ppc_article(Request $request){
        if(input("?id")) {
            $id=input("id"); 
            $ppc = Db::name('archives')
            ->where('id', $id)
            ->find();
            if(!$ppc){
                abort(404, '页面异常');
                // return view(Env::get('app_path') . 'index/404.html',404);
            }
        }
        else{
          $fname=input("filename");
          $ppc = Db::name('archives')
          ->where(['filename'=>$fname,'arcrank'=>0])
          ->find();
          if(!$ppc){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
        }
          $id=$ppc['id'];
        }
            $artppc = Db::view('sh_addon21','*')
            ->view('sh_archives','*','sh_addon21.aid=sh_archives.id')
            ->view('sh_arctype','typename','sh_addon21.typeid = sh_arctype.id')
            ->where('sh_addon21.aid',$id)
            ->find();  
            $ppc_list = Db::name('archives')
            ->where(['typeid'=>34,'arcrank'=>0])
            ->field('title,id,filename')
            ->order('id', 'desc')
            ->select();          
        $this->assign("artppc",$artppc);
        $this->assign("ppc",$ppc);
        $this->assign("ppc_list",$ppc_list);
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function products(){
        $this->casetype(); 
        $this->newstype(); 
        $this->mobile();
        return $this->menu();

    }
    public function mobile_list(){
        $this->casetype(); 
        $this->newstype(); 
        $this->mobile();
        return $this->menu();
        return $this->fetch();   
    }
    public function stationary_list(){
        
        $this->casetype(); 
        $this->newstype(); 
        $this->mobile();
        return $this->menu();
        return $this->fetch();   
    }


    //测试
    public function sta_type(Request $request){
        $type = str_replace("-"," ",input("type"));
        $typeid = [
            "Jaw Crushers"=> "3",
            "Impact Crushers"=>"4",
            "Cone Crushers"=>"5",
            "VSI Crushers"=>"6"
        ];
        
        $res = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename,typedir','sh_addonimages18.typeid = sh_arctype.id')
        ->where(['sh_addonimages18.typeid'=>$typeid[$type],'arcrank'=>0])
        ->select(); 
        if(!$res){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
        }
        $this->assign("res",$res);
        $this->casetype(); 
        $this->menu();
        return $this->fetch();   
    }


    public function grindingmill_list(){
        $this->casetype(); 
        $this->newstype(); 
        $this->mobile();
        return $this->menu();
        return $this->fetch();   
    }
    public function vsi_list(){
        $proliv = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
        //->where('sh_addonimages18.typeid',9)
        ->where(['sh_addonimages18.typeid'=>[9],'arcrank'=>0])
        ->order('weight', 'asc')
        ->select(); 
        // if(!$proliv[0]['typename']){
        //     abort(404, '页面异常');
        //     // return view(Env::get('app_path') . 'index/404.html',404);
        // }
        $this->assign("proliv",$proliv);
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu();
        return $this->fetch();   
    }
    public function aggregate_production_line_list(){
        $agg = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
        ->where('sh_addonimages18.typeid',8)
        ->order('weight', 'asc')
        ->select(); 
        // if(!$proliv[0]['typename']){
        //     abort(404, '页面异常');
        //     // return view(Env::get('app_path') . 'index/404.html',404);
        // }
        $this->assign("agg",$agg);
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu();
        return $this->fetch();   
    }
    public function aggregate_production_line_article(Request $request)
    {
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
          if(!$res){
            abort(404, '页面异常');
            // return view(Env::get('app_path') . 'index/404.html',404);
        }
          $id=$res['id'];
        }
        $artagg = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
        ->where('sh_addonimages18.aid',$id)
        ->find();            
        preg_match('/}(.*){/',$artagg['bpic'],$arr);
        $artagg['bpic']=trim($arr[1]);       
        $relist = Db::name('archives')
        ->where(['typeid'=>8,'arcrank'=>0])
        ->field('title,litpic,id')
        ->limit(3)
        ->order('id', 'desc')
        ->select();

    $this->assign("relist",$relist);
    $this->assign("artagg",$artagg);
    $this->assign("res",$res);
    $this->casetype(); 
    $this->newstype(); 
    return $this->menu(); 
    return $this->fetch(); 
    }
    public function feeding_conveying_list(){
        $feed = Db::view('sh_addonimages18','*')
        ->view('sh_archives','*','sh_addonimages18.aid=sh_archives.id')
        ->view('sh_arctype','typename','sh_addonimages18.typeid = sh_arctype.id')
        ->where(['sh_addonimages18.typeid'=>[10],'arcrank'=>0])
        ->order('weight', 'asc')
        ->select(); 
        // if(!$feed['typename']){
        //     abort(404, '页面异常');
        //     // return view(Env::get('app_path') . 'index/404.html',404);
        // }
        $this->assign("feed",$feed);
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu();
        return $this->fetch();   
    }

}

