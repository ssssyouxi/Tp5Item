<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

use \think\facade\Request;
use \app\admin\model\Addonarticle; //查表addonarticle
use \app\admin\model\Arctype; //查表Arctype
use \app\admin\model\Archives; //查表Archives
use \app\admin\model\Arcatt; //查表Arcatt
use think\facade\Env;

#use think\facede\Env;


class News extends Base
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
                            ->where('a.arcrank','neq','-2')
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
                            ->where('a.arcrank','neq','-2')
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
                        ->where('a.typeid',input('get.id'))
                        ->where('delete_time',null)
                        ->where('s.arcrank','neq','-2')
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
        $dat = input("post.");
        $imglist =json_decode(input('post.imglist'));
        $dat = $this->eachimg($imglist,$dat);
        
        $channel = Db::name("arctype")
                            ->field("channeltype")
                            ->where("id",input('post.typeid'))
                            ->find();
        $table = Db::name("channeltype")
                            ->field("addtable")
                            ->where("id",$channel['channeltype'])
                            ->find();
        
        $dat['flag'] = $flag;
        $dat['senddate'] = time();
        $dat['pubdate'] = time();
        $dat['sortrank'] = time();
        $dat['channel']=$channel['channeltype'];
        $res2 = Db::name('arctiny')
                        ->strict(false)
                        ->insertGetId($dat);
        $dat['aid']=$res2;
        $dat['id']=$res2;
        $res = Archives::strict(false)->insert($dat);
        
        $dat['body'] = input('post.editorValue');
        $res1 = Db::table($table['addtable'])
                        ->strict(false)
                        ->insert($dat);
        if($res!=0 && $res1!=0){
            return  ['code'=>1,'msg'=>'增加成功'];
        }else{
            return  ['code'=>0,'msg'=>'增加失败'];
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
        $news['body'] = isset($news['body'])?htmlspecialchars_decode(html_entity_decode($news['body'])):'';
        $this->assign("arcatt",$arcatt);
        $this->assign("wxj",html_entity_decode($wxj));
        $this->assign("news",$news);
        $this->assign("res",$res);
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
        $data = input('post.');
        $imglist =json_decode(input('post.imglist'));

        $flag = input('post.flag');
        $flag1 = isset($flag) ? implode(",",$flag) : '';
        $data['flag']=$flag1;
        $data['pubdate']=time();
        $data = $this->eachimg($imglist,$data);
        $channel = Db::name("arctype")
                            ->field("channeltype")
                            ->where("id",input('post.typeid'))
                            ->find();
        $table = Db::name("channeltype")
                            ->field("addtable")
                            ->where("id",$channel['channeltype'])
                            ->find();
        $res = Archives::where('id',input('post.id'))  //对于总表archives的更新
                        ->strict(false)
                        ->update($data);
        $res1 = Db::table($table['addtable'])->where('aid',input('post.id')) //对于模型对应的表的更新
                            ->strict(false)
                            ->update($data);
        $res2 = Db::name('arctiny')->where('id',input('post.id')) //对于公共表arctiny的更新
                            ->strict(false)
                            ->update(input("post."));
        if($res!==false && $res1!==false && $res2!==false){
            return  ['code'=>1,'msg'=>'修改成功'];
        }else{
            return  ['code'=>0,'msg'=>'修改失败'];
        }
    }
    private function eachimg($arr,$data){
        foreach($arr as $key => $value){
            /*if(is_array($value) && array_key_exists("0",$value)!==false){
                    $this->eachimg($value,$data); 
            }else{*/
            if(is_array($value)){
                foreach($value as $k =>$v){
                    if(strripos($v,'temp')!==false){
                        $path = str_replace('temp','uploads',$v);
                        if(rename(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $v,Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $path)){
                            return ['code'=>1,'msg'=>'修改成功'];
                        }else{
                            return ['code'=>0,'msg'=>'修改失败'];
                        }
                        $value[$k] = $path;
                    }
                }
                $value = implode(",",$value);
            }else{
                if(strripos($value,'temp')!==false){
                    $path = str_replace('temp','uploads',$value);
                    if(rename(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $value,Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $path)){
                        
                    }else{
                        
                    }
                    $arr[$key] = $path;
                }
            }
















            // if(is_array($value)){
            //     $value = implode(",",$value);
            // }
            
            $data[$key]=$value;
            /*}*/
        }
        return $data;
    }
    //对新闻的软删除
    public function delNews($id){
        $id = json_decode($id);
        $res1 = Archives::where(['id'=>$id])
                        ->update(['arcrank'=>'-2']);
        $res= Archives::destroy($id);
        
        
        // dump(Archives::getLastSql());
        if($res!==false && $res1 !== false){
            return ['code'=>1,'msg'=>'删除成功！','id'=>$id];
        }else{
            return ['code'=>0,'msg'=>'删除失败！'];
        }
    }

    //获得当前类型的样式   
    //待移至模板引擎上
    private function gettype($arr,$news = ""){
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
                case 'imgfile':
                
                $res.="
                <div class='row cl'>
				<label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."：</label>
				<div class='formControls col-xs-8 col-sm-9'>
					<div class='uploader-thum-container'>
						<div id='' class='uploader-list'>
							
							<img src='".$a."' alt='' >
							
                            <input type='file' name='".$v['field']."' class='singleimg' id='".$v['field']."' style='display:none' />
                            <button type='button' value='上传图片' onclick=document.getElementById('".$v['field']."').click()  ></button>
							
						</div>
					</div>
				</div>
			</div>
                ";
                break;
                case 'img':
                if($a){
                    if(stristr($a,"dede:")){
                        preg_match_all("/{[^}]*}([^{]*){\/[^}]*}/",$a,$r);
                        //preg_match("/\'}(.*){\//",$a,$r);
                        $c = '';
                        foreach ($r[1] as $key => $value) {
                            $c .="<img src='".$value."' class='many' data-id=".$v['field']." data-src='".$value."' onclick='removeimg($(this))' style='width: 20%;max-width: 150px'/>"; 
                        }
                    }else{
                        $c = '';
                        $arr = explode(",",$a);
                        foreach($arr as $key =>$value){
                            $c .="<img src='".$value."' class='many' data-id=".$v['field']." data-src='".$value."' onclick='removeimg($(this))' style='width: 20%;max-width: 150px'/>";
                        }
                    }
                    
                    
                    
                }else{
                    $c = $a;
                }

                $res.=
                "
                <div class='row cl'>
                    <label class='form-label col-xs-4 col-sm-2'>".$v['itemname']."(一张或多张)：</label>
                    <div class='formControls col-xs-8 col-sm-9'>
                        ".$c."
                        <input type='file' name='".$v['field']."[]' class='manyimg' id='".$v['field']."' style='display:none' />
                        <button type='button' onclick=document.getElementById('".$v['field']."').click() >点击上传</button>
                        
						
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