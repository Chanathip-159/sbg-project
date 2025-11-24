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
	$sql = "SELECT [CUSTOMER_Username]
	,[CUSTOMER_Password]
	,[CUSTOMER_Category]
	,[CUSTOMER_Contact]
	,[CUSTOMER_Detail]
	,[CUSTOMER_Telephone]
	,[CUSTOMER_Email]
	,[CUSTOMER_ActivateDate]
	,[CUSTOMER_ExpireDate]
	,[CUSTOMER_MonthlyUsage]
	,[CUSTOMER_AccountUsage]
	,[CUSTOMER_CurrentUsage]
	,[CUSTOMER_CurrentUsageByDR]
	,[CUSTOMER_ChargeType]
	,[CUSTOMER_CreateDt]
	,[CUSTOMER_UpdateDt]
	,[CUSTOMER_NeedDr]
	,[CUSTOMER_Status]
	,[CUSTOMER_AdminComment]
	-- ,[CUSTOMER_Parent_Username]
FROM [SBG].[dbo].[CUSTOMERS]
WHERE [CUSTOMER_Username] = ?";
	$acc = querySqlSingleRowEx($sql,[$user_id]);
}
?>
						<div class="form-row justify-content-center text-center">	
							<div class="col-md-6">
								<div class="form-group" id="checkError_user">
									<label class="form-control-label mb-0">Username *</label>
									<input class="form-control text-center" name="CUSTOMER_Username" id="CUSTOMER_Username" value="<?=$user_id;?>" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="form-control-label mb-0">Category</label>
									<select class="form-control" name="CUSTOMER_Category" <?=($_SESSION[_SES_USR_TYP_ID]>_ADMIN_L?"disabled":"")?> disabled>
									<?php
									$sql = "SELECT [CATEGORY_Id],[CATEGORY_Name],[CATEGORY_Description] FROM [SBG].[dbo].[CATEGORIES]";
									if ($stmt = querySqlEx($sql)) {
										while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									?>
										<option value="<?=$row['CATEGORY_Id'];?>" <?=($acc['CUSTOMER_Category'] == $row['CATEGORY_Id'] ? 'selected' : '');?>><?=$row['CATEGORY_Name']." : ".$row['CATEGORY_Description'];?></option>
									<?php
										}
									}
									?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-row justify-content-center text-center">	
							<div class="col-md-6">
								<div class="form-group">
									<label class="form-control-label mb-0">Status *</label>
									<select class="form-control" name="CUSTOMER_Status" <?=($_SESSION[_SES_USR_TYP_ID]>_ADMIN_L?"disabled":"")?> disabled>
										<option value="1" <?=($acc['CUSTOMER_Status']=="1"?'selected':'');?>>1 : Active</option>
										<option value="0" <?=($acc['CUSTOMER_Status']=="0"?'selected':'');?>>0 : Idle</option>
										<option value="2" <?=($acc['CUSTOMER_Status']=="2"?'selected':'');?>>2 : Blacklist</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-row justify-content-center text-center">
							<div class="col-md-6">
								<div class="form-group" id="checkError_person">
									<label class="form-control-label mb-0">Contact Person *</label>
									<input class="form-control" name="CUSTOMER_Contact" id="CUSTOMER_Contact" value="<?=$acc['CUSTOMER_Contact']?>" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="checkError_phone">
									<label class="form-control-label mb-0">Phone No. *</label>
									<input class="form-control" name="CUSTOMER_Telephone" id="CUSTOMER_Telephone" value="<?=$acc['CUSTOMER_Telephone']?>" disabled>
								</div>
							</div>
						</div>
						<div class="form-row justify-content-center text-center">	
							<div class="col-md-6">
								<div class="form-group" id="checkError_email">
									<label class="form-control-label mb-0">e-Mail *</label>
									<input class="form-control" name="CUSTOMER_Email" id="CUSTOMER_Email" value="<?=$acc['CUSTOMER_Email']?>" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="form-control-label mb-0">Use DR</label>
									<select class="form-control" name="CUSTOMER_NeedDr" disabled>
										<option value="0" <?=($acc['CUSTOMER_NeedDr']=="0"?'selected':'');?>>0 : No DR</option>
										<option value="1" <?=($acc['CUSTOMER_NeedDr']=="1"?'selected':'');?>>1 : Use DR</option>
									</select>
								</div>
							</div>
						</div>		
