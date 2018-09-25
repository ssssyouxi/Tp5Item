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
    public function index(){
        $type = Arctype::view('Arctype','id,reid,topid,typename')->select()->toArray(); 
        $this->assign('type',$type);
        return $this->fetch();
    }
    public function listall(){
        if(input('get.kw')){
            $keywords = input('get.kw');
            $artlist = Db::name("archives")
                            ->alias('a')
                            ->field('a.id,title,typeid,writer,senddate,click')
                            ->join('sh_arctype t',' t.id = a.typeid')
                            ->field('typename')
                            ->order('a.id','desc')
                            ->where(['delete_time'=>null])
                            ->where('a.title','like',"%$keywords%")
                            ->paginate(10,false,['query'=>['kw'=>input("get.kw")]]);
                            
        }else{
            $artlist = Db::name("archives")
                            ->alias('a')
                            ->field('a.id,title,typeid,writer,senddate,click')
                            ->join('sh_arctype t',' t.id = a.typeid')
                            ->field('typename')
                            ->order('a.id','desc')
                            ->where(['delete_time'=>null])
                            ->paginate(10);
        }
        $keywords = isset($keywords) ? $keywords:'';
        $this->assign("kw",$keywords);
        $count = $artlist->total();
        $this->assign("count",$count);
        $this->assign("artlist",$artlist);
        return $this->fetch();
    }
    //显示首页
    public function artlist()
    {

        
        $channeltype = Db::name("arctype")
                            ->field("typename,channeltype")
                            ->where("id",input("get.id"))
                            ->find();
        $addtable = Db::name("channeltype")
                            ->field("addtable")
                            ->where("id",$channeltype['channeltype'])
                            ->find();
        $artlist = Db::table($addtable['addtable'])
                        ->field('aid')
                        ->alias('a')
                        ->join('sh_archives s','a.aid = s.id')
                        ->field('title,writer,senddate,click')
                        ->join('sh_arctype t','a.typeid = t.id')
                        ->field('typename')
                        ->order('s.id','desc')
                        ->where(['a.typeid'=>input('get.id'),'delete_time'=>null])
                        ->paginate(10,false,['query'=>['id'=>input("get.id")]]);
        $this->assign("arctype",input("get.id"));
        $this->assign("typename",$channeltype['typename']);
        $count = $artlist->total();
        
        
        $this->assign("count",$count);
        $this->assign("artlist",$artlist);
        return $this->fetch();
    }

    //增加新闻-新文档
    public function add($typeid,$channel=-2){
        $weight = (Archives::max('weight'))+1;
        $arcatt = Arcatt::select();
        if($channel==-2){
            $channeltype = Arctype::field("channeltype")->where("id",$typeid)->find();
        }else{
            $channeltype["channeltype"] = $channel;
        }






        // $channeltype = Arctype::field("channeltype")->where("id",$typeid)->find();
        $fieldset = Db::name("channeltype")->field("fieldset")->where("id",$channeltype["channeltype"])->find();

        $arr = explode("\n",$fieldset['fieldset']);
        $res_arr = [];
        foreach ($arr as $key => $value) {
            preg_match('/<field:(\w+)\s.*itemname="(\S+)"\s.*type="(\S+)"/i',$value,$r);
            // dump($r);
            if($r){
                $res_arr[] = [
                    'field'=>$r[1],
                    'itemname'=>$r[2],
                    'type'=>$r[3],
                ];
            }
        }
        $res = $this->gettype($res_arr);


















        $type = Arctype::view('Arctype','id,reid,topid,typename,channeltype')->select()->toArray();
        $list = $this->getTree($type);
        $wxj= '';
        foreach($list as $value){
            if($value['channeltype']==$channeltype["channeltype"]){
                $select = $typeid == $value['id']? 'selected': '';
                $wxj.= "<option value='".$value['id']."'".$select.">".str_repeat('—', $value['level']).$value['typename']."</option>";}
        }
        $this->assign("wxj",html_entity_decode($wxj));





        $this->assign("res",$res);
        $this->assign("weight",$weight);
        $this->assign("arcatt",$arcatt);
        return $this->fetch();
    }
    //增加新闻-接收
    public function addNews(Request $request){
        // dump(input('post.'));
        // exit;
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
        $channel = Db::name("arctype")
                            ->field("channeltype")
                            ->where("id",input('post.typeid'))
                            ->find();
        $table = Db::name("channeltype")
                            ->field("addtable")
                            ->where("id",$channel['channeltype'])
                            ->find();
        $dat = input("post.");
        $dat['flag'] = $flag;
        $dat['senddate'] = time();
        $dat['pubdate'] = time();
        $dat['sortrank'] = time();
        $dat['channel']=$channel['channeltype'];
        $res = Archives::strict(false)->insertGetId(
                                            $dat
                                            );
        $data = input("post.");
        $data['aid']=$res;
        $data['body'] = input('post.editorValue');
        dump($data);
        $res1 = Db::table($table['addtable'])
                        ->strict(false)
                        ->insert($data);
        if($res!=0 && $res1!=0){
            $this->success( '添加成功！');
        }else{
            $this->error( '添加失败！');
        }
    }



    //展示新闻详情
    public function article(Request $request)
    {

        $channel = Db::name("archives")->field("channel")->where("id",input("get.id"))->find();
        $addtable = Db::name("channeltype")->field("addtable,fieldset")->where("id",$channel['channel'])->find();
        dump($addtable['addtable']);
        $news = Db::table($addtable['addtable'])
                                ->alias('a')
                                ->view($addtable['addtable'],'*')
                                ->view('sh_archives','*','a.aid=sh_archives.id')
                                ->view('sh_arctype','typename','a.typeid = sh_arctype.id')
                                ->where("a.aid",input('get.id'))
                                ->find();
        /////////////

        $arr = explode("\n",$addtable['fieldset']);
        $res_arr = [];
        foreach ($arr as $key => $value) {
            preg_match('/<field:(\w+)\s.*itemname="(\S+)"\s.*type="(\S+)"/i',$value,$r);
            // dump($r);
            if($r){
                $res_arr[] = [
                    'field'=>$r[1],
                    'itemname'=>$r[2],
                    'type'=>$r[3],
                ];
                // $res_arr['list'] = $list;
            }
        }
        $res = $this->gettype($res_arr,$news);
        /////////////
        $type = Arctype::view('Arctype','id,reid,topid,typename,channeltype')->select()->toArray();
        $arcatt = Arcatt::select();
        
        // dump($type);
        // dump($this->getCate($type));
        // dump($arcatt[4]['att']);
        // dump($news['flag']);
        // dump((strpos( $news['flag'],$arcatt[4]['att'])) !== false ? true : false);
        $list = $this->getTree($type);
        $wxj= '';
        foreach($list as $value){
            if($value['channeltype'] == $news['channel']){
                $select = $news['typeid'] == $value['id']? 'selected': '';
                $wxj.= "<option value='".$value['id']."'".$select.">".str_repeat('—', $value['level']).$value['typename']."</option>";
            }
        }
        $news['body'] = htmlspecialchars_decode(html_entity_decode($news['body']));
        $this->assign("arcatt",$arcatt);
        $this->assign("wxj",html_entity_decode($wxj));
        $this->assign("news",$news);
        $this->assign("res",$res);
        return $this->fetch();
        
    }
    function getTree($array, $reid =0, $level = 0){

        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];
        foreach ($array as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['reid'] == $reid){
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTree($array, $value['id'], $level+1);

            }
        }
        return $list;
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
        dump(input("post."));

        $flag = input('post.flag');
        $flag1 = isset($flag) ? implode(",",$flag) : '';
        $channel = Db::name("arctype")
                            ->field("channeltype")
                            ->where("id",input('post.typeid'))
                            ->find();
        $table = Db::name("channeltype")
                            ->field("addtable")
                            ->where("id",$channel['channeltype'])
                            ->find();
        $res = Archives::where('id',input('post.id'))
                        ->update([
                            'title'=>input('post.title'),
                            'flag'=>$flag1,
                            'typeid'=>input('post.typeid'),
                            'weight'=>input('post.weight'),
                            'click'=>input('post.click'),
                            'keywords'=>input('post.keywords'),
                            'description'=>input('post.description'),
                            'writer'=>input('post.writer'),
                            'source'=>input('post.source'),
                            'litpic'=>input('post.litpic'),
                            'pubdate'=>time()
                            ]);
        $res1 = Db::table($table['addtable'])->where('aid',input('post.id'))
                            ->strict(false)
                            ->data(input("post."))
                            ->update();
        if($res!=0||$res1!=0){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
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

    //获得当前类型的样式
    public function gettype($arr,$news = ""){
        $res="";
        foreach($arr as $k => $v){
            $a = isset($news[$v['field']]) ? $news[$v['field']] : '';
            switch($v['type']){
                case 'htmltext':
                    $res .= 
                        "
                        <div class='row cl'>
                            <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
                            <div class='formControls col-xs-8 col-sm-9'> 
                                <script id='editor_".$v['field']."' type='text/plain' name='".$v['field']."'  style='width:100%;height:400px;'></script> 
                        </div>
                        <input type='hidden' id='".$v['field']."'  value='".$a."'/>
                        </div>
                        <script>
                            var ue_".$v['field']." = UE.getEditor('editor_".$v['field']."');
                            ue_".$v['field'].".addListener('ready',function () {  
                                ue_".$v['field'].".setContent($('#".$v['field']."').val());
                            });
                        </script>";
                    break;
                case 'text':
                    $res.=
                    "
                    <div class='row cl'>
			            <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
			            <div class='formControls col-xs-8 col-sm-9'>
				            <input type='text' class='input-text' value='".$a."' placeholder='' id='' name='".$v['field']."'>
			            </div>
		            </div>
                    ";
                    break;
                case 'number':
                    $res.=
                    "
                    <div class='row cl'>
			            <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
			            <div class='formControls col-xs-8 col-sm-9'>
				            <input type='text' class='input-text' value='".$a."' placeholder='' id='' name='".$v['field']."'>
			            </div>
		            </div>
                    ";
                    break;
                //img项仅供参考
                case 'img':
                if($a){
                    
                    preg_match_all("/{[^}]*}([^{]*){\/[^}]*}/",$a,$r);
                    // //preg_match("/\'}(.*){\//",$a,$r);
                        $c = '';
                    foreach ($r[1] as $key => $value) {
                        $c .="<img src='".$value."' class='eximg' onclick='removeimg(this)'/><input type='text' value='".$value."' name='".$v['field']."[]' style='display:none'/>"; 
                    }
                    
                    
                }else{
                    $c = $a;
                }

                $res.=
                "
                <div class='row cl'>
                    <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
                    <div class='formControls col-xs-8 col-sm-9'>
                        ".$c."
                        <input type='button' value='点击上传' onclick=document.getElementById('".$v['field']."').click() />
                        
						<input type='file' name='".$v['field']."[]' class='".$v['field']."[]' id='".$v['field']."' style='display:none' />
                    </div>
                </div>
                ";
                    break;
                //以上仅供参考
                case 'addon':
                $res.=
                "
                <div class='row cl'>
                    <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
                    <div class='formControls col-xs-8 col-sm-9'>
                        <input type='text' class='input-text' value='".$a."' placeholder='' id='' name='".$v['field']."'>
                    </div>
                </div>
                ";
                    break;
                case 'multitext':
                $res.=
                "
                <div class='row cl'>
                    <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
                    <div class='formControls col-xs-8 col-sm-9'>
                        <textarea  class='textarea' placeholder='说点什么...最少输入10个字符'  placeholder='' id='' name='".$v['field']."'>".$a."</textarea>
                    </div>
                </div>
                ";
                    break;
                default :
                    $res.=$v['itemname'];
            // 'text',
            // 'imgfile',
            // 'multitext',
            // 'number',
            // 'img',
            // 'addon',
            // 'float',
            // 'stepselect',
            // 'datetime',
            // 'int',
            // 'softlinks',
            // 'checkbox',
            // 'textdata'
            }
        }
        return $res;
    }
}