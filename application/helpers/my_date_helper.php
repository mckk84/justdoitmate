<?php
/*
Dade Helper

* convert date according to user timezone offset
* convert db date Y-m-d H:i:s

*/

function getuserdate($date, $timezone)
{
	$db_time = strtotime($date);
	if(empty($db_time)){
		return '';
	}
	$offset = $timezone * 60;
	return date("d.m.Y G:i:s", $db_time + $offset);
}

function getuserdateshow($date, $timezone)
{
	$db_time = strtotime($date);
	if(empty($db_time)){
		return '';
	}
	$offset = $timezone * 60;
	return date("M d Y h:i a", $db_time + $offset);
}

function setuserdate($date, $timezone)
{
	$db_time = strtotime($date);
	if(empty($db_time)){
		return '';
	}
	$offset = $timezone * 60;
	return date("Y-m-d H:i:s", $db_time - $offset);
}

function seconds_to_hms($seconds)
{
	$days = floor($seconds/86400);
	$hrs = floor($seconds/3600);
	$mins = intval(($seconds / 60) % 60); 
	$sec = intval($seconds % 60);

	if($days > 0){
		return $days." Days ";
	}else{
		$hrs = $hrs - ($days * 24);
	}
	
	if($hrs >= 1)
	{
		return $hrs." Hrs ";
	}
	return "";
}


?>