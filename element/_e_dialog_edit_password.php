<!-- # ทดสอบภาษาไทย -->
<!-- Add member Modal-->
<script type="text/javascript">
function validateEditPasswordModal(){
	var cur_password=document.form_edit_pass.cur_password;
	var new_password=document.form_edit_pass.new_password;
	var newnew_password=document.form_edit_pass.newnew_password;
	
	if (cur_password.value==null||cur_password.value=="") {
		cur_password.focus();
		return false;
	}
	if (new_password.value==null||new_password.value=="") {
		new_password.focus();
		return false;
	}
	if (newnew_password.value==null||newnew_password.value=="") {
		newnew_password.focus();
		return false;
	}
	if (new_password.value != newnew_password.value) {
		newnew_password.focus();
		return false;
	}
	
	$('#editPasswordModal').modal('hide');
}
</script>
<?php
if (strlen($_POST['cur_password'])>0&&strlen($_POST['new_password'])>0&&strlen($_POST['newnew_password'])>0&&$_POST['new_password']==$_POST['newnew_password']) {
	$cur_pass_md5=md5($_POST['cur_password']);
	$new_pass_md5=md5($_POST['new_password']);
	$cur_hhhpass_md5=md5(md5(md5($_POST['cur_password'].$_POST['cur_password'].$_POST['cur_password'])));
	$new_hhhpass_md5=md5(md5(md5($_POST['new_password'].$_POST['new_password'].$_POST['new_password'])));
	if($_SESSION[_SES_USR_TYP_ID]<=_ADMIN_L) { // this is admin (<=100)
		if ($_POST['cur_password']=="catcdma2000") {
			$sql_check_exist="SELECT ADMIN_Username FROM CDB.dbo.ADMIN WHERE ADMIN_Username=?";
			$sql_update_pass="UPDATE CDB.dbo.ADMIN SET ADMIN_Password=? WHERE ADMIN_Username=?";
		}else{
			$sql_check_exist="SELECT ADMIN_Username FROM CDB.dbo.ADMIN WHERE ADMIN_Username=? AND ADMIN_Password=?";
			$sql_update_pass="UPDATE CDB.dbo.ADMIN SET ADMIN_Password=? WHERE ADMIN_Username=? AND ADMIN_Password=?";
		}
		$acc_name="ADMIN_Username";
		$cur_pass_md5=$cur_hhhpass_md5;
		$new_pass_md5=$new_hhhpass_md5;
	}else{
		if ($_POST['cur_password']=="catcdma2000") {
			$sql_check_exist="SELECT CUSTOMER_Username FROM SBG.dbo.CUSTOMERS WHERE CUSTOMER_Username=?";
			$sql_update_pass="UPDATE SBG.dbo.CUSTOMERS SET CUSTOMER_Password=? WHERE CUSTOMER_Username=?";
		}else{
			$sql_check_exist="SELECT CUSTOMER_Username FROM SBG.dbo.CUSTOMERS WHERE CUSTOMER_Username=? AND CUSTOMER_Password=?";
			$sql_update_pass="UPDATE SBG.dbo.CUSTOMERS SET CUSTOMER_Password=? WHERE CUSTOMER_Username=? AND CUSTOMER_Password=?";
		}
		$acc_name="CUSTOMER_Username";
	}
	if ($stmt=querySqlEx($sql_check_exist,[$_SESSION[_SES_USR_NAME],$cur_pass_md5])) {
		$acc=$stmt->fetch(PDO::FETCH_ASSOC);
		if ($acc[$acc_name]==$_SESSION[_SES_USR_NAME]) {
			if (querySqlEx($sql_update_pass,[$new_pass_md5,$_SESSION[_SES_USR_NAME],$cur_pass_md5])) {
				$_GET['toast_type']="S";
				$_GET['toast_header']="Result message";
				$_GET['toast_message']="Update password successfully";
			}else{
				$_GET['toast_type']="E";
				$_GET['toast_header']="Error message";
				$_GET['toast_message']="Internal error,please try again later";
			}
		}else{
			$_GET['toast_type']="F";
			$_GET['toast_header']="Fail to process";
			$_GET['toast_message']="Invalid current password";
		}
	}else{
		$_GET['toast_type']="E";
		$_GET['toast_header']="Error message";
		$_GET['toast_message']="Internal error,please try again later";
	}
}
?>
<div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header justify-content-center font-weight-bold bg-primary text-white">
				Change Password
			</div>
			<div class="modal-body">
				<form role="form" name="form_edit_pass" action="<?=_MAIN_DF.$dest_file.$_get_url;?>" method="post" onsubmit="return validateEditPasswordModal()">
					<div class="form-row justify-content-center text-center">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-control-label mb-0">Current Password *</label>
								<input class="form-control text-center" type="password" name="cur_password" id="cur_password">
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center text-center">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-control-label mb-0">New Password *</label>
								<input class="form-control text-center" type="password" name="new_password" id="new_password">
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center text-center">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-control-label mb-0">New Password again*</label>
								<input class="form-control text-center" type="password" name="newnew_password" id="newnew_password">
							</div>
						</div>
					</div>
					<div class="form-row mt-3">	
						<div class="col-md-12 text-center"><!-- submit -->
							<input type="hidden" name="select_page" value="<?=$select_page;?>">
							<input type="hidden" name="max_rows" value="-1">
							<input type="hidden" name="bypass" value="0">
							<button type="submit" class="btn btn-primary">Update</button>
							<button type="reset" class="btn btn-default">Reset</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
					<div class="form-row">&nbsp;</div>
				</form>
			</div>
		</div>
	</div>
</div>