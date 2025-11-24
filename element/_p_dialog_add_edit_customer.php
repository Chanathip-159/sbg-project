<!-- # ทดสอบภาษาไทย -->
<!-- Edit Account Modal-->
<script type="text/javascript">
function validateAddEditCustomerAccModal(){
	var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	
	var user = document.form.CUSTOMER_Username.value;
	if (user == null || user == "") {
		document.getElementById("checkError_user").className += " has-error";
		form.CUSTOMER_Username.focus();
		return false;
    }
	var contact = document.form.CUSTOMER_Contact.value;
	if (contact == null || contact == "") {
		document.getElementById("checkError_person").className += " has-error";
		form.CUSTOMER_Contact.focus();
		return false;
    }
	var phone = document.form.CUSTOMER_Telephone.value;
	if (phone == null || phone == "") {
		document.getElementById("checkError_phone").className += " has-error";
		form.CUSTOMER_Telephone.focus();
		return false;
    }
	
	var email = document.form.CUSTOMER_Email.value;
	if (email == null || email == "" || !mailformat.test(email)) {
		document.getElementById("checkError_email").className += " has-error";
		form.CUSTOMER_Email.focus();
		return false;
    }
	

	
	document.form_acc_edit_acc.action = "<?=_MAIN_DF;?>"+document.form_acc_edit_acc.<?=_DEST_FILE;?>.value;
	$('#addEditCustomerAccModal').modal('hide');
}
</script>
<div class="modal fade" id="addEditCustomerAccModal" tabindex="-1" role="dialog" aria-labelledby="addEditCustomerAccModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="dialog">
		<div class="modal-content">
			<div class="modal-header justify-content-center font-weight-bold bg-primary text-white" id="add-edit-customer-modal-header">New customer</div>
			<div class="modal-body edit-content">
				<form role="form" name="form_acc_edit_acc" action="<?=_MAIN_DF;?>manage_cus" method="post" onsubmit="return validateAddEditCustomerAccModal();">
					<div class="form-row">
						<div class="col-md-12" id="add-edit-customer-modal-content"></div>
					</div>
					<div class="form-row mt-3">	
						<div class="col-md-12 text-center"><!-- submit -->
							<input type="hidden" name="add_edit_customer_id" id="add_edit_customer_id" />
							<input type="hidden" name="del_item" id="del_item" />
							<input type="hidden" name="reset_item" id="reset_item" />
							<input type="hidden" name="<?=_DEST_FILE;?>" value="<?=$dest_file;?>" />
							<input type="hidden" name="<?=_TXT_SEARCH;?>" value="<?=$_POST[_TXT_SEARCH];?>" />
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
$('#addEditCustomerAccModal').on('show.bs.modal', function(e) {
	var $modal = $(this), user_id = e.relatedTarget.id;
	var load_sub=true;
	document.getElementById("add-edit-customer-modal-content").innerHTML="";
	form_acc_edit_acc.action = "<?=_MAIN_DF;?>manage_cus";
	if (user_id.substr(0, 4) == "del-") {
		document.getElementById("add-edit-customer-modal-header").className = 'modal-header justify-content-center font-weight-bold bg-danger text-white';
		document.getElementById("add-edit-customer-modal-header").innerHTML="Delete customer : "+user_id.split("-")[1];
		document.getElementById("add_edit_customer_id").value="";
		document.getElementById("del_item").value=user_id.split("-")[1];
		document.getElementById("btn-submit").innerHTML="Apply";
		document.getElementById("btn-reset").style.display ="none";
		load_sub=false;
	}else if (user_id.substr(0, 6) == "reset-") {
		document.getElementById("add-edit-customer-modal-header").className = 'modal-header justify-content-center font-weight-bold bg-warning text-white';
		document.getElementById("add-edit-customer-modal-header").innerHTML="Reset password of customer : "+user_id.split("-")[1];
		document.getElementById("add_edit_customer_id").value="";
		document.getElementById("reset_item").value=user_id.split("-")[1];
		document.getElementById("btn-submit").innerHTML="Apply";
		document.getElementById("btn-reset").style.display ="none";
		load_sub=false;
	}else if (user_id == "_NEW_") {
		document.getElementById("add-edit-customer-modal-header").className = 'modal-header justify-content-center font-weight-bold bg-primary text-white';
		document.getElementById("add-edit-customer-modal-header").innerHTML="New customer";
		document.getElementById("add_edit_customer_id").value=user_id;
		document.getElementById("btn-submit").innerHTML="Submit";
		document.getElementById("btn-reset").style.display ="block";
		load_sub=true;
	}else if (user_id.length > 0){
		document.getElementById("add-edit-customer-modal-header").className = 'modal-header justify-content-center font-weight-bold bg-primary text-white';
		document.getElementById("add-edit-customer-modal-header").innerHTML="Edit customer : "+user_id;
		document.getElementById("add_edit_customer_id").value=user_id;
		document.getElementById("btn-submit").innerHTML="Submit";
		document.getElementById("btn-reset").style.display ="block";
		load_sub=true;
	}
	if (load_sub) {
		$.ajax({
			cache: false,
			type: 'POST',
			url: 'element/_p_dialog_add_edit_customer_form.php',
			data: 'user_id='+user_id+"&",
			success: function(data) {
				document.getElementById("add-edit-customer-modal-content").innerHTML=data;
			}
		});
	}
});
</script>