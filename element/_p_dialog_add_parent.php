<!-- # ทดสอบภาษาไทย -->
<!-- Edit Account Modal-->
<script type="text/javascript">
  function validateAddParentModal(){
    document.form_add_parent.action = "<?=_MAIN_DF;?>"+document.form_add_parent.<?=_DEST_FILE;?>.value;
    $('#addParentModal').modal('hide');
  }
</script>
<div class="modal fade" id="addParentModal" tabindex="-1" role="dialog" aria-labelledby="addParentModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="dialog">
		<div class="modal-content">
			<div class="modal-header justify-content-center font-weight-bold bg-primary text-white" id="add-parent-modal-header">New customer</div>
			<div class="modal-body edit-content">
				<form role="form" name="form_add_parent" action="<?=_MAIN_DF;?>first" method="post" onsubmit="return validateAddParentModal();">
					<div class="form-row">
						<div class="col-md-12" id="add-parent-modal-content"></div>
					</div>
					<div class="form-row mt-3">	
						<div class="col-md-12 text-center"><!-- submit -->
							<input type="hidden" name="add_parent_customer_id" id="add_parent_customer_id" />
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
$('#addParentModal').on('show.bs.modal', function(e) {
	var $modal = $(this), user_id = e.relatedTarget.id;
	var load_sub=true;
	document.getElementById("add-parent-modal-content").innerHTML="";
	form_add_parent.action = "<?=_MAIN_DF;?>first";
  
  if (user_id == "_ADD_") {
		document.getElementById("add-parent-modal-header").className = 'modal-header justify-content-center font-weight-bold bg-primary text-white';
		document.getElementById("add-parent-modal-header").innerHTML="Add Sub-user";
		document.getElementById("add_parent_customer_id").value=user_id;
		document.getElementById("btn-submit").innerHTML="Submit";
		document.getElementById("btn-reset").style.display ="block";
		load_sub=true;
	}

	if (load_sub) {
		$.ajax({
			cache: false,
			type: 'POST',
			url: 'element/_p_dialog_add_parent_form.php',
			data: 'user_id='+user_id+"&",
			success: function(data) {
				document.getElementById("add-parent-modal-content").innerHTML=data;
			}
		});
	}
});
</script>