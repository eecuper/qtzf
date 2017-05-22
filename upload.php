<?php
header('content-type: text/html; charset=utf-8');

error_reporting(0);
//隐藏警告信息
error_reporting(E_ALL^E_NOTICE);

$pid = $_GET['pid'];

//echo $pid;

$allowedExts = array("gif", "jpeg", "jpg", "png","text","zip","rar","xls","mp3");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

//var_dump($_FILES);
 
//var_dump($allowedExts);

$result=array('flag'=>true,'msg'=>'','fileName'=>'');

if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "audio/mpeg")
|| ($_FILES["file"]["type"] == "text/plain")
|| ($_FILES["file"]["type"] == "application/msword")
|| ($_FILES["file"]["type"] == "application/vnd.ms-excel")
|| ($_FILES["file"]["type"] == "application/octet-stream"))
&& ($_FILES["file"]["size"] < 800000)
&& in_array($extension, $allowedExts))
{
	if ($_FILES["file"]["error"] > 0)
	{
		echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	}
	else
	{
	list($rname,$ext) = explode('.',  $_FILES["file"]["name"] );
	$signName = time().'.'.$ext;
	
	//echo "Upload: " . $_FILES["file"]["name"] . "<br>";
	//echo "Type: " . $_FILES["file"]["type"] . "<br>";
	//echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
	//echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
	
	//echo "exit file: " . file_exists("upload1/" . $_FILES["file"]["name"]) . "<br>";
	
	if (file_exists("Uploads/Picture/" . $signName))
	{
	//echo $signName . " already exists. ";
	}
	else
	{
	move_uploaded_file($_FILES["file"]["tmp_name"],"Uploads/Picture/" .$signName);
	//echo "Stored in: " . "Uploads/Picture/" . $signName;
	$result['fileName']="Uploads/Picture/" . $signName;
	}
	}
	echo "<script type='text/javascript'>";
	echo "window.opener.document.getElementById('".$pid."').value='".$result['fileName']."';";
	//echo "window.opener.document.getElementById('img".$pid."').src='".$result['fileName']."';";
	echo "this.close();";
	echo "</script>";
	 
}
else
{
	echo '上传错误';
 
}
?>