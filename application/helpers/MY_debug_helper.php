<?php
/*
Debug Helper

* log db error to table 'error_log'
* log input error to table 'error_log'

*/

function justlog($user, $subject, $errorno, $error_msg)
{
	$CI =& get_instance();
	$CI->load->database();
	$CI->load->library('session');
	
	$timestamp = time();
	$idata = array( 'user' => $user, 'subject' => $subject, 'errorno' => $errorno, 'error_msg' => $error_msg, 'timestamp' => $timestamp);
	$CI->db->insert('error_log', $idata);
	return true;
}

?>