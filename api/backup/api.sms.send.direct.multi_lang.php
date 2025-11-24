<?php
# SMS API (Direct insert to DB) for INTERNAL USE ONLY
# recommend to store this file outside of /var/www/html such as /var/sms
# SendSMSDirect($data, $dbName = "SMS", $appName="<SET YOUR Application Name here>") ex: ISAG, RBT, ... etc

# this page must UTF-8 Encoding and Receive only UTF-8 character

/*
$send_data = Array();
$send_data['sms_client_ip'] = "172.17.10.35"; // Host IP ex: $_SERVER['REMOTE_ADDR']
$send_data['sms_type'] = "submit"; 
$send_data['sms_service_type'] = ""; // Application name (6 character)
$send_data['sms_sender'] = "SBG"; // sender ex: mybyCAT, iTop
$send_data['sms_receiver'] = "66864601062"; // destination ex: 66864601421
$send_data['sms_delivery_report'] = 0; // 0 = no need dr sms; 1 = need dr sms
$send_data['sms_langauge'] = "T";

#$value = '測試短消息測試短消1測試短消息測試短消2測試短消息測試短消3測試短消息測試短消4測試短消息測試短消5測試短消息測試短消6測試短消息測試短消7';
#$value = 'အခ်စ္အတြက္ ကံမေကာင္းတဲ့သူအခ်စ္အတြက္ ကံမေကာင္းတဲ့သူကာင္းတဲ့သူကာင္းတဲ့သူူ ကံမေကာင္းတဲ့သူကာင္း  ကံမေကာင္းတဲ့သူကာင္း';
$value = "ข้อความ'01ข้อความ'02ข้อความ'03ข้อความ'04ข้อความ'05ข้อความ'06ข้อความ'07ข้อความ'08ข้อความ'09ข้อความ'00ข้อความ'11ข้อความ'12ข้อความ'13ข้อความ'14ข้อความ'15ข้อความ'16ข้อความ'17ข้อความ'18ข้อความ'19ข้อความ'20";
#$value = "ຂໍ້​ຄວາມ01ຂໍ້​ຄວາມ02ຂໍ້​ຄວາມ03ຂໍ້​ຄວາມ04ຂໍ້​ຄວາມ05ຂໍ້​ຄວາມ06ຂໍ້​ຄວາມ07ຂໍ້​ຄວາມ08ຂໍ້​ຄວາມ09ຂໍ້​ຄວາມ10ຂໍ້​ຄວາມ11ຂໍ້​ຄວາມ12ຂໍ້​ຄວາມ13ຂໍ້​ຄວາມ14ຂໍ້​ຄວາມ15ຂໍ້​ຄວາມ16ຂໍ້​ຄວາມ17ຂໍ້​ຄວາມ18ຂໍ້​ຄວາມ19ຂໍ້​ຄວາມ20";
#$value = '表示運賃はエコノミークラス「Starter」片道運賃です。ご予約・お支払い方法によって既定の支払手数料または取扱手数料が別途必要です。また、空港使用料等が別途必要です。運賃の払い戻しはできませんが、変更の時点で適用となる変更手数料と運賃の差額を支払うことにより、ご予約の変更が可能です。お手荷物のお預け、座席指定などは別途有料です。手数料一覧はこちら。';

$send_data['sms_message'] = $value; // message in UTF-8
#print_r($send_data);
print_r(SendSMSDirect($send_data));
sleep(99999);
//*/

