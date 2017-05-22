<?php

namespace Mobile\Controller;

use Admin\Controller\AdminController;

 
// Home 模块基类、继承于Admin模块基类获取系统共享方法


class BaseController extends AdminController {
	
	/**
	 * 基类控制器初始化
	 */
	protected function _initialize(){		 
		parent::intiParams();
 	}
 
 	//导航菜单集合
 	protected function menuList(){
 		 
 		return $menus;		
 	}

 	//导航菜单信息
 	protected function menuInfo($id=0){
 		$menu['title']='中心动态';
 		$menu['icon']='';
 		$menu['url']  ='http://www.qtzfcg.gov.cn/zxdt/';
 		return $menu;
 	}

 	//列表集合抓取
 	protected function newsList($adr='',$rowSize=10000){
 		vendor('htmlParse.simple_html_dom');	 	 
	 	$html = file_get_html($adr);	 	 
		if($html){
			$main = $html->find('div[id=TRS] tr');
			foreach ($main as $row => $tr) {
					if($row<=$rowSize){
						$a  = $tr->find('td',1);
						$text= $a->find('a',0)->plaintext;
					 	$date = $tr->find('td',2)->plaintext;     //日期

					 	$link= $a->find('a',0)->href;  //超链接
					 	$sp = explode('/', $link);
					 	$l_mon = $sp[1];

					 	$sp2 = explode('_',$sp[2]);
					 	$l_date= str_replace('t','', $sp2[0]);
					 	$l_id  = str_replace('.htm','', $sp2[1]);

					 	$href = $adr.'/'.$link;

					 	$new['title']=$text;
					 	$new['id']   =$l_id;
					 	$new['mon']  =$l_mon;
					 	$new['date'] =$l_date;
					 	$new['url']  =$href;
					 	$news[] = $new;
					 }
					//logging('<a href="'.$href.'">'.$text.'</a>'. ' : '.$date.' : '.$link . '  月份'.$l_mon . ' 日期 '.$l_date.' ID '.$l_id.'<br>');
					//logging('月份'.$l_mon . ' 日期 '.$l_date.' ID '.$l_id.'<br>');

					//判断是否已经存在新闻
					// $sql = "select * from fn_qingtian where nid=".$l_id;
					// $news = $mysql->doSql($sql);

					//已经存在
					// if(count($news)>0){
					// 	logging($l_id.' : 数据已经存在.');
					// }else{
					// 	$new['nid']=$l_id;
					// 	$new['type']=str_replace($local,'',$adr);
					// 	$new['title']=$text;
					// 	$new['mon']=$l_mon;
					// 	$new['date']=$l_date;
					// 	$new['url']=$link;
					// 	$new['stime']=time();
					// 	$new['status']=1;
					// 	$insert = $mysql->insert('fn_qingtian',$new); 
					// }  		 
			}	  
			$html->clear();
			return $news;
		}else{
			return false;
		}
 	}

 	//内容信息抓取
 	protected function newsInfo($url=''){
 		vendor('htmlParse.simple_html_dom');
 		$html = new \simple_html_dom();
		$html->load_file($url);	
 		//$html = file_get_html($url);
		if($html){
			$newsInfo['title']  = $html->find(".font_bt",0)->plaintext;
			$newsInfo['publish']= $html->find("#property",0)->plaintext;
			$newsInfo['auth']   = $html->find(".content",0)->find(".font_12_24",2)->plaintext;
			$newsInfo['url']    = $url;
			$newsInfo['source'] = substr($url,0,strlen($url)-strpos(strrev($url),'/'));
			$con   = $html->find(".font_text",0)->innertext;

			$newsInfo['content'] =str_replace('face="仿宋_GB2312"', '', $con);//mb_convert_encoding($con,'GB2312','utf-8');
			$html->clear();				
			return $newsInfo;
		}else{
			return false;
		}
		// logging( '标题：'.$title.'<br>');
		// logging( '发布时间：'.$publish.'<br>');
	 	// logging( '撰稿人：'.$auth.'<br>');
	 	// logging($cons);
	 	// $where['nid']=$l_id;
	 	// $unew['content']=$cons;
	 	// $unew['auth']=$auth;
	 	// $unew['publish']=$publish;
	 	// $mysql->where($where);
	 	// $update = $mysql->update('fn_qingtian',$unew);
	 	//$sql="update fn_qingtian set content='".htmlspecialchars($cons)."' ,auth='".$auth."' ,publish='".$publish."' where nid=".$l_id;
	 	// echo $sql;
	 	//$update = $mysql->doSql($sql);
	 	//if($update) logging($l_id.' : 内容已同步.');
 	}

 	protected function newsQuery($kwd=array()){
 		$res = [];
 		$i = 0;
 		// dump($kwd);
 		if(is_array($kwd)){
 			$n = M('nav');
 			$where['url_type']='link';
 			$navs = $n->where($where)->select(); 			
 			foreach ($navs as $key => $nav) {
 				// if($key==1){
 					$newsList = self::newsList($nav['url']);
 					// dump($newsList);
	 				if(is_array($newsList)){
	 					foreach ($newsList as $new_key => $new) {
	 						if(!empty($kwd['title'])){
	 							if(strpos($new['title'], $kwd['title'])===false){

	 							}else{
	 								$new['flag']='title';
	 								$res[] = $new;
	 							}
	 						}

	 						if(!empty($kwd['content'])){
	 							$i++;
	 							$info = self::newsInfo($new['url']);
	 							if(strpos($info['content'], $kwd['content'])>=1 || strpos($info['title'], $kwd['content'])>=1){
	 								$new['flag']='content';
	 								$res[] = $new;
	 							}
	 						}

	 						if(!empty($kwd['mon']) && $new['mon']==$kwd['mon']){
	 							$new['flag']='mon';
	 							$res[] = $new;
	 						}
	 						if(!empty($kwd['date']) && $new['date']==$kwd['date']){
	 							$new['flag']='date';
	 							$res[] = $new;
	 						}
	 					}
	 				}
 				// }
 			}
 		}

 		return $res;
 	}
}
?>