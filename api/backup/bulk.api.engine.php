<?php
error_reporting(0); // close error
ini_set("memory_limit", "1024M");
include_once("bulk.function.global.php");
include_once("api.sms.send.direct.php");												######
include_once("api.sms.send.direct.141.php");
include_once("config.php");

define('__db_host__',"10.100.143.100");
define('__db_user__',"sa");
define('__db_pass__',"V@5my");
define('__db_name__',"VAS");

define('__service_name__',"BUK");

define('__pause__',2); // sec
define('__max_thread__',5); // thread

//
function writeLog($transaction, $user, $from, $target, $mess, $lang, $service_type, $error, $return, $status = null) {
	if (!$status) $status = $return;
	file_put_contents (setLogFile()
		, date("Y-m-d H:i:s")."|$transaction|$user|$from|$target|$mess|$lang|$service_type|$error|$return|$status\r\n"
		, FILE_APPEND | LOCK_EX
	);
}
function writeCdr($transaction, $service_type, $user, $from, $target) {
	file_put_contents (setCdrFile()
		, date("Y-m-d H:i:s")."|$transaction|$service_type|$user|$from|$target\r\n"
		, FILE_APPEND | LOCK_EX
	);
}

function sendSMSEngine($user, $hpass, $from, $target, $mess, $lang, $expire=null, $scheduled=null) {
	$year_month = date("Ym");
	$_from = mssql_escape(iconv("UTF-8","TIS-620",$from));					######
	$_mess = mssql_escape(iconv("UTF-8","TIS-620",$mess));				######
	$nor_mess = norMessToLog($_mess);
	if ((strlen($user)==0)||(strlen($hpass)==0)) {
		$returnToUser = "invalid user or password.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "1", $returnToUser, "empty user or password");
		sleep(__pause__);
		return $returnToUser;
	}
	if (strlen($from)==0) {
		$returnToUser = "empty from.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "2", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	if (!checkSender($from)) {
		$returnToUser = "[$from] not allow.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "3", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	if (strlen($mess)==0) {
		$returnToUser = "empty message.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "4", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	if (strlen($lang)==0) {
		$returnToUser = "empty lang.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "5", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	$list_target = explode(",",$target);
	if (count($list_target)>1000) {
		$returnToUser = "over receiver.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "20", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	// connect db
	if (!mssql_select_db(__db_name__, mssql_connect(__db_host__,__db_user__,__db_pass__))) {
		$returnToUser = "service is offline.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, "", "7", $returnToUser, "can not connect db.");
		sleep(__pause__);
		return $returnToUser;
	}
	
	$inter_flag=false;
	$file_inter_list = file_get_contents('./inter_account_list.txt', true);
	$file_inter_list = explode("\n", $file_inter_list);
	foreach ($file_inter_list as $account ) {	
		if (trim($user) == trim($account)) {
			$inter_flag=true;
			break;
		}
	}
	if (!$inter_flag) {
		for ($i=0;$i<count($list_target);$i++) {
			$t = conv2MSISDN($list_target[$i]); 
			if (!$t) {
				$returnToUser = "invalid msisdn format [".$list_target[$i]."].";
				writeLog("", $user, $from, $target, $nor_mess, $lang, "", "6", $returnToUser);
				sleep(__pause__);
				return $returnToUser;
			}else{
				$list_target[$i] = $t;
			}
		}
	}
	/*
	$sql["sql"] = "SELECT count(*) FROM [sms].[dbo].[sms_sender] WHERE [bulk_user]='$user'";
	$result = SendTCPQUERY($sql);
	if ($result[0][0] >0) {
		$sql["sql"] = "SELECT count(*)FROM [sms].[dbo].[sms_sender] WHERE [bulk_user]='$user' AND [sender_name] ='$str'";
		$result = SendTCPQUERY($sql);
		return ($result[0][0] >0)?  true: false;
	} else {
		return true;
	}//*/
	
	$sql = "SELECT [Customer_ID]
		,[Customer_ServiceType]
		,[Customer_RegisDate]
		,[Customer_ExpireDate]
		,[Customer_PromotionMonth]
		,[Customer_PromotionAccount]
		,[Customer_AllCurrentUsage]
		,[Customer_Connected]
	FROM [VAS].[dbo].[SBG_Customers]
	WHERE [Customer_Account] = '$user' AND [Customer_Password] = '$hpass';";
	$mssql = mssql_query($sql);
	$account = mssql_fetch_array($mssql);
	//$serviceType = $account['Customer_ServiceType']
	$serviceType = "BUK";
	if (!$account['Customer_ID']) {
		$returnToUser = "invalid username or password.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "8", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	// regist date
	if (strtotime($account['Customer_RegisDate']) > time()) {
		$returnToUser = "account idle.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "9", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	// expired date
	if (strtotime($account['Customer_ExpireDate']) < time()) {
		$returnToUser = "account expired.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "10", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	// limit sms from account
	if (($account['Customer_AllCurrentUsage'] >= $account['Customer_PromotionAccount'])
		&&($account['Customer_PromotionAccount'] != "-1")) {
		$returnToUser = "account out of credit (a).";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "11", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	$sql = "SELECT [ReportUsage_ID]
		,[ReportUsage_Amount]
		,[ReportUsage_MonthQuota]
	FROM [VAS].[dbo].[SBG_ReportUsage]
	WHERE [Customer_ID] = '".$account['Customer_ID']."' AND [ReportUsage_Month] = '$year_month';";
	$mssql = mssql_query($sql);
	$report = mssql_fetch_array($mssql);
	if (!$report['ReportUsage_ID']) {
		$sql = "INSERT INTO [VAS].[dbo].[SBG_ReportUsage]
		([ReportUsage_Month]
		,[ReportUsage_Amount]
		,[ReportUsage_MonthQuota]
		,[Customer_ID]
		)VALUES(
		'$year_month'
		,'0'
		,'".$account['Customer_PromotionMonth']."'
		,'".$account['Customer_ID']."'
		);";
		if (!mssql_query($sql)) {
			$returnToUser = "service offline";
			writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "12", $returnToUser, "can not insert counter (m).");
			sleep(__pause__);
			return $returnToUser;
		} else {
			$sql = "SELECT [ReportUsage_ID]
				,[ReportUsage_Amount]
				,[ReportUsage_MonthQuota]
			FROM [VAS].[dbo].[SBG_ReportUsage]
			WHERE [Customer_ID] = '".$account['Customer_ID']."' AND [ReportUsage_Month] = '$year_month';";
			$mssql = mssql_query($sql);
			$report = mssql_fetch_array($mssql);
		}
	}
	// limit sms from month
	if (($report['ReportUsage_Amount'] >= $report['ReportUsage_MonthQuota'])
		&&($report['ReportUsage_MonthQuota'] != "-1")) {
		$returnToUser = "account out of credit (m).";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "13", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	// check message
	if (strlen($_mess)== 0) {
		$returnToUser = "empty message.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "14", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	// check thread
	if ($account['Customer_Connected'] >= __max_thread__ ) {
		$returnToUser = "out of thread.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "15", $returnToUser);
		sleep(__pause__);
		$sql = "UPDATE [VAS].[dbo].[SBG_Customers] SET [Customer_Connected] = [Customer_Connected] - 1 WHERE [Customer_ID] = '".$account['Customer_ID']."';";
		mssql_query($sql);
		return $returnToUser;
	}else{
		$sql = "UPDATE [VAS].[dbo].[SBG_Customers] SET [Customer_Connected] = [Customer_Connected] + 1 WHERE [Customer_ID] = '".$account['Customer_ID']."';";
		if (!mssql_query($sql)) {
			$returnToUser = "can not update thread counter.";
			writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "16", $returnToUser);
			sleep(__pause__);
			return $returnToUser;
		}
	}
	$send_fail = "";$sms_return_id_all = ""; $success_counter = 0;$starttime = time();
	$list_limit = count($list_target);
	$sqlInsertTran = "INSERT INTO [VAS].[dbo].[SBG_Transactions]([Transaction_ID],[Transaction_CustomerAccount],[Transaction_CustomerIP],[Transaction_Sender]
,[Transaction_Destination],[Transaction_Message],[Transaction_Langage],[Transaction_DRFlag])VALUES";
	for ($i=0;$i<$list_limit;$i++) {
		//$rem = SendTCPSMS("bulk:".$user."|$from|".$list_target[$i]."|$_mess|".$lang."|0|$serviceType|");
		$send_data = Array();
		$send_data['sms_client_ip'] = $_SERVER['REMOTE_ADDR'];
		$send_data['charge_account'] = $user;
		$send_data['sms_type'] = "submit";
		$send_data['sms_service_type'] = __service_name__;
		$send_data['sms_sender'] = $_from;
		$send_data['sms_receiver'] = $list_target[$i];
		$send_data['sms_delivery_report'] = 1;
		$send_data['sms_langauge'] = $lang;
		$send_data['sms_validity_period'] = $expire;
		$send_data['sms_schedule_delivery_time'] = $scheduled;
		$send_data['sms_message'] = $_mess;
		if ($user == "sila_enc") {
			$rem = SendSMSDirect141($send_data);
		}else{
			$rem = SendSMSDirect($send_data);
		}
		if ($rem['error_code'] == "0") {
			#$success_counter++;
			$success_counter += count($rem["transaction_ids"]);
			$transaction_ids = implode(":", $rem["transaction_ids"]);
			if ($i==($list_limit-1)) {
				$sms_return_id_all .= $transaction_ids; 
			}else{
				$sms_return_id_all .= $transaction_ids.",";
			}
			writeCdr($transaction_ids, $serviceType, $user, $from, $list_target[$i]);
			writeLog($transaction_ids, $user, $from, $list_target[$i], $nor_mess, $lang, $serviceType, "0", "");
			for($j = 0; $j < count($rem["transaction_ids"]); $j++) {
				$sqlInsertTran .= "('".$rem["transaction_ids"][$j]."','$user','".$_SERVER['REMOTE_ADDR']."','$_from', '".$list_target[$i]."','$nor_mess','$lang','1')";
				if ($j < count($rem["transaction_ids"])-1) $sqlInsertTran .= ",";
			}
		}else{
			$transaction_ids = genID();
			if (strlen($rem['error']) == 0) {
				if ($i==($list_limit-1)) {
					$sms_return_id_all .= $transaction_ids;
					$send_fail .= $list_target[$i]." send fail timeout.";
				}else{
					$sms_return_id_all .= $transaction_ids.",";
					$send_fail .= $list_target[$i]." send fail timeout.,";
				}
			}else{
				if ($i==($list_limit-1)) {
					$sms_return_id_all .= $transaction_ids;
					$send_fail .= $list_target[$i]." send fail ".$rem['error'].".";
				}else{
					$sms_return_id_all .= $transaction_ids.",";
					$send_fail .= $list_target[$i]." send fail ".$rem['error'].".,";
				}
			}
			$returnToUser = "";
			writeLog($transaction_ids, $user, $from, $list_target[$i], $nor_mess, $lang, $serviceType, "15", $returnToUser, $rem['error']);
			$sqlInsertTran .= "('$transaction_ids','$user','".$_SERVER['REMOTE_ADDR']."','$_from', '".$list_target[$i]."','$nor_mess','$lang','1')";
		}
		if ($i < $list_limit-1) $sqlInsertTran .= ",";
		if (time()-$starttime > 15) {
			$starttime = time();
			set_time_limit(30); // reset time for loop
		}
		
		
		
		
		
		if ($user=="white.space") usleep(45000); // wait for 45 milisec (20 SMS/s)
		
		
		
		
		
	}
	if (!mssql_query($sqlInsertTran)) {
		$returnToUser = "can not update usage transactions.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "17", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	$sql = "UPDATE [VAS].[dbo].[SBG_ReportUsage] 
			SET [ReportUsage_Amount] = [ReportUsage_Amount] + $success_counter 
			WHERE [ReportUsage_ID] = '".$report['ReportUsage_ID']."';";
	if (!mssql_query($sql)) {
		$returnToUser = "can not update usage.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "18", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	$sql = "UPDATE [VAS].[dbo].[SBG_Customers] 
			SET [Customer_Connected] = [Customer_Connected] - 1
			, [Customer_AllCurrentUsage] = [Customer_AllCurrentUsage] + $success_counter
			WHERE [Customer_ID] = '".$account['Customer_ID']."';";
	if (!mssql_query($sql)) {
		$returnToUser = "can not update thread counter.";
		writeLog("", $user, $from, $target, $nor_mess, $lang, $serviceType, "19", $returnToUser);
		sleep(__pause__);
		return $returnToUser;
	}
	if ($list_limit > 30) {
		$returnToUser = "[$list_limit] server receive data $send_fail";
	} else {
		$returnToUser = "[".$sms_return_id_all."] "."server receive data $send_fail";
	}
	writeLog($sms_return_id_all, $user, $from, $target, $nor_mess, $lang, $serviceType, "0", $returnToUser);
	return $returnToUser;
}
?>
