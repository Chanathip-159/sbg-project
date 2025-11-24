<?php
error_reporting(0); // close error
ini_set("memory_limit", "1024M");
require_once("bulk.api.engine.php");
$send_result=sendSMSEngine($_POST['user'], md5($_POST['pass']), $_POST['from'], $_POST['target'], $_POST['mess'], $_POST['lang'], $_POST['expire'], $_POST['scheduled']);
if (strlen($send_result['error_code'])&&$send_result['error_code']=="0") {
	$xml_result="[".implode(",",@$send_result['transaction_ids'])."] server receive data";
}else{
	if (isset($send_result['error'])&&strlen($send_result['error'])) {
		$xml_result=$send_result['error'];
	}else{
		if (isset($send_result['transaction_ids'])&&count($send_result['transaction_ids'])) {
			$xml_trans="[".implode(",",@$send_result['transaction_ids'])."]";
		}else{
			$xml_trans="";
		}
		if (isset($send_result['fails'])&&count($send_result['fails'])) {
			$xml_fails=" ".implode(",",$send_result['fails']);
		}else{
			$xml_fails="";
		}
		$xml_result=$xml_trans." server receive data".$xml_fails;
	}
}
echo $xml_result;
?>
