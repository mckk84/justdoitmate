<?php 
// verify the key

$time = date("Y-m-d H:i:s");
$home = "http://www.justdoitmate.in";
$title = "Justdoitmate.in";

if( isset($_GET['key']) && $_GET['key'] != ''){
	$key = $_GET['key'];
}else{
	http_redirect($home, array(), false, HTTP_REDIRECT);
}

define('BASEPATH', './');

include('application/config/database.php');

$link = mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db($db['default']['database']);

$query = "UPDATE `tbl_userinfo` SET `validated`=1 WHERE `key`='".trim($key)."'";
$res = mysql_query($query);
if(mysql_affected_rows() != 1)
{
	echo "<b>Email Validation Failed.</b><br/>";
}
else{
	echo "<b>Email Validation Successful.</b><br/>";
}
mysql_close($link);
header( "refresh:3;url=".$home."" ); 
echo '<p>You\'ll be redirected in about 3 secs. If not, click <a href="'.$home.'"><b>'.$title.'</b></a>.</p>'; 
exit;
?>