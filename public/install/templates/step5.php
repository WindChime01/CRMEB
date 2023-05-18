<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo $Title; ?> - <?php echo $Powered; ?></title>
<link rel="stylesheet" href="./css/install.css?v=9.0" />
<script src="js/jquery.js"></script>
<?php
$uri = $_SERVER['REQUEST_URI'];
$root = substr($uri, 0,strpos($uri, "install"));
$admin = $root."../admin/index/index";
?>
</head>
<body>
<div class="wrap">
  <?php require './templates/header.php';?>
  <section class="section">
    <div class="">
      <div class="success_tip cc"> <a href="<?php echo $admin;?>" class="f16 b">安装完成，进入后台管理</a>
          <p>为了您站点的安全，安装完成后即可将网站根目录下“public/install”文件夹中除install.lock外的文件及文件夹全部删除。<p>
      </div>
	        <div class="bottom tac">
	        <a href="<?php echo 'http://'.$host;?>" class="btn">进入前台</a>
	        <a href="<?php echo 'http://'.$host;?>/admin/login/index" class="btn btn_submit J_install_btn">进入后台</a>
      </div>
      <div class=""> </div>
    </div>
  </section>
</div>
<?php require './templates/footer.php';?>
<script>
$(function(){
    var version = '<?php echo trim($curent_version['version']);?>';
    var version_code = '<?php echo trim($curent_version['version_code']);?>';
	$.ajax({
	type: "POST",
	url: "http://shop.crmeb.net/api/web/upgrade",
	data: {host:'<?php echo $host;?>',https:'<?php echo 'http://'.$host;?>',version:version,version_code:version_code,ip:'<?php echo $_SERVER[HTTP_CLIENT_IP];?>'},
	dataType: 'json',
	success: function(){}
	});
});
</script>
</body>
</html>
