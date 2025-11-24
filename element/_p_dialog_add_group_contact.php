<!-- # ทดสอบภาษาไทย -->
<!-- Add member Modal-->
<script type="text/javascript">
function validateAddGroConModal(){
	var list_msisdn = /^[0-9,]+$/;
	var numbers = /^[0-9]+$/;

	var msisdns = document.form_group_add_con.Msisdns;
	var acc_name = document.form_group_add_con.ACC_Name;
	var acc_surname = document.form_group_add_con.ACC_Surname;
	var acc_contact_phone = document.form_group_add_con.ACC_ContactPhone;
	if (!list_msisdn.test(msisdns.value) || msisdns.value == null || msisdns.value == "") {
		msisdns.className += " has-error";
		msisdns.focus();
		return false;
	}
	if (acc_name.value == null || acc_name.value == "") {
		acc_name.className += " has-error";
		acc_name.focus();
		return false;
	}
	if (acc_surname.value == null || acc_surname.value == "") {
		acc_surname.className += " has-error";
		acc_surname.focus();
		return false;
	}
	if (!list_msisdn.test(acc_contact_phone.value) || acc_contact_phone.value == null || acc_contact_phone.value == "") {
		acc_contact_phone.className += " has-error";
		acc_contact_phone.focus();
		return false;
	}
	$('#addAccModal').modal('hide');
}
</script>
<div class="modal fade" id="addGroConModal" tabindex="-1" role="dialog" aria-labelledby="addGroupContactModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header justify-content-center font-weight-bold bg-primary text-white">
				Add new account(s)
			</div>
			<div class="modal-body">
				<form role="form" name="form_group_add_con" action="<?=_MAIN_DF;?>group_add_contact" method="post" onsubmit="return validateAddGroConModal()">
					<div class="form-row justify-content-center text-center">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-control-label mb-0">Member Phone number *<BR/>(up to 1,000 MSISDN Ex:66810000000,66810000001,...)</label>
								<textarea class="form-control" rows="2" col="11" maxlength="12000" name="Msisdns" id="Msisdns"></textarea>
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center text-center">	
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label mb-0">Type *</label>
								<select class="form-control" name="ACC_Level">
								<?php
								$sql = "SELECT [ACClevel_Id],[ACClevel_Name] FROM [ITOP].[dbo].[ACClevel]";
								if ($stmt = querySqlEx($sql)) {
									while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								?>
									<option value="<?=$row['ACClevel_Id'];?>"><?=$row['ACClevel_Name'];?></option>
								<?php
									}
								}
								?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label mb-0">Status *</label>
								<select class="form-control" name="ACC_Status">
								<?php
								$sql = "SELECT [ACCstatus_Id],[ACCstatus_Name] FROM [ITOP].[dbo].[ACCstatus]";
								if ($stmt = querySqlEx($sql)) {
									while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								?>
									<option value="<?=$row['ACCstatus_Id'];?>"><?=$row['ACCstatus_Name'];?></option>
								<?php
									}
								}
								?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center text-center">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label mb-0">Name *</label>
								<input class="form-control" name="ACC_Name" id="ACC_Name" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label mb-0">Surname *</label>
								<input class="form-control" name="ACC_Surname" id="ACC_Surname" />
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="form-control-label mb-0">Sex</label>
								<select class="form-control" name="ACC_Sex">
									<option value="1">Male</option>
									<option value="2">Female</option>
									<option value="3">Other</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center text-center">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label mb-0">Contact Phone number *</label>
								<input class="form-control" name="ACC_ContactPhone" id="ACC_ContactPhone" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label mb-0">Email</label>
								<input class="form-control" name="ACC_Email" id="ACC_Email" />
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center text-center">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-control-label mb-0">Description</label>
								<textarea class="form-control" rows="3" maxlength="500" name="ACC_Description" id="ACC_Description"></textarea>
							</div>
						</div>
					</div>
					<div class="form-row mt-3">	
						<div class="col-md-12 text-center"><!-- submit -->
							<input type="hidden" name="ACCPAIR_Parent" value="<?=$select_msisdn;?>" />
							<input type="hidden" name="<?=_DEST_FILE;?>" value="<?=$dest_file;?>" />
							<input type="hidden" name="select_page" value="<?=$select_page;?>">
							<input type="hidden" name="max_rows" value="-1">
							<input type="hidden" name="bypass" value="0">
							<button type="submit" class="btn btn-primary">Add</button>
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