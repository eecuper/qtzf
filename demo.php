<?php 

require ('Snoopy.class.php');
include_once("simple_html_dom.php");
require 'MMysql.cfg.php';
require 'MMysql.php';
require 'util.php';


header("Content-type: text/html; charset=utf-8"); 
 
$query= "http://60.190.126.3:8086/was40/search?templet=ls_demo_result.jsp&searchdate=&searchword=&presearchword=&channelid=&dengyu1=%3D&title=&relationtype=and&dengyu2=%3D&content=&DateInputType=2&dc1=&dc2=&start_year=&start_month=&start_day=&end_year=&end_month=&end_day=&selectDate=0&sortfield=LIFO&prepage=20&sub1=%E6%A3%80%E7%B4%A2";
 
// $re = https_request($u,null);
// preg_match_all('/<tr>/',$re,$arr);
// print_r($arr[0]);
// dump($arr);


// echo '<br>******************************* curl 示例******************************<br>';
// $html = https_request($query);  
// dump($html);

// echo '<br>******************************* snoopy 示例******************************<br>';
// $snoopy = new Snoopy;	
// if($snoopy->fetch($adrs[1]))  
// {  
  
//   dump($snoopy->results);
// }  
// else  
//  echo "error fetching document: ".$snoopy->error."/n";  
 

// echo '<br>******************************* simple_html_dom 示例******************************<br>';
// // 新建一个Dom实例
// $html = new simple_html_dom();
 
// // 从url中加载
// $html->load_file($adrs[1]);
 
// // 从字符串中加载
// //$html->load('<html><body>从字符串中加载html文档演示</body></html>');
 
// //从文件中加载
// //$html->load_file('path/file/test.html');
// $main = $html->find('div[id=TRS]',0);
// dump($main);


$local = 'http://www.qtzfcg.gov.cn/';

//中心动态
$adrs[]="http://www.qtzfcg.gov.cn/zxdt/";

// //本周开标
// $adrs[]="http://www.qtzfcg.gov.cn/zxdt/bzkb/";

// //征求意见
// $adrs[]="http://www.qtzfcg.gov.cn/zfcg/zqyj/";

// ///采购动态
// $adrs[]="http://www.qtzfcg.gov.cn/zfcg/cgdt/";

// //招标公告
// $adrs[]="http://www.qtzfcg.gov.cn/zfcg/ggpt/zbgg/";

// //中标公示
// $adrs[]="http://www.qtzfcg.gov.cn/zfcg/ggpt/zbgs/";

// //中标公告
// $adrs[]="http://www.qtzfcg.gov.cn/zfcg/ggpt/zb/";

// //废标公告
// $adrs[]="http://www.qtzfcg.gov.cn/zfcg/ggpt/fbgg/";
 
 echo '<br>******************************* file_get_html 示例******************************<br>';

if(empty($mysql)){
	$mysql = new MMysql($MMysqlConfig);
}

$k  = 1;

foreach ($adrs as $key => $adr) {

	// $adr = $adrs[0];
	logging(' : 第 '.$k.' 次抽取开始...');
	logging(' : '.$adr);
	//新建一个Dom实例
	$html = file_get_html($adr);

	$main = $html->find('div[id=TRS] tr');

	foreach ($main as $row => $tr) {
		if($row>=0){
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

			// logging('<a href="'.$href.'">'.$text.'</a>'. ' : '.$date.' : '.$link . '  月份'.$l_mon . ' 日期 '.$l_date.' ID '.$l_id.'<br>');
			logging('月份'.$l_mon . ' 日期 '.$l_date.' ID '.$l_id.'<br>');

			//判断是否已经存在新闻
			$sql = "select * from fn_qingtian where nid=".$l_id;
			$news = $mysql->doSql($sql);

			//已经存在
			if(count($news)>0){
				logging($l_id.' : 数据已经存在.');
			}else{
				$new['nid']=$l_id;
				$new['type']=str_replace($local,'',$adr);
				$new['title']=$text;
				$new['mon']=$l_mon;
				$new['date']=$l_date;
				$new['url']=$link;
				$new['stime']=time();
				$new['status']=1;
				$insert = $mysql->insert('fn_qingtian',$new);

				//如果新增成功 , 则获取具体新闻内容然后更新
				if($insert){
					logging($l_id.' : 数据抽取成功.');
					//$href= 'http://www.qtzfcg.gov.cn/zxdt/201608/t20160812_299726.htm';
					$html2 = file_get_html($href);
					$title = $html2->find(".font_bt",0)->plaintext;
					$publish=$html2->find("#property",0)->plaintext;
					$auth  = $html2->find(".content",0)->find(".font_12_24",2)->plaintext;
					$cons   = $html2->find(".font_text",0);					
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
				 	$sql="update fn_qingtian set content='".htmlspecialchars($cons)."' ,auth='".$auth."' ,publish='".$publish."' where nid=".$l_id;
				 	// echo $sql;
				 	$update = $mysql->doSql($sql);
				 	if($update) logging($l_id.' : 内容已同步.');
				 	// echo $cons; die;
				}
			}  	
		}
	}
  
	$html->clear();
	logging(' : 第 '.$k.' 次抽取结束...');

	$k++;
}

?>

