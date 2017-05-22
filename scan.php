<?php 
require ('Snoopy.class.php');
require ('simple_html_dom.php');
require 'MMysql.cfg.php';
require 'MMysql.php';

if (!defined("DIR_ROOT"))
{
	define("DIR_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

echo '<title>jing scan</title>';
echo '<META HTTP-EQUIV="Refresh" content="360">';

//测试输出
function dump($var, $echo=true, $label=null, $strict=true) {
$label = ($label === null) ? '' : rtrim($label) . ' ';
if (!$strict) {
    if (ini_get('html_errors')) {
        $output = print_r($var, true);
        $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
    } else {
        $output = $label . print_r($var, true);
    }
} else {
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    if (!extension_loaded('xdebug')) {
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
    }
}
if ($echo) {
    echo($output);
    return null;
}else
    return $output;
} 


//调用URL 
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

//snoopy 页面抓取类
function _snoopy(){	
		// $re = https_request($u,null);
		// preg_match_all('/<tr>/',$re,$arr);
		// print_r($arr[0]);
		// dump($arr);
	 $snoopy = new Snoopy;
	 $re = $snoopy->fetch($url); //获取所有内容
	 //dump($snoopy->results); //显示结果
	 //$xml = simplexml_load_string($re);
	 //echo $xml;

	 $html = new simple_html_dom();
	 $html->load_file($url);
	 $trs = $html->find('tbody > tr');
	 dump($trs);

	 //dump(mb_convert_encoding($snoopy->results,"gbk","utf-8")); 

	 //可选以下
	 //dump($snoopy->fetchtext($url)); //获取文本内容（去掉html代码）
	 //dump($snoopy->fetchlinks($url)); //获取链接
	 //dump($snoopy->fetchform($url));  //获取表单
}

 //日志记录
 function logging($str=''){
 	if(!empty($str)){
 		$day = date("Y_m_d");
 		$filename = DIR_ROOT."log\scan_".$day.".log"; 		
 		$fp = fopen($filename,"a+");
 		$str = iconv("utf-8","GB2312",$str);
 		fwrite($fp, date("Y-m-d H:i:s") .': '. $str."\n");
 		fclose($fp);
 	}
 }



//暂停 当天已经达到    即停止任务进行
 function action_stop_dscan($mysql){ 	
 	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}
	//http://member.djjl360.com?controller=task&action=status&status=0&taskid=2585008&version=1&sid=336201&vs=tb2.2.0.4&k=f40f2adb91dc26cfe802a4bfe1f1d7ae&s=GpAA3AVY
	$sql = "select * from task_task_list where list_jid is not null and list_d_scan=0 and list_d_success>=list_d_cnt and list_d_cnt>0";
	$stops = $mysql->doSql($sql);
	foreach ($stops as $key => $stop) {
		if(intval($stop['list_jid'])>0){
			$url  = 'http://member.djjl360.com?controller=task&action=status&status=0&taskid='.intval($stop['list_jid']).'&version=1&sid=336201&vs=tb2.2.0.4&k=f40f2adb91dc26cfe802a4bfe1f1d7ae&s=GpAA3AVY';
			logging($url);
 			logging('客户端任务停止：'.$stop['list_jid']);
 			$re = https_request($url); 	
 			logging('操作结果 ：'.$re);	

 			if($re['success']){
 				//更新成功后扫描控制
				$sql=" update task_task_list set list_d_scan=1 where list_d_scan=0 and list_jid=".intval($stop['list_jid']);
				logging('day-停止扫描 - '.$sql);	
				$mysql->doSql($sql);
 			}
		}
	}
 }

 //暂停  总量已经完成 即停止任务进行
 function action_stop_scan($mysql){ 	
 	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}
	//http://member.djjl360.com?controller=task&action=status&status=0&taskid=2585008&version=1&sid=336201&vs=tb2.2.0.4&k=f40f2adb91dc26cfe802a4bfe1f1d7ae&s=GpAA3AVY
	$sql = "select * from task_task_list where list_jid is not null and list_scan=0 and list_success>=list_cnt and list_cnt>0";
	$stops = $mysql->doSql($sql);
	foreach ($stops as $key => $stop) {
		if(intval($stop['list_jid'])>0){
			$url  = 'http://member.djjl360.com?controller=task&action=status&status=0&taskid='.intval($stop['list_jid']).'&version=1&sid=336201&vs=tb2.2.0.4&k=f40f2adb91dc26cfe802a4bfe1f1d7ae&s=GpAA3AVY';
			logging($url);
 			logging('客户端任务停止：'.$stop['list_jid']);
 			$re = https_request($url); 	
 			logging('操作结果 ：'.$re);	

 			if($re['success']){
 				//更新成功后扫描控制
				$sql=" update task_task_list set list_scan=1 , list_d_scan=1 where list_scan=0 and  list_jid=".intval($stop['list_jid']);
				logging('停止扫描 - '.$sql);	
				$mysql->doSql($sql);
 			}
		}
	}
 }
	
