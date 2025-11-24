<?php
# this page must be UTF-8 Encodeing
error_reporting(0);

# define default variable
define('_CEO',false);
define('_DEB',true);
define('_APP_ID',3); // 3=SBG
define('_LOGN',"/var/php.sbg.log/SMSWebServiceEngine.php");
define('_SES_USR_NAME',"session_user_name");
define('_SES_USR_TYP_ID',"session_user_type_id");
$php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];

# define basic function
require_once('/var/php.lib/function.basic.php');
require_once('/var/php.lib/function.basic.db.php');
require_once('bulk.api.engine.php');

define(_URL,"http://10.100.141.152/sbg/api/SMSWebServiceEngine.php");

function sendSMS($user, $pass, $from, $target, $mess, $lang, $expire, $scheduled) {
	return sendSMSEngine($user, md5($pass), $from, $target, $mess, $lang, $expire, $scheduled);
}

if ($HTTP_RAW_POST_DATA) {
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
}elseif ($_POST) {
	$HTTP_RAW_POST_DATA = file_get_contents('php://input');
}else{
	if(strtolower(substr($_SERVER['QUERY_STRING'], 0, 4))=="wsdl") {
		header('Content-Type: text/xml; charset=utf-8');
		echo str_replace("__URL__", _URL, file_get_contents("SMSWebserviceEngine.wsdl"));
	}else{
		header('Content-Type: text/html; charset=utf-8');
		echo str_replace("__URL__", _URL, file_get_contents("send_sms_function.html"));
	}
	exit(); // end code
}

// check is not utf-8
if (!isUtf8($HTTP_RAW_POST_DATA)) {
	echoMsg(__FILE__,__FUNCTION__,"receive-sbg-api","HTTP_RAW_POST_DATA not UTF-8 :: [$HTTP_RAW_POST_DATA]");
}else{
	echoMsg(__FILE__,__FUNCTION__,"receive-sbg-api","HTTP_RAW_POST_DATA :: [$HTTP_RAW_POST_DATA]");
}

if($HTTP_RAW_POST_DATA) {
	$aryXml = xml2Array(trim($HTTP_RAW_POST_DATA));
	$user = $aryXml['Envelope']['Body']['sendSMS']['user'];
	$pass = $aryXml['Envelope']['Body']['sendSMS']['pass'];
	$from = $aryXml['Envelope']['Body']['sendSMS']['from'];
	$target = $aryXml['Envelope']['Body']['sendSMS']['target'];
	$mess = $aryXml['Envelope']['Body']['sendSMS']['mess'];
	$lang = $aryXml['Envelope']['Body']['sendSMS']['lang'];
	$expire = $aryXml['Envelope']['Body']['sendSMS']['expire'];
	$scheduled = $aryXml['Envelope']['Body']['sendSMS']['scheduled'];
	$send_result= sendSMS($user, $pass, $from,$target,$mess,$lang,$expire,$scheduled);
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
	header('Content-Type: text/xml; charset=utf-8');
	echo '<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/">
	   <SOAP-ENV:Body>
		  <ns1:sendSMSResponse xmlns:ns1="http://localhost/service/">
			 <return xsi:type="xsd:string">'.$xml_result.'</return>
		  </ns1:sendSMSResponse>
	   </SOAP-ENV:Body>
	</SOAP-ENV:Envelope>';
}else{
	header('Content-Type: text/xml; charset=utf-8');
	echo '<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/">
	   <SOAP-ENV:Body>
		  <ns1:sendSMSResponse xmlns:ns1="http://localhost/service/">
			 <return xsi:type="xsd:string">Error Page not found</return>
		  </ns1:sendSMSResponse>
	   </SOAP-ENV:Body>
	</SOAP-ENV:Envelope>';
}
?>
