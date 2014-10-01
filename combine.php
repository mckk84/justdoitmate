<?php
if($_GET['type']=='javascript'){
	$file_loc = 'js/';
	$type ='javascript';
} else {
	$file_loc = 'css/';
	$type = 'css';
}
$elements = explode(',', $_GET['webfiles']);
$lastmodified = 0;
while (list(,$element) = each($elements)) {
	$path = $file_loc.$element;
	
	if (file_exists($path))
	{
		$lastmodified = max($lastmodified, filemtime($path));
	}
}
// Send Etag hash
$hash = $lastmodified . '-' . md5($_GET['webfiles']);
header ("Etag: \"" . $hash . "\"");
$offset = 48 * 60 * 60;
$expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
// send the Expire header
header ($expire);   
// cache-control
header ("Cache-Control: public,max-age=864000");
$gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
$deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');
$encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');
// Check for buggy versions of Internet Explorer
if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') && 
		preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
$version = floatval($matches[1]);
if ($version < 6)
	$encoding = 'none';
if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1')) 
	$encoding = 'none';
}
/****************************************************************************
 * @TODO instead of reading all files create a cache file as a combined file
*****************************************************************************/
$contents = '';
reset($elements);
while (list(,$element) = each($elements)) {
	$path = $file_loc.$element;
	if (file_exists($path))
	{
		$contents .= "\n\n" . file_get_contents($path);
	}
}
header ("Content-Type: text/" . $type);
if (isset($encoding) && $encoding != 'none') 
{
	$contents = gzencode($contents, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
	header ("Content-Encoding: " . $encoding);
	header ('Content-Length: ' . strlen($contents));
	echo $contents;
} 
else 
{
	header ('Content-Length: ' . strlen($contents));
	echo $contents;
}