<?php
if (strlen($acc['CUSTOMER_ActivateDate'])) {
	$start_date = explode("-",$acc['CUSTOMER_ActivateDate']);
	if (count($start_date) == 3) {
		$start_date_y = $start_date[0];
		$start_date_m = $start_date[1];
		$start_date_d = $start_date[2];
	}else{
		$start_default_time = strtotime('+3day');
		$start_date_y = date("Y",$start_default_time);
		$start_date_m = date("m",$start_default_time);
		$start_date_d = date("d",$start_default_time);
	}
}else{
	$start_default_time = strtotime('+3day');
	$start_date_y = date("Y",$start_default_time);
	$start_date_m = date("m",$start_default_time);
	$start_date_d = date("d",$start_default_time);
}


if (strlen($acc['CUSTOMER_ExpireDate'])) {
	$expire_date = explode("-",date("Y-m-d",strtotime($acc['CUSTOMER_ExpireDate']." 00:00:00 -1day")));
	if (count($expire_date) == 3) {
		$expire_date_y = $expire_date[0];
		$expire_date_m = $expire_date[1];
		$expire_date_d = $expire_date[2];
	}else{
		$expire_default_time = strtotime('+60day');
		$expire_date_y = date("Y",$expire_default_time);
		$expire_date_m = date("m",$expire_default_time);
		$expire_date_d = date("d",$expire_default_time);
	}
}else{
	$expire_default_time = strtotime('+60day');
	$expire_date_y = date("Y",$expire_default_time);
	$expire_date_m = date("m",$expire_default_time);
	$expire_date_d = date("d",$expire_default_time);
}
if ($start_date_y < $expire_date_y) $date_y_min = $start_date_y; else $date_y_min = $expire_date_y;
if ($date_y_min > date("Y")-2) $date_y_min = date("Y")-2;
?>	
						<div class="form-row justify-content-center text-center">	
							<div class="col-md-6">					
								<div class="form-group input-group mb-0"><!-- Start Date -->
									<div class="form-group col-md-12 mb-0">
										<label class="control-label">Active Date (00:00:00)</label>
									</div>
									<div class="form-group col-md-4">
										<select name="CUSTOMER_ActivateDate_year" class="custom-select" disabled>
<?php
for($year = $date_y_min; $year < date("Y")+10; $year++) {
?>
											<option value="<?=$year; ?>" <?=($year==$start_date_y?"selected":'');?>><?=$year; ?></option>
<?php
}
?>
										</select>
									</div>
									<div class="form-group col-md-5">
										<select name="CUSTOMER_ActivateDate_month" class="custom-select" disabled>
<?php
for($i=1;$i<=12;$i++) {
?>
											<option value="<?=$i;?>" <?=(strlen($start_date_m)&&$i==$start_date_m?"selected":'');?>><?=$month_eng_full[$i];?></option>
<?php 
}
?>
										</select>
									</div>
									<div class="form-group col-md-3">
										<select name="CUSTOMER_ActivateDate_day" class="custom-select" disabled>
<?php
for($i=1;$i<=31;$i++) {
?>
											<option value="<?=$i;?>" <?=(strlen($start_date_d)&&$i==$start_date_d?"selected":'');?>><?=$i;?></option>
<?php 
}
?>
										</select>
									</div>
								</div><!-- Start Date -->
							</div>
							<div class="col-md-6">	
								<div class="form-group input-group"><!-- Expire Date --> 
									<div class="form-group col-md-12 mb-0">
										<label class="control-label">Expire Date (23:59:59)</label>
									</div>
									<div class="form-group col-md-4">
										<select name="CUSTOMER_ExpireDate_year" class="custom-select" disabled>
