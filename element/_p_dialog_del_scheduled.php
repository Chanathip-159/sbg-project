<!-- # ทดสอบภาษาไทย -->
<!-- Edit Account Modal-->
<div class="modal fade" id="delSchModal" tabindex="-1" role="dialog" aria-labelledby="delScheduledModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="dialog">
		<div class="modal-content">
			<div class="modal-header justify-content-center font-weight-bold bg-primary text-white" id="del-shceduled-header">Delete scheduled</div>
			<div class="modal-body edit-content">
				<form role="form" name="form_del_scheduled" action="<?=_MAIN_DF;?>scheduled" method="post">
					<div class="form-row">
						<div class="col-md-12" id="id_for_del"></div>
					</div>
					<div class="form-row mt-3">	
						<div class="col-md-12 text-center">
							<div class="form-row form-inline justify-content-center">
								<div class="col-md-12" id="id_for_del"></div>
							</div>
							<div class="form-row form-inline justify-content-center">
								<input type="hidden" name="del_scheduled_id" id="del_scheduled_id" value="" />
								<input type="hidden" name="<?=_DEST_FILE;?>" value="<?=$dest_file."&Sid=".$select_msisdn;?>" />
								<input type="hidden" name="<?=_TXT_SEARCH;?>" value="<?=$_POST[_TXT_SEARCH];?>" />
								<input type="hidden" name="select_page" value="<?=$select_page;?>">
								<input type="hidden" name="max_rows" value="-1">
								<input type="hidden" name="bypass" value="0">
								<button type="submit" class="btn btn-primary mx-1" id="btn-submit">Confirm Delete</button>
								<button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
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
$('#delSchModal').on('show.bs.modal', function(e) {
	document.getElementById("del_scheduled_id").value=e.relatedTarget.id;
	document.getElementById("id_for_del").innerHTML="Do you want to delete scheduled ID:"+e.relatedTarget.id+" ?";
});
</script>