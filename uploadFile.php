<?php

	$id = $_GET['id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>文件选择</title>
</head>
<body>

网络文件
<input type="text" id="wl"/>
<input type="button" value="确定" onclick="checkWl();"/>
<br>
本地文件
<form action="upload.php?pid=<?php echo $id ?>" method="post" enctype="multipart/form-data">
<input type="file" name="file" id="file"/>
<input type="submit" value="上传"/>
</form>

<script type="text/javascript">
function checkWl(){
	var wl = document.getElementById('wl').value;
	wl = wl.replace(/(^\s*)|(\s*$)/g, "");
	if(wl==''){
		alert('不允许为空');
		return;
	}else{
		window.opener.document.getElementById('<?php echo $id?>').value=wl;
		//window.opener.document.getElementById('img<?php echo $id?>').src=wl;
	}
}
</script>

</body>
</html>