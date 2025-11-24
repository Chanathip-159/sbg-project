<?php
# ทดสอบภาษาไทย
session_start();
require_once("/var/php.lib/function.basic.php");
require_once("/var/php.lib/function.basic.db.php");
require_once("_1_defines.php"); #use for define variable such as _CEO, _DEB
require_once("_2_var_static.php"); #use for static variable such as month name
require_once("_3_functions_base.php"); #use for basic functions such as echoMsg
require_once("_4_functions_db.php"); # db connect and db functions

#define('_MAIN_DF', basename($_SERVER['PHP_SELF'])."?"._DEST_FILE."=");
/*
$dest_file = "manage";																																		// destination name of file
$_SESSION[_SES_CUR_PAGE] = $dest_file;																												// mark destination name to session
*/
require_once("_e_check_permission.php");																									// check permission from session name and session destination name

# handle SQL Injection
foreach ($_GET as $key => $value) {
  $_GET[$key]=addslashes(strip_tags(trim($value)));
}
foreach ($_POST as $key => $value) {
  $_POST[$key]=addslashes(strip_tags(trim($value)));
}

if (strlen($_POST['msisdn'])>0) {
	$group_msisdn = explode("-",$_POST['msisdn']);
	if (count($group_msisdn)==2) {
		$group_id = $group_msisdn[0];
		$msisdn = $group_msisdn[1];
		?><input type="hidden" name="edit_contact" id="edit_contact" value="1" />
		<input type="hidden" name="edit_contact_id" id="edit_contact_id" value="<?=$group_id;?>" />
		<input type="hidden" name="edit_contact_msisdn" id="edit_contact_msisdn" value="<?=$msisdn;?>" />
		<?php
	} elseif (count($group_msisdn)==3) {
		$del = true;
		$group_id = $group_msisdn[1];
		$msisdn = $group_msisdn[2];
	}
	if ($group_msisdn[0] != "new") {
		$verify1 = validateNumber($group_id);
		$verify2 = validatePhoneNumber($msisdn);
		$msisdn = convert2ThaiPhoneNumber($msisdn);

		if (!$msisdn || !$verify1 || !$verify2) {
			require_once("../_t_error.php");
			exit(0);
		}
	}else{
		if ($group_msisdn[0] == "new") {
			?><input type="hidden" name="new_contact" id="new_contact" value="1" /><?php
		}else{
			require_once("../_t_error.php");
			exit(0);
		}
	}
}else{
	$group_id = "";
	$msisdn = "";
}
$sql = "SELECT TOP "._GROUP_NUM." [GROUPINFO_Id], [GROUPINFO_Name] FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?";
if ($stmt = querySqlEx($sql, [$_SESSION[_SES_USR_NAME]])) {
	$groups = @$stmt->fetchAll();
}
if ($group_id>0) {
	$sql = "SELECT * FROM [ITOP].[dbo].[GROUPINFO], [ITOP].[dbo].[GROUPMAP] WHERE [GROUPINFO_Id] = [GROUPMAP_Id] AND [GROUPMAP_Id] = ? AND [GROUPMAP_Msisdn] = ?";
	$contact = querySqlSingleRowEx($sql, [$group_id, $msisdn]);
	#print_r($contact);
}
if ($groups[0]['GROUPINFO_Id']>0 && !$del) {
?>
						<div class="form-row justify-content-center text-center">
							<div class="col-md-8">
								<div class="form-group">
									<label class="form-control-label mb-0">Group *</label>
									<select id = "GROUPINFO_Id" name="GROUPINFO_Id" class="custom-select">
									<?php
									foreach($groups AS $group) {
									?>
									<option value="<?=$group['GROUPINFO_Id'];?>" <?=($group['GROUPINFO_Id']==$group_id ? "selected":"");?>><?=$group['GROUPINFO_Name'];?></option>
									<?php 
									}//*/
									?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-row justify-content-center text-center">
							<div class="col-md-8">
								<div class="form-group">
									<label class="form-control-label mb-0">Msisdn *</label>
									<input class="form-control text-center" name="GROUPMAP_Msisdn" id="GROUPMAP_Msisdn" value="<?=$contact['GROUPMAP_Msisdn']?>" />
								</div>
							</div>
						</div>
						<div class="form-row justify-content-center text-center">
							<div class="col-md-8">
								<div class="form-group">
									<label class="form-control-label mb-0">Name *</label>
									<input class="form-control text-center" name="GROUPMAP_Name" id="GROUPMAP_Name" value="<?=$contact['GROUPMAP_Name']?>" />
								</div>
							</div>
						</div>
						<div class="form-row justify-content-center text-center">
							<div class="col-md-8">
								<div class="form-group">
									<label class="form-control-label mb-0">Description (Max 50 characters)</label>
									<textarea class="form-control" rows="3" maxlength="500" name="GROUPMAP_Description" id="GROUPMAP_Description"><?=$contact['GROUPMAP_Description']?></textarea>
								</div>
							</div>
						</div>
						<div class="form-row font-italic justify-content-center">
							<div class="col-md-3 text-right">Activate date :</div>
							<div class="col-md-5"><?php echo $contact['GROUPMAP_ActivateDt'];?></div>
						</div>
						
<?php
} else if ($del) {
	echo "Click \"Apply\" to continue";
	?><input type="hidden" name="delete-id" id="delete-id" value="<?=$group_id?>" />
	<input type="hidden" name="delete-msisdn" id="delete-msisdn" value="<?=$msisdn?>" /><?php
}else{
	//require_once("../_t_error.php");
	echo "You must create at less a group for store your contacts";
}
?>