<?php
for($year = $date_y_min; $year < date("Y")+10; $year++) {
?>
											<option value="<?=$year; ?>" <?=($year==$expire_date_y?"selected":'');?>><?=$year; ?></option>
<?php
}
?>
											<option value="3000" <?=("3000"==$expire_date_y?"selected":'');?>>3000</option>
										</select>
									</div>
									<div class="form-group col-md-5">
											<select name="CUSTOMER_ExpireDate_month" class="custom-select" disabled>
<?php
for($i=1;$i<=12;$i++) {
?>
											<option value="<?=$i;?>" <?=(strlen($expire_date_m)&&$i==$expire_date_m?"selected":'');?>><?=$month_eng_full[$i];?></option>
<?php 
}
?>
										</select>
									</div>
									<div class="form-group col-md-3">
											<select name="CUSTOMER_ExpireDate_day" class="custom-select" disabled>
<?php
for($i=1;$i<=31;$i++) {
?>
											<option value="<?=$i;?>" <?=(strlen($expire_date_d)&&$i==$expire_date_d?"selected":'');?>><?=$i;?></option>
<?php 
}
?>
										</select>
									</div>
								</div><!-- Expire Date -->
							</div>
						</div>
						
						
						
						<div class="form-row justify-content-center text-center">
							<div class="col-md-3">
								<div class="form-group">
									<label class="form-control-label mb-0">Monthly Usage *</label>
									<input class="form-control" name="CUSTOMER_MonthlyUsage" id="CUSTOMER_MonthlyUsage"  value="<?=$acc['CUSTOMER_MonthlyUsage']?>" />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label class="form-control-label mb-0">Account Usage *</label>
									<input class="form-control" name="CUSTOMER_AccountUsage" id="CUSTOMER_AccountUsage" value="<?=$acc['CUSTOMER_AccountUsage']?>" />
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label class="form-control-label mb-0">Apply</label>
									<select name="CUSTOMER_MonthlyUsage_Apply" class="custom-select">
										<option value="0">Next month</option>
										<option value="1">This month</option>
									</select>
								</div>
							</div>
						</div>
						
						
						<div class="form-row justify-content-center text-center">
							<div class="col-md-2">
								<div class="form-group">
									<label class="form-control-label mb-0">ChargeType</label>
									<select name="CUSTOMER_ChargeType_Apply" class="custom-select" disabled>
										<option value="0">Normal</option>
										<option value="1">by DR</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="form-control-label mb-0">CurrentUsageNormal</label>
									<input class="form-control" name="CUSTOMER_CurrentUsage" id="CUSTOMER_CurrentUsage" value="<?=$acc['CUSTOMER_CurrentUsage']?>" disabled />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="form-control-label mb-0">CurrentUsageByDR</label>
									<input class="form-control" name="CUSTOMER_CurrentUsageByDR" id="CUSTOMER_CurrentUsageByDR" value="<?=$acc['CUSTOMER_CurrentUsageByDR']?>" disabled />
								</div>
							</div>
						</div>
	
						<div class="form-row justify-content-center text-center">
							<div class="col-md-6">
								<div class="form-group">
									<label class="form-control-label mb-0">Detail</label>
									<textarea class="form-control" rows="3" maxlength="500" name="CUSTOMER_Detail" id="CUSTOMER_Detail" disabled><?=$acc['CUSTOMER_Detail']?></textarea>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="form-control-label mb-0">Admin Remark</label>
									<textarea class="form-control" rows="3" maxlength="500" name="CUSTOMER_AdminComment" id="CUSTOMER_AdminComment" <?=($_SESSION[_SES_USR_TYP_ID]>_ADMIN_L?"disabled":"")?>><?=$acc['CUSTOMER_AdminComment']?></textarea>
								</div>
							</div>
						</div>
						<div class="form-row font-italic justify-content-center">
							<div class="col-md-5 text-right">Create date : <?php echo $acc['CUSTOMER_CreateDt'];?></div>
							<div class="col-md-5">Last update : <?=$acc['CUSTOMER_UpdateDt'];?></div>
						</div>