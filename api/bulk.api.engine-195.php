<?php
error_reporting(0); // close error
ini_set('memory_limit',"2048M");
require_once(__DIR__.'/../../php.lib/function.basic.php');
require_once(__DIR__.'/../../php.lib/function.basic.db.php');
require_once(__DIR__.'/../../php.lib/api.sms.send.direct.php');

define('_SMS_SERVICE_TYPE',"BUK");

define('_PAUSE',2); // sec
define('_MAX_NUM',1000);

function sendSMSEngine($user,$hpass,$from,$target,$mess,$lang,$expire=null,$scheduled=null) {
	$year_month=date("Ym");
	$log_msg="$user,$hpass,$from,$target,$mess,$lang,$expire,$scheduled";
	if ((strlen($user)==0)||(strlen($hpass)==0)) {
		$return=Array('error_code'=>1,'error'=>"invalid user or password.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	if (strlen($from)==0) {
		$return=Array('error_code'=>2,'error'=>"empty from.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	if (strlen($mess)==0) {
		$return=Array('error_code'=>4,'error'=>"empty message.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	if (strlen($lang)==0) {
		$return=Array('error_code'=>5,'error'=>"empty lang.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	$target_explode=explode(",",$target);
	$total_target=count($target_explode);
	if ($total_target>_MAX_NUM) {
		$return=Array('error_code'=>20,'error'=>"over receiver.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	if ($total_target==0) {
		$return=Array('error_code'=>19,'error'=>"no target.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	foreach($target_explode AS $target) {
		if (!validatePhoneNumber($target,false)) { // false=domestic, true=international
			$return=Array('error_code'=>18,'error'=>"not phone number format.");
			writeLog($log_msg,$return);
			sleep(_PAUSE);
			return $return;
		}
		$targets[]=convert2ThaiPhoneNumber($target);
	}
	$sql="SELECT GETDATE() AS 'current_datetime',CUSTOMER_Sender,CUSTOMER_ActivateDate,CUSTOMER_ExpireDate,CUSTOMER_MonthlyUsage,CUSTOMER_AccountUsage,CUSTOMER_CurrentUsage,CUSTOMER_NeedDr,CUSTOMER_Status
FROM SBG.dbo.CUSTOMERS
WHERE CUSTOMER_Username=? AND CUSTOMER_Password=?";
	$account=querySqlSingleRowEx($sql,[$user,$hpass]);
	if (!$account['CUSTOMER_ActivateDate']) {
		$return=Array('error_code'=>8,'error'=>"invalid username or password.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	// regist date
	if (isset($account['CUSTOMER_ActivateDate'])&&strlen($account['CUSTOMER_ActivateDate'])&&time()<strtotime($account['CUSTOMER_ActivateDate'])) {
		$return=Array('error_code'=>9,'error'=>"account idle.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	// expired date
	if (isset($account['CUSTOMER_ExpireDate'])&&strlen($account['CUSTOMER_ExpireDate'])&&time()>strtotime($account['CUSTOMER_ExpireDate'])) {
		$return=Array('error_code'=>10,'error'=>"account expired.");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	if (($account['CUSTOMER_CurrentUsage']+$total_target>$account['CUSTOMER_AccountUsage'])&&($account['CUSTOMER_AccountUsage']!="-1")) {
		$return=Array('error_code'=>11,'error'=>"account out of credit (a).");
		writeLog($log_msg,$return);
		sleep(_PAUSE);
		return $return;
	}
	// check sender
	if ($from!=$account['CUSTOMER_Sender']) {
		if (querySqlSingleFieldEx("SELECT COUNT(*) count_sender FROM SBG.dbo.SENDERS WHERE SENDER_Username=?",[$user])) {
			// lock sender
			$match=false;
			$stmt=querySqlEx("SELECT SENDER_Sender FROM SBG.dbo.SENDERS WHERE SENDER_Username=?",[$user]);
			$log_sender="";
			while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$log_sender.=$row['SENDER_Sender'].",";
				if ($row['SENDER_Sender']==$from) $match=true;
			}
			if (!$match) {
				$return=Array('error_code'=>17,'error'=>"sender [$from] not allow.",'DBsender'=>$log_sender);
				writeLog($log_msg,$return);
				sleep(_PAUSE);
				return $return;
			}
		}
	}
	
	$send_data=Array();
	$send_data['sms_client_ip']=getClientIp();
	$send_data['sms_type']="submit"; 
	$send_data['sms_service_type']=_SMS_SERVICE_TYPE; // Application name (6 character)
	$send_data['sms_charge_account']=$user;
	$send_data['sms_sender']=$from;
	$send_data['sms_delivery_report']=(isset($account['CUSTOMER_NeedDr'])?$account['CUSTOMER_NeedDr']:'0'); // 0=no need dr sms; 1=need dr sms
	$send_data['sms_langauge']=$lang;
	if (strlen($expire)) $send_data['sms_validity_period']=$expire;
	if (strlen($scheduled)) $send_data['sms_schedule_delivery_time']=$scheduled;
	$send_data['sms_message']=$mess;
	$starttime=time();
	$transaction_ids=Array();
	$error_code=0;
	$error=NULL;
	$fails=Array();
	$success_counter=0;
	$targets_all = array_chunk($targets,20);
	foreach($targets_all AS $targets_20){
		$send_data['sms_receiver']=@implode(",",$targets_20);
		$rem=sendSmsToDb($send_data);
		if (!isset($rem['error_code'])||(strlen($rem['error_code'])&&$rem['error_code']!="0")) {
			$fails[]=$targets_20[0]."-".$targets_20[count($targets_20)-1]." send_fail ".$rem['error_code'];
			$error_code=999;
		}else{
			$transaction_ids=array_merge($transaction_ids,$rem['transaction_ids']);
		}
		if (time()-$starttime>15) {
			$starttime=time();
			set_time_limit(30); // reset time for loop
		}
	}

	$sql="SELECT REPORT_Username,REPORT_MonthUsage,REPORT_MonthQuota FROM SBG.dbo.REPORTS WHERE REPORT_Username=? AND REPORT_YearMonth=?";
	$report=querySqlSingleRowEx($sql,[$user,$year_month]);
	if (!$report['REPORT_Username']) {
		$sql="INSERT INTO SBG.dbo.REPORTS (REPORT_Username,REPORT_YearMonth,REPORT_MonthQuota) VALUES (?,?,?)";
		querySqlEx($sql,[$user,$year_month,$account['CUSTOMER_MonthlyUsage']]);
		if (($total_target>$account['CUSTOMER_MonthlyUsage'])&&($account['CUSTOMER_MonthlyUsage']!="-1")) {
			$return=Array('error_code'=>12,'error'=>"account out of credit (m).");
			writeLog($log_msg,$return);
			sleep(_PAUSE);
			return $return;
		}
	}else{
		if (($report['REPORT_MonthUsage']+$total_target>$report['REPORT_MonthQuota'])&&($report['REPORT_MonthQuota']!="-1")) {
			$return=Array('error_code'=>13,'error'=>"account out of credit (m).");
			writeLog($log_msg,$return);
			sleep(_PAUSE);
			return $return;
		}
	}

	$total_success=count($transaction_ids);
	$sql="UPDATE SBG.dbo.REPORTS SET REPORT_MonthUsage=REPORT_MonthUsage+CONVERT(int,?),REPORT_UpdateDt=GETDATE() WHERE REPORT_Username=? AND REPORT_YearMonth=?
UPDATE SBG.dbo.CUSTOMERS SET CUSTOMER_CurrentUsage=CUSTOMER_CurrentUsage+CONVERT(int,?) WHERE CUSTOMER_Username=?";
	querySqlEx($sql,[$total_success,$user,$year_month,$total_success,$user]);
	$return=Array('error_code'=>$error_code,'error'=>$error,'transaction_ids'=>$transaction_ids,'fails'=>$fails);
	writeLog($log_msg,$return);
	return $return;
}

// Include curent user
function getParentUser($user,$level=0) {

	$users = "";

	// Add Current user
	if ($level == 0) {
		$users = $user;
	}

	// Find Parent
	$sql="SELECT CUSTOMER_Parent_Username
	FROM SBG.dbo.CUSTOMERS
	WHERE CUSTOMER_Username=?";
	$account=querySqlSingleRowEx($sql,[$user]);
	if ($account['CUSTOMER_Parent_Username']) {
		
		// Add Parent
		$users = $users . "," . $account['CUSTOMER_Parent_Username'];

		// Next Parent level
		$level = $level + 1;
		
		// Find Grant Parent
		$users = $users . getParentUser($account['CUSTOMER_Parent_Username'],$level);

	}

	return $users;

}

function writeLog($log_msg,$return){
	echoMsg(__FILE__,__FUNCTION__,"receive-sbg-api","input::".$log_msg." return::".@print_r($return,true));
}
?>
