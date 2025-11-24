<!-- # ทดสอบภาษาไทย -->
<!-- Edit Account Modal-->
<script type="text/javascript">
function validateEditGroConModal(){
	var list_msisdn = /^[0-9,]+$/;
	var numbers = /^[0-9]+$/;
	
	var group_msisdn = document.form_con.GROUPMAP_Msisdn;
	var group_name = document.form_con.GROUPMAP_Name;
	var group_description = document.form_con.GROUPMAP_Description;

	if (group_name.value == null || group_name.value == "") {
		group_name.className += " has-error";
		group_name.focus();
		return false;
	}
	if (group_description.value != null  && group_description.value.length > 50) {
		group_description.className += " has-error";
		group_description.focus();
		return false;
	}
	if (!list_msisdn.test(group_msisdn.value) || group_msisdn.value == null || group_msisdn.value == "") {
		group_msisdn.className += " has-error";
		group_msisdn.focus();
		return false;
	}

	$('#editGroConModal').modal('hide');
}
</script>
<div class="modal fade" id="editGroConModal" tabindex="-1" role="dialog" aria-labelledby="editGroupContactModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="dialog">
		<div class="modal-content">
			<div class="modal-header justify-content-center font-weight-bold bg-primary text-white" id="edit-contact-header">New contact</div>
			<div class="modal-body edit-content">
				<form role="form" name="form_con" action="<?=_MAIN_DF;?>contacts" method="post" onsubmit="return validateEditGroConModal();">
					<div class="form-row">
						<div class="col-md-12" id="edit-content"></div>
					</div>
					<div class="form-row mt-3">	
						<div class="col-md-12 text-center"><!-- submit -->
							<input type="hidden" name="msisdn" id="msisdn" value="<?=$msisdn;?>" />
							<input type="hidden" name="<?=_DEST_FILE;?>" value="<?=$dest_file."&Sid=".$select_msisdn;?>" />
							<input type="hidden" name="select_page" value="<?=$select_page;?>">
							<input type="hidden" name="max_rows" value="-1">
							<input type="hidden" name="bypass" value="0">
							<div class="form-row form-inline justify-content-center">
								<button type="submit" class="btn btn-primary mx-1" id="btn-submit">Submit</button>
								<button type="reset" class="btn btn-default mx-1" id="btn-reset">Reset</button>
								<button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
					<div class="form-row">&nbsp;</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#editGroConModal').on('show.bs.modal', function(e) {
	//var $modal = $(this), group_msisdn = e.relatedTarget.id;
	var $modal = $(this);
	var group_count = <?=querySqlSingleFieldEx("SELECT COUNT(*) FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?", [$_SESSION[_SES_USR_NAME]]);?>;
	
	if (e.relatedTarget === undefined) {
		if (group_count>0) {
			group_msisdn = "new-0";
			document.getElementById("edit-contact-header").className = 'modal-header justify-content-center font-weight-bold bg-primary text-white';
			document.getElementById("edit-contact-header").innerHTML="New contact";
			document.getElementById("btn-submit").innerHTML="Submit";
			document.getElementById("btn-submit").style.display = "block";
			document.getElementById("btn-reset").style.display = "block";
		}else{
			group_msisdn = "new-0";
			document.getElementById("edit-contact-header").className = 'modal-header justify-content-center font-weight-bold bg-warning text-dark';
			document.getElementById("edit-contact-header").innerHTML="Warning";
			document.getElementById("btn-submit").style.display = "none";
			document.getElementById("btn-reset").style.display = "none";
		}
	}else{
		group_msisdn = e.relatedTarget.id;
		if (group_msisdn.substr(0, 4) == "del-") {
			document.getElementById("edit-contact-header").className = 'modal-header justify-content-center font-weight-bold bg-danger text-white';
			document.getElementById("edit-contact-header").innerHTML="Delete contact : "+group_msisdn.split("-")[2];
			document.getElementById("msisdn").value=group_msisdn.split("-")[2];
			document.getElementById("btn-submit").innerHTML="Apply";
			document.getElementById("btn-submit").style.display = "block";
			document.getElementById("btn-reset").style.display ="none";
		}else{
			document.getElementById("edit-contact-header").className = 'modal-header justify-content-center font-weight-bold bg-primary text-white';
			document.getElementById("edit-contact-header").innerHTML="Edit contact : "+group_msisdn.split("-")[1];
			document.getElementById("msisdn").value=group_msisdn.split("-")[1];
			document.getElementById("btn-submit").innerHTML="Submit";
			document.getElementById("btn-submit").style.display = "block";
			document.getElementById("btn-reset").style.display = "block";
		}
	}
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'element/_p_dialog_edit_group_contact_form.php',
		data: 'msisdn='+group_msisdn+"&",
		success: function(data) {
			document.getElementById("edit-content").innerHTML=data;
		}
	});
});
</script>