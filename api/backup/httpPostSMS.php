<?
/* require cURL */
define("__URL__","http://10.100.143.143/api/SMSHttpEngine.php");

$user="<YOUR USERNAME>";
$pass="<YOUR PASSWORD>";
$from="Sendername"; // 11 digits max
$target="66864600000,66864600001"; // 66864600000,66864600001

$user="";
$pass="";
$from="";
$target="";

$mess = "ทดสอบภาษาไทย 2-1";
$mess = urlencode($mess);
$lang="T";
#$expire="2015-11-17 13:25:00";
//$scheduled="2015-17-17 10:00:00";
echo SendHttpPostSMS(Array('user'=>$user,'pass'=>$pass,'from'=>$from,'target'=>$target,'mess'=>$mess,'lang'=>$lang,'expire'=>$expire/*,'scheduled'=>$scheduled*/));


function SendHttpPostSMS ($params) {
	$postdata = http_build_query($params);
	$opts = array(
		'http' =>array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata,
			'timeout' => 30
			)
		,'ssl' => array(
			'allow_self_signed'=>true,
			'verify_peer'=>false
		)
	);
	$context  = stream_context_create($opts);
	$result = file_get_contents(__URL__, false, $context);
	return $result;
}
?>
