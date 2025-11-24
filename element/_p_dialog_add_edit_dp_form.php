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
		$sql = "SELECT [DEPARTMENT_Id]
                  ,[DEPARTMENT_Name]
                  ,[DEPARTMENT_Description]
                  ,[DEPARTMENT_CreateDt]
                  ,[DEPARTMENT_UpdateDt]
                  ,[DEPARTMENT_Username]
						FROM [SBG].[dbo].[DEPARTMENT]
						WHERE [DEPARTMENT_Name] = ?
						";

		$acc = querySqlSingleRowEx($sql,[$user_id]);
		// $acc2 = querySqlEx($sql,[$user_id]);
	}
?>
	<input class="form-control text-center" name="DEPARTMENT_Username" id="DEPARTMENT_Username" value="<?=$acc['DEPARTMENT_Username']?>" hidden/>
	
	<div class="form-row justify-content-center text-center">
		<div class="col-md-6">
			<div class="form-group" id="checkError_user">
				<label class="form-control-label mb-0">Department Name</label>
				<input class="form-control text-center" name="DEPARTMENT_Name" id="DEPARTMENT_Name" value="<?=$acc['DEPARTMENT_Name']?>" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group" id="checkError_user">
				<label class="form-control-label mb-0">Description</label>
				<input class="form-control text-center" name="DEPARTMENT_Description" id="DEPARTMENT_Description" value="<?=$acc['DEPARTMENT_Description']?>" />
			</div>
		</div>
	</div>
	<div class="form-row font-italic justify-content-center">
		<div class="col-md-5 text-right">Create date : <?=$acc['DEPARTMENT_CreateDt'];?></div>
		<div class="col-md-5">Last update : <?=$acc['DEPARTMENT_UpdateDt'];?></div>
	</div>