//开启 当天未达到 并且 总量未完成 即开启任务继续
 function action_start_scan($mysql){
 	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}

 	//开启
	//http://member.djjl360.com?controller=task&action=status&status=1&taskid=2585008&version=1&sid=336201&vs=tb2.2.0.4&k=f40f2adb91dc26cfe802a4bfe1f1d7ae&s=GpAA3AVY
	$sql = "select * from task_task_list where list_jid is not null and list_d_scan=1 and list_scan=0 and list_cnt>0 and list_success<list_cnt";
	$stops = $mysql->doSql($sql);
	foreach ($stops as $key => $stop) {
		if(intval($stop['list_jid'])>0){
			$url  = 'http://member.djjl360.com?controller=task&action=status&status=1&taskid='.intval($stop['list_jid']).'&version=1&sid=336201&vs=tb2.2.0.4&k=f40f2adb91dc26cfe802a4bfe1f1d7ae&s=GpAA3AVY';
			logging($url);
 			logging('客户端任务开启：'.$stop['list_jid']);
 			$re = https_request($url); 	
 			logging('操作结果 ：'.$re);	

 			if($re['success']){
 				//更新成功后扫描控制
				$sql=" update task_task_list set list_scan=0 ,list_d_scan=0 where list_d_scan=1 and list_jid=".intval($stop['list_jid']);
				logging('开启扫描 - '.$sql);	
				$mysql->doSql($sql);
 			}
		}
	}
 }


//扫描任务
 function action_scan($mysql){

 	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}

 	//检索需要扫描的任务
	$sql = "select * from task_task_list where list_jid is not null and (list_success<list_cnt or list_scan=0)";
	$jings = $mysql->doSql($sql);
	//$mysql->dump($jings);

	foreach ($jings as $key => $jing) {
		if(intval($jing['list_jid'])>0){
		$url  = 'http://member.djjl360.com/?sid=336201&vs=tb2.2.0.4&k=23613166671587deb924433996b8a46c&s=K0fbZYA0&&controller=task&action=info&id='.$jing['list_jid'];
		echo date("Y-m-d H:i:s").' : '.iconv("utf-8","GB2312",'扫描任务: ').$jing['list_jid'].'<br>';
		logging('扫描任务:'.$jing['list_jid']);
		//新建一个Dom实例
		$html = file_get_html($url);
		$arr = array();
		//echo $html;
		$h  =  '<table border=1>';
		$i=0;
		foreach($html->find('tr') as $tr){
			$h.= '<tr>';
			$j=0;
			foreach($tr->find('td') as $td){			
				$arr[$i][$j]=mb_convert_encoding($td->innertext,"gbk","utf-8");
				$h.= '<td>'.mb_convert_encoding($td->innertext,"gbk","utf-8").'</td>';
				$j++;
			}
			$h.= '</tr>';
			$i++;
		}
		$h.= '</table>';
		 

		//mysql_query("set character set gbk");
		if(is_array($arr) && !empty($arr)){		
			$mysql->doSql("set character set gbk"); //设置这个插入中文就不会报 data to long
			foreach ($arr as $tr => $tds) {	

				if(!empty($tds[8])){
				$data = array(
					     'jing_id'=>intval($tds[0]),
					     'jing_tid'=>intval($tds[1]),
					     'jing_uid'=>intval($tds[2]),
					     'jing_jf'=>intval($tds[3]),
					     'jing_date'=>$tds[4],
					     'jing_des'=>$tds[5],
					     'jing_area'=>$tds[6],
					     'jing_status'=>$tds[7],
					     'jing_comp'=>$tds[8],
					     'jing_error'=>$tds[9],
					     'jing_action'=>$tds[10]
					     );
				    //dump($data);

					try{
						$mysql->insert('task_jing_scan',$data);						
						$sql = $mysql->getLastSql();
						//echo $sql.'<br>';
						logging($sql);
					}catch(Exception $e){
						//重复数据根据主键 jing_id 约束不重复插入
						//echo $e->getMessage().'<br>';
					}
				}		 	
			}
		}
	}
	}
 }

