<?php
  # ทดสอบภาษาไทย
  session_start();
  require_once(__DIR__."/../../php.lib/function.basic.php");
  require_once(__DIR__."/../../php.lib/function.basic.db.php");
  require_once("_1_defines.php"); #use for define variable such as _CEO,_DEB
  require_once("_2_var_static.php"); #use for static variable such as month name
  require_once("_3_functions_base.php"); #use for basic functions such as echoMsg
  require_once("_4_functions_db.php"); # db connect and db functions

  #define('_MAIN_DF',basename($_SERVER['PHP_SELF'])."?"._DEST_FILE."=");
  /*
  $dest_file = "manage";																																		// destination name of file
  $_SESSION[_SES_CUR_PAGE] = $dest_file;																												// mark destination name to session
  */
  require_once("_e_check_permission.php");																									// check permission from session name and session destination name

  # handle SQL Injection
  foreach ($_GET as $key=>$value) {
    $_GET[$key]=addslashes(strip_tags(trim($value)));
  }
  foreach ($_POST as $key=>$value) {
    $_POST[$key]=addslashes(strip_tags(trim($value)));
  }

  if (strlen($_POST['user_id'])) $user_id = $_POST['user_id'];
  if ($user_id == "_ADD_") {
    $user_id = "";
  }elseif ($user_id == "_SELF_") {
    $user_id = $_SESSION[_SES_USR_NAME];
  }
?>
<!--
  <div class="form-row justify-content-center text-center">
    <div class="col-md-6">
      <div class="form-group" id="checkError_user">
        <input class="form-control text-center" placeholder="Username" name="CUSTOMER_Parent_Username" id="CUSTOMER_Parent_Username" value="<?=$user_id;?>" <?=($user_id == $_SESSION[_SES_USR_NAME] ? 'disabled' : '')?> required/>
        &nbsp;
        <input class="form-control text-center" placeholder="Account Usage" name="CUSTOMER_AccountUsage" id="CUSTOMER_AccountUsage" required/>
      </div>
    </div>
  </div>
-->
<div class="form-row justify-content-center text-center">
    <div class="col-md-6">
      <div class="form-group" id="checkError_user">
        <input class="form-control text-center" placeholder="Username" name="CUSTOMER_Username" id="CUSTOMER_Username" value="<?=$user_id;?>" <?=($user_id == $_SESSION[_SES_USR_NAME] ? 'disabled' : '')?> required/>
        &nbsp;
        <input class="form-control text-center" placeholder="Sub-user" name="CUSTOMER_Parent_Username" id="CUSTOMER_Parent_Username" required/>
        &nbsp;
        <input class="form-control text-center" placeholder="Account usage for Sub-user" name="CUSTOMER_AccountUsage" id="CUSTOMER_AccountUsage" required/>
      </div>
    </div>
  </div>

						