function SendSMSDirect($data, $dbName = "SMS") {
	$charge_account = str_replace("'", "''", str_replace("''", '"', $data['charge_account']));
	$sms_service_type = str_replace("'", "''", str_replace("''", '"', $data['sms_service_type']));
	$client_ip = $data['sms_client_ip'];
	$command = "send_msg";
	switch ($data['sms_type']) {
		case "submit":
			$command_id = 4;
		break;
		case "deliver":
			$command_id = 5;
		break;
		default:
			$ary['error']="unknown command_id";
			$ary['error_code']="101";
			return $ary;
		break;
	}
	if ($data['sms_sender']) {
		$sms_sender = str_replace("'", "''", str_replace("''", '"', $data['sms_sender']));
	}else{
		$ary['error']="unknown sms_sender";
		$ary['error_code']="102";
		return $ary;
	}
	if ($data['sms_receiver']) {
		$sms_receiver = $data['sms_receiver'];
	}else{
		$ary['error']="unknown sms_receiver";
		$ary['error_code']="103";
		return $ary;
	}
	$priority_flag = 0;
	if ($data['sms_delivery_report']) {
		$registered_delivery = 1;
	}else{
		$registered_delivery = 0;
	}
	$sms_schedule_delivery_time = "NULL";
	if (strlen($data['sms_schedule_delivery_time'])) {
		$time = strtotime($data['sms_schedule_delivery_time']) - time();
		if (($time > 0)&&($time <= 172800)){
			$sms_schedule_delivery_time = "'".date("ymdHis", strtotime($data['sms_schedule_delivery_time']))."028+'"; // 28+ = GMT+7 or UTC+7 (28 = (7hr * 60min)/15min)
		}
	}
	$sms_validity_period = "NULL";
	if (strlen($data['sms_validity_period'])) {
		$time = strtotime($data['sms_validity_period']) - time();
		if ($time < 0) {
			$sms_validity_period = "'000000001500000R'"; // 15 min
		}elseif ($time > 172800){
			$sms_validity_period = "'000002000000000R'"; // 2 day
		}else{
			$sms_validity_period = "'".date("ymdHis", strtotime($data['sms_validity_period']))."028+'";
		}
	}
	if (strlen($data['sms_message'])==0) {
		$ary['error']="empty message";
		$ary['error_code']="104";
		return $ary;
	}
	$sms_message = str_replace("'", "''", str_replace("''", '"', $data['sms_message']));
	$ary_sms_message=false;
	
	switch (strtoupper ($data['sms_langauge'])) {
		case "O":
		case "TH":
		case "T":
			$data_coding = 8;
			$ucs2 = iconv('UTF-8//IGNORE','UCS-2BE//IGNORE', $sms_message);
			if (strlen($ucs2)<=140) { // single message
				$ary_sms_message[]=$sms_message; 
			}else{ // long message
				while(strlen($sms_message)>0) {
					for($i=1; $i<strlen($sms_message); $i++) {
						if (($i >= strlen($sms_message))||($i+1 >= strlen($sms_message))) {
							$ary_sms_message[]=$sms_message;
							$sms_message = "";
							break;
						}
						$ucs2 = iconv('UTF-8//IGNORE','UCS-2BE//IGNORE', substr($sms_message, 0, $i));
						if (strlen($ucs2)>=134){
							$check = iconv('UTF-8', 'UTF-16LE', substr($sms_message, 0, $i)); // force cut if only correct words
							if (!$check) {
								$j = $i;
								while(!$check) { // loop for complete words
									$check = iconv('UTF-8', 'UTF-16LE', substr($sms_message, 0, $j--)); // force cut if only correct words
									if ($check) {
										$ary_sms_message[]=substr($sms_message, 0, $j);
										$sms_message = substr($sms_message, $j);
										break;
									}
								}
								if (!$check) { // still error
									$j = $i;
									while(!$check) { // loop for complete words but ignore error
										$check = iconv('UTF-8//IGNORE', 'UTF-16LE//IGNORE', substr($sms_message, 0, $j--)); // ignore error
										if ($check) {
											$ary_sms_message[]=substr($sms_message, 0, $j);
											$sms_message = substr($sms_message, $j);
											break;
										}
									}
								}
							}else{
								$ary_sms_message[]=substr($sms_message, 0, $i);
								$sms_message = substr($sms_message, $i);
							}
							break;
						}
					}
				}
			}
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
	ini_set('mssql.charset', 'UTF-8');
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
			
			$sql="INSERT INTO [$dbName].[dbo].[SMSGWCE_pend]
				([charge_account],[command_id],[service_type],[source_addr],[destination_addr],[esm_class],[priority_flag],[schedule_delivery_time],[validity_period],[registered_delivery],[data_coding],[short_message],[long_message_id],[current_message],[summary_message],[transaction_id],[command],[request_ip]) VALUES
				( '$charge_account', $command_id, '$sms_service_type', '$sms_sender', '$sms_receiver', $esm_class, $priority_flag, $sms_schedule_delivery_time, $sms_validity_period, $registered_delivery, $data_coding, CONVERT(nvarchar(MAX), 0x".bin2hex(iconv('UTF-8//IGNORE', 'UTF-16LE//IGNORE', $ary_sms_message[$current_message-1]))."), $long_message_id, $current_message, $summary_message, $transaction_id, '$command', '$client_ip')";

			$query = mssql_query(str_replace(", ''",", NULL", $sql));
//			echo ">>$sql<<\n";
//			if (!$query) echo $sql;
			$transaction_ids[] = $transaction_id;
		}
		if (!$query) {
//			echo date("Y-m-d H:i:s")." [$client_ip] sql string {\n".print_r($data,true)."\n}\nerror time \n\n";
			$ary['error']="[".mssql_get_last_message()."]"."\nip=$client_ip\nsql=$sql\ndata=".print_r($data,true);$ary['error_code']="301";
		}else{
			$ary['error_code']="0";
			$ary['error']="";
			$ary['transaction_ids'] = $transaction_ids;
		}
	}
	return $ary;
}
?>