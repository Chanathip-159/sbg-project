<?
error_reporting(0); // close error
ini_set("memory_limit", "1024M");
$dbhost="10.100.143.100";
$dbuser="sa";
$dbpass="V@5my";
$dbname="VAS";

$con=mssql_connect($dbhost,$dbuser,$dbpass);
$db_connect = mssql_select_db($dbname,$con);
?>
