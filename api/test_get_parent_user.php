<?php
error_reporting(0); // close error
ini_set('memory_limit',"2048M");
require_once('bulk.api.engine.php');

$users = getParentUser("0986202776");

echo "abc <br>";
echo "$users <br>";


$users = explode(",",$users);

$result = print_r($users,true);

echo "result $result <br>";

foreach ($users as $user) {
	echo "$user <br>";
}



?>