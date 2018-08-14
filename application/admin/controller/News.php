<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

use \think\facade\Request;
use \app\admin\model\Addonarticle; //查表addonarticle
use \app\admin\model\Arctype; //查表Arctype
use \app\admin\model\Archives; //查表Archives
use \app\admin\model\Arcatt; //查表Arcatt

#use think\facede\Env;


class News extends Controller
{
    
    //显示首页
    public function company()
    {
        $Company = Addonarticle::alias('a')
                                ->join('sh_archives s','a.aid = s.id')
                                ->join('sh_arctype t','a.typeid = t.id')
                                ->order('s.sortrank','desc')
                                ->where(['a.typeid'=>'12','delete_time'=>null])
                                ->select();
        $count = count($Company);
        $this->assign("count",$count);
        $this->assign("company",$Company);
        return $this->fetch();
    }

    //增加一条新闻
    public function add(){
        $weight = (Archives::max('weight'))+1;
        $arcatt = Arcatt::select();
        $this->assign("weight",$weight);
        $this->assign("arcatt",$arcatt);
        return $this->fetch();
    }
    public function addNews(Request $request){
        if($request ==''){
            return "未接收到任何数据！";
            exit();
        }
        // foreach (input('post.') as $k => $v) {
        //     # code...
        //     if(isset($k)){
        //         "$k"=>$v
        //     }
        // }
        if(input('post.flag')){
            $flag = implode(",",input('post.flag'));
        }else{
            $flag = '';
        }
        $res = Archives::strict(false)->insertGetId([
                                            'title'=>input('post.title'),
                                            'flag'=>$flag,
                                            'weight'=>input('post.weight'),
                                            'click'=>input('post.click'),
                                            'keywords'=>input('post.keywords'),
                                            'description'=>input('post.description'),
                                            'writer'=>input('post.writer'),
                                            'source'=>input('post.source'),
                                            'litpic'=>input('post.litpic'),
                                            'senddate'=>time(),
                                            'filename'=>input('post.filename'),
                                            'typeid'=>"12"
                                            ]);
        $res1 = Addonarticle::insert([
                                'aid'=>$res,
                                'body'=>input('post.editorValue'),
                                
                            ]);
        if($res!=0 && $res1!=0){
            return '添加成功！';
        }else{
            return '添加失败！';
        }
    }
    //展示新闻详情
    public function article($id)
    {
        $news = Addonarticle::view('sh_addonarticle','typeid,body')
                                ->view('sh_archives','*','sh_addonarticle.aid=sh_archives.id')
                                ->view('sh_arctype','typename','sh_addonarticle.typeid = sh_arctype.id')
                                ->where('sh_addonarticle.aid',$id)
                                ->find();
        $type = Arctype::view('Arctype','id,reid,topid,typename')->select()->toArray();
        $arcatt = Arcatt::select();
        
        // dump($type);
        // dump($this->getCate($type));
        // dump($arcatt[4]['att']);
        // dump($news['flag']);
        // dump((strpos( $news['flag'],$arcatt[4]['att'])) !== false ? true : false);
        $this->assign("arcatt",$arcatt);
        $this->assign("type",$type);
        $this->assign("news",$news);
        return $this->fetch();
    }
    // private function getCate($arr){
    //     $arr_top = [];
    //     $arr_son = [];
    //     foreach($arr as $v){

    //         if ($v['topid'] == 0) {
    //             $arr_top[$v['id']] = $v;
    //         } else {
    //             $arr_son[] = $v;
    //         }
    //     }
    //     return $this->getSon($arr_top, $arr_son);
        

    // }
    // private function getSon($arr_top, $arr_son) {
    //     dump('start');
    //     dump($arr_top);
    //     dump('end');
    //     foreach($arr_son as $v){
    //         if (isset($arr_top[$v['reid']])) {
    //             $arr_top[$v['reid']]['son'][$v['id']] = $v;
    //             $arr_p[] = $v;
    //         } else {
    //             $arr_s[] = $v;
    //         }
    //     }
    //     if (empty($arr_s)) {
    //         return $arr_top;
    //     }
    //     $this->getSon($arr_p, $arr_s);
    // }
    
    //修改新闻详细内容
    public function updateNews(Request $request){
        if($request ==''){
            return "未接收到任何数据！";
            exit();
        }
        $res = Archives::where('id',input('post.id'))
                        ->update([
                            'title'=>input('post.title'),
                            'flag'=>implode(",",input('post.flag')),
                            'weight'=>input('post.weight'),
                            'click'=>input('post.click'),
                            'keywords'=>input('post.keywords'),
                            'description'=>input('post.description'),
                            'writer'=>input('post.writer'),
                            'source'=>input('post.source'),
                            'litpic'=>input('post.litpic')
                            ]);
        $res1 = Addonarticle::where('aid',input('post.id'))
                            ->update([
                                'body'=>input('post.editorValue')
                            ]);
        if($res!=0||$res1!=0){
            return '修改成功！';
        }else{
            return '修改失败！您未修改任何项目';
        }
    }

    //对新闻的软删除
    public function delNews($id){
        $id = json_decode($id);
        $res= Archives::destroy($id);
        if($res){
            return ['code'=>1,'msg'=>'删除成功！','id'=>$id];
        }else{
            return ['code'=>0,'msg'=>'删除失败！'];
        }
    }

}