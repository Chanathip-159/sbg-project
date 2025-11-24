<?php
error_reporting(0); // close error
ini_set("memory_limit", "1024M");
# this page must ANSI Encoding and Receive only ANSI
/*
$send_data['sms_client_ip'] = $_SERVER['REMOTE_ADDR'];
$send_data['charge_account'] = "0864601062";
$send_data['sms_type'] = "submit";
$send_data['sms_service_type'] = "BUK";
$send_data['sms_sender'] = "test";
$send_data['sms_receiver'] = "66864601062";
$send_data['sms_delivery_report'] = 1;
$send_data['sms_langauge'] = "T";
$send_data['sms_validity_period'] = NULL;
$send_data['sms_schedule_delivery_time'] = NULL;
$send_data['sms_message'] = "ทดสอบ SMS";
$rem = SendSMSDirect141($send_data);
print_r($rem);
	#$rem['error_code'] // error code
	#$rem['error'] // detail of error
	#$rem['transaction_ids'] // Array of transaction_id
		
//*/
function SendSMSDirect141($data, $dbName = "SMS") {
	if (strlen($data['charge_account'])) $charge_account = "'".$data['charge_account']."'"; else $charge_account = "NULL";
	$sms_service_type = str_replace("'", "''", str_replace("''", '"', $data['sms_service_type']));
	$client_ip = $data["sms_client_ip"];
	$command = "send_msg";
	switch ($data["sms_type"]) {
		case "submit":
		$command_id = 4;
		break;
		case "deliver":
		$command_id = 5;
		break;
		default:
		$ary['error']="unknown command_id";
		$ary['error_code']="101";
		$data = false;
		break;
	}
	if ($data["sms_sender"]) {
		$sms_sender = $data["sms_sender"];
	}else{
		$ary['error']="unknown sms_sender";
		$ary['error_code']="102";
		$data = false;
	}
	if ($data["sms_receiver"]) {
		$sms_receiver = $data["sms_receiver"];
	}else{
		$ary['error']="unknown sms_receiver";
		$ary['error_code']="103";
		$data = false;
	}
	$priority_flag = 0;
	if ($data["sms_delivery_report"]) {
		$registered_delivery = 1;
	}else{
		$registered_delivery = 0;
	}
	$sms_schedule_delivery_time = "NULL";
	if (strlen($data["sms_schedule_delivery_time"])) {
		$time = strtotime($data["sms_schedule_delivery_time"]) - time();
		if (($time > 0)&&($time <= 172800)){
			$sms_schedule_delivery_time = "'".date("ymdHis", strtotime($data["sms_schedule_delivery_time"]))."028+'"; // 28+ = GMT+7 or UTC+7 (28 = (7hr * 60min)/15min)
		}
	}
	$sms_validity_period = "NULL";
	if (strlen($data["sms_validity_period"])) {
		$time = strtotime($data["sms_validity_period"]) - time();
		if ($time < 0) {
			$sms_validity_period = "'000000001500000R'"; // 15 min
		}elseif ($time > 172800){
			$sms_validity_period = "'000002000000000R'"; // 2 day
		}else{
			$sms_validity_period = "'".date("ymdHis", strtotime($data["sms_validity_period"]))."028+'";
		}
	}
	$sms_message = $data["sms_message"];
	$ary_sms_message=false;
	switch (strtoupper($data["sms_langauge"])) {
		case "F":
		$data_coding = 16;
		if (strlen($sms_message)>70) $ary_sms_message = str_split($sms_message, 67); else $ary_sms_message[0] = $sms_message;
		break;
		case "TH":
		case "T":
		$data_coding = 8;
		if (strlen($sms_message)>70) $ary_sms_message = str_split($sms_message, 67); else $ary_sms_message[0] = $sms_message;
#		sleep(99999);
		break;
		case "L":
		$data_coding = 3;
		if (strlen($sms_message)>140) $ary_sms_message = str_split($sms_message, 134); else $ary_sms_message[0] = $sms_message;
		break;
		case "EN":
		case "E":
		$data_coding = 2;
		if (strlen($sms_message)>140) $ary_sms_message = str_split($sms_message, 134); else $ary_sms_message[0] = $sms_message;
		break;
		case "B":
		$data_coding = 0;
		if (strlen($sms_message)>160) $ary_sms_message = str_split($sms_message, 152); else $ary_sms_message[0] = $sms_message;
		break;
		default:
		$ary['error']="unknown data_coding";
		$ary['error_code']="105";
		$data = false;
		break;
	}
	if (!mssql_connect("10.100.143.100", "sa", "V@5my")) {
		$ary['error']="Database disconnect";
		$ary['error_code'] = "300";
		$data = false;
	}
	if ($data) {
		$transaction_ids = Array();
		$summary_message = count($ary_sms_message);
		if ($summary_message>1) {
			$long_message_id = rand(1,255);
			$esm_class = 64;
		}else{
			$long_message_id = 0;
			$esm_class = 0;
		}
		for ($current_message=1; $current_message<=$summary_message; $current_message++) {
			
			$microsec = microtime()*1000000;
			while(strlen($microsec) < 6) $microsec = "0".$microsec;
			$transaction_id = date("ymdHis").$microsec;
			while (strlen($transaction_id)<24) $transaction_id .= rand(0,9);
			
			$sql="INSERT INTO [$dbName].[dbo].[SBG_TEST_pend]
				([charge_account],[command_id],[service_type],[source_addr],[destination_addr],[esm_class],[priority_flag],[schedule_delivery_time],[validity_period],[registered_delivery],[data_coding],[short_message],[long_message_id],[current_message],[summary_message],[transaction_id],[command],[request_ip]) VALUES
				( $charge_account, $command_id, '$sms_service_type', '$sms_sender', '$sms_receiver', $esm_class, $priority_flag, $sms_schedule_delivery_time, $sms_validity_period, $registered_delivery, $data_coding, '".$ary_sms_message[$current_message-1]."', $long_message_id, $current_message, $summary_message, $transaction_id, '$command', '$client_ip')";
			$query = mssql_query(str_replace(", ''",", NULL", $sql));
//			if (!$query) echo $sql;
			$transaction_ids[] = $transaction_id;
		}
		if (!$query) {
//			echo date("Y-m-d H:i:s")." [$client_ip] sql string {\n".print_r($data,true)."\n}\nerror time \n\n";
			$ary['error']="[".mssql_get_last_message()."]"."\nip=$client_ip\nsql=$sql\ndata=".print_r($data,true);$ary['error_code']="301";
		}else{
			$ary['error_code']="0";
			$ary['error']="";
			$ary["transaction_ids"] = $transaction_ids;
		}
	}
	return $ary;
}
?>