//更新昨日日刷点
function action_update_zdcnt($mysql){
	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}
	//更新订单成功的数据量 list_success 
	$sql = "UPDATE task_task_list AS  a SET list_z_success = ";
	$sql.=" (SELECT cnt FROM (SELECT jing_tid,COUNT(jing_id) AS cnt FROM task_jing_scan WHERE LENGTH(jing_error)=0 and LEFT(jing_comp,10)='".date("Y-m-d",strtotime("-1 day"))."'  GROUP BY jing_tid) AS b";
	$sql.=" WHERE a.list_jid=b.jing_tid) ";
	//$sql.=" where list_d_scan=0";
	logging('zday-更新成功数 - '.$sql);	
	$mysql->doSql($sql);
	
	//更新订单成功的数据量 list_success 
	$sql=" UPDATE task_task_list AS  a SET list_z_fail = ";
	$sql.=" (SELECT cnt FROM (SELECT jing_tid,COUNT(jing_id) AS cnt FROM task_jing_scan WHERE LENGTH(jing_error)>0 and LEFT(jing_comp,10)='".date("Y-m-d",strtotime("-1 day"))."'  GROUP BY jing_tid) AS b";
	$sql.=" WHERE a.list_jid=b.jing_tid)";
	//$sql.=" where list_d_scan=0";
	logging('zday-更新失败数 - '.$sql);	
	$mysql->doSql($sql);

	logging('数据扫描中..');
}

//更新日刷点
function action_update_dcnt($mysql){
	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}
	//更新订单成功的数据量 list_success 
	$sql = "UPDATE task_task_list AS  a SET list_d_success = ";
	$sql.=" (SELECT cnt FROM (SELECT jing_tid,COUNT(jing_id) AS cnt FROM task_jing_scan WHERE LENGTH(jing_error)=0 and LEFT(jing_comp,10)='".date('Y-m-d',time())."'  GROUP BY jing_tid) AS b";
	$sql.=" WHERE a.list_jid=b.jing_tid) ";
	//$sql.=" where list_d_scan=0";
	logging('day-更新成功数 - '.$sql);	
	$mysql->doSql($sql);
	
	//更新订单成功的数据量 list_success 
	$sql=" UPDATE task_task_list AS  a SET list_d_fail = ";
	$sql.=" (SELECT cnt FROM (SELECT jing_tid,COUNT(jing_id) AS cnt FROM task_jing_scan WHERE LENGTH(jing_error)>0 and LEFT(jing_comp,10)='".date('Y-m-d',time())."'  GROUP BY jing_tid) AS b";
	$sql.=" WHERE a.list_jid=b.jing_tid)";
	//$sql.=" where list_d_scan=0";
	logging('day-更新失败数 - '.$sql);	
	$mysql->doSql($sql);

	logging('数据扫描中..');
}


//更新总刷点
function action_update_cnt($mysql){
	if(empty($mysql)){
 		$mysql = new MMysql($MMysqlConfig);
 	}
	//更新订单成功的数据量 list_success 
	$sql = "UPDATE task_task_list AS  a SET list_success = ";
	$sql.=" (SELECT cnt FROM (SELECT jing_tid,COUNT(jing_id) AS cnt FROM task_jing_scan WHERE LENGTH(jing_error)=0  GROUP BY jing_tid) AS b";
	$sql.=" WHERE a.list_jid=b.jing_tid)";
	//$sql.="  where list_scan=0";
	logging('更新成功数 - '.$sql);	
	$mysql->doSql($sql);
	
	//更新订单成功的数据量 list_success 
	$sql=" UPDATE task_task_list AS  a SET list_fail = ";
	$sql.=" (SELECT cnt FROM (SELECT jing_tid,COUNT(jing_id) AS cnt FROM task_jing_scan WHERE LENGTH(jing_error)>0  GROUP BY jing_tid) AS b";
	$sql.=" WHERE a.list_jid=b.jing_tid)";
	//$sql.="  where list_scan=0";
	logging('更新失败数 - '.$sql);	
	$mysql->doSql($sql);

	logging('数据扫描中..');
}


$mysql = new MMysql($MMysqlConfig);


//开启当天未完成
action_start_scan($mysql);

//当天量完成停止
action_stop_dscan($mysql);
	
//总量完成停止
action_stop_scan($mysql);

//扫描
action_scan($mysql);

//更新刷点
action_update_cnt($mysql);

//更新日刷点
action_update_dcnt($mysql);

//更新昨日刷点
action_update_zdcnt($mysql);
 
?>