<?php

if (!defined("DIR_ROOT"))
{
    define("DIR_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

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
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
    
    curl_close($curl);
    return $output;
}


 //日志记录
 function logging($str=''){
 	if(!empty($str)){
 		// $day = date("Y_m_d");
 		// $filename = DIR_ROOT."log\scan_".$day.".log"; 		
 		// $fp = fopen($filename,"a+");
        echo $str.'<br>';
 		//$str = iconv("utf-8","GB2312",$str);

 		// fwrite($fp, date("Y-m-d H:i:s") .': '. $str."\n");
 		// fclose($fp);
 	}
 }

?>