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
	if ($user_id == "_NEW_") {
		$user_id = "";
	}elseif ($user_id == "_SELF_") {
		$user_id = $_SESSION[_SES_USR_NAME];
	}

	if (isset($user_id)) {
		$sql = "SELECT [SENDER_Username]
									,[SENDER_Sender]
									,[SENDER_Status]
									,[CATEGORY_Id]
									,[PRIORITY_flag]
									,[SENDER_CreateDt]
									,[SENDER_UpdateDt]
						FROM [SBG].[dbo].[SENDERS]
						WHERE [SENDER_Sender] = ?
						";

		$acc = querySqlSingleRowEx($sql,[$user_id]);
		// $acc2 = querySqlEx($sql,[$user_id]);
	}
?>
	<input class="form-control text-center" name="SENDER_Username" id="SENDER_Username" value="<?=$acc['SENDER_Username']?>" hidden/>
	
	<div class="form-row justify-content-center text-center">
		<div class="col-md-6">
			<div class="form-group" id="checkError_user">
				<label class="form-control-label mb-0">Sender Name</label>
				<input class="form-control text-center" name="SENDER_Sender" id="SENDER_Sender" value="<?=$acc['SENDER_Sender']?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group" id="checkError_user">
				<label class="form-control-label mb-0">Status</label>
				<select class="form-control text-center" name="SENDER_Status" id="SENDER_Status">
					<option value="1" <?=($acc['SENDER_Status'] == 1?"selected":'');?> > 1 : Active </option>
					<option value="2" <?=($acc['SENDER_Status'] == 2?"selected":'');?> > 2 : Blacklist </option>
					<option value="3" <?=($acc['SENDER_Status'] == 3?"selected":'');?> > 3 : Suspend </option>
				</select>
			</div>
		</div>
	</div>
	<div class="form-row justify-content-center text-center">
		<div class="col-md-6">
			<div class="form-group" id="checkError_user">
				<label class="form-control-label mb-0">Category</label>
				<select class="form-control text-center" name="CATEGORY_Id" id="CATEGORY_Id">
					<option value="1" <?=($acc['CATEGORY_Id'] == 1?"selected":'');?> > 1 : Public relations </option>
					<option value="2" <?=($acc['CATEGORY_Id'] == 2?"selected":'');?> > 2 : Normal </option>
					<option value="3" <?=($acc['CATEGORY_Id'] == 3?"selected":'');?> > 3 : Important </option>
					<option value="4" <?=($acc['CATEGORY_Id'] == 4?"selected":'');?> > 4 : Very important</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group" id="checkError_user">
				<label class="form-control-label mb-0">Priority Flag</label>
				<select class="form-control text-center" name="PRIORITY_flag" id="PRIORITY_flag">
					<option value="1" <?=($acc['PRIORITY_flag'] == 1?"selected":'');?> > 1 : Standard </option>
					<option value="2" <?=($acc['PRIORITY_flag'] == 2?"selected":'');?> > 2 : Low </option>
					<option value="3" <?=($acc['PRIORITY_flag'] == 3?"selected":'');?> > 3 : Medium </option>
					<option value="4" <?=($acc['PRIORITY_flag'] == 4?"selected":'');?> > 4 : High</option>
				</select>
			</div>
		</div>
	</div>
	<div class="form-row font-italic justify-content-center">
		<div class="col-md-5 text-right">Create date : <?=$acc['SENDER_CreateDt'];?></div>
		<div class="col-md-5">Last update : <?=$acc['SENDER_UpdateDt'];?></div>
	</div>