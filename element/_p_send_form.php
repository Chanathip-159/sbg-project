<?php
if (isset($_POST['t_msisdn']) && isset($_POST['t_amount'])) {
	// receive $_POST 
	$_GET['toast_type'] = "S";
	$_GET['toast_header'] = "Result message";
	$_GET['toast_message'] = "Please, wait for the SMS confirmation.";
}
?>
			<div class="row col-md-12 justify-content-right"><!-- # ทดสอบภาษาไทย -->
				<div class="col-md-7">
					<div class="card mr-2">
						<div class="card-header bg-primary text-white">
							<div class="row">
								<div class="col"><h4>Contacts</h4></div>
								<div class="col text-right">
									<h4>
									<?php
									$sql = "SELECT COUNT(*) FROM [SBG].[dbo].[GROUPS] WHERE [GROUP_Username] = ?";
									echo "(".querySqlSingleFieldEx($sql, [$_SESSION[_SES_USR_NAME]]).")";
									?>
									</h4>
								</div>
							</div>
						</div>
						<div class="card-body">
							<?php 
							$max_rows = 5;
							require_once('_p_send_contacts_form_table.php');
							?>
						</div>
					</div>
				</div>
				<div class="col-md-5" id="save_draft_of_topup_list">
					<div class="card ml-2">
						<form class="d-md-inline-block form-inline" name="formAdd5" method="post" action="<?=_MAIN_DF;?>by_contacts" onsubmit="">
							<div class="card-header bg-primary text-white">
								<div class="row">
									<div class="col">
										<h4>Group list</h4>
									</div>
									<div class="col text-right" id="topup_count_down">
										<h4></h4>
									</div>
								</div>
							</div>
							<div class="card-body justify-content-center" id="phone_number_list">
								<div id="dummy_topup" style="display: block;">
									<div class="row justify-content-center">
										<div class="card mb-2 bg-warning text-dark" id="card_container">
											<div class="card-body shadow form-group form-inline p-2 justify-content-center" style="width: 22rem;">
												<div class="ml-3 form-inline">
													<a class="font-weight-bold font-italic mr-2 class-group-count" onclick="addTopupNumberToTopupList('');">Click <i class="fa fa-fw fa-plus-circle"></i> for add group to send SMS</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer text-center">
								<button type="button" class="btn btn-primary" onclick="return validateMsisdnAmount();">Send</button>
								<button type="reset" class="btn btn-default" onclick="clearListAll();">Clear</button>
							</div>
						</form>
					</div>
				</div>
			</div>
<!-- Confirm Modal-->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header font-weight-bold" id="confirmModalHeader">
				<h5 class="modal-title" id="confirmModalHeaderText"></h5>
			</div>
			<div class="modal-body" id="confirmModalBody">
				<h6 id="confirmModalBodyText"><h6>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" type="submit" id="confirmModalConfirmButton">Confirm</button>
				<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var topup_list_max_number = 10;
var topup_list_counter = 0;

$('#confirmModalConfirmButton').click(function(e){
      e.preventDefault();
	  localStorage.removeItem('save_topup_draft');
	  formAdd5.submit();
});

var draft = localStorage.getItem('save_topup_draft');
if (draft != null) {
	document.getElementById("save_topup_draft").innerHTML = draft;
}

function validateMsisdnAmount() {
	var numbers = /^[0-9]+$/;
	var list_of_msisdn_topup = "";
	var msisdns_topup = document.querySelectorAll('.class-check-msisdn'), amounts_topup = document.querySelectorAll('.class-check-amount'), i;
	for (i = 0; i < msisdns_topup.length; ++i) {
		msisdn_topup = msisdns_topup[i].value; amount_topup = amounts_topup[i].value;
		if (!numbers.test(msisdn_topup) || msisdn_topup.length <10 || msisdn_topup.length >11 || 
			(msisdn_topup.length==11 && msisdn_topup.substring(0, 2) != '66')||
			(msisdn_topup.length==10 && msisdn_topup.substring(0, 1) != '0')
			){
			document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
			document.getElementById("informModalHeaderText").innerHTML = "Alert";
			if (msisdn_topup.length>0) {
				document.getElementById("informModalBodyText").innerHTML = "MSISDN ["+msisdn_topup+"] is incorrect format";
			}else{
				document.getElementById("informModalBodyText").innerHTML = "Some MSISDN is empty";
			}
			$("#informModal").modal("show");
			return false;
		}
		if (!numbers.test(amount_topup)){
			document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
			document.getElementById("informModalHeaderText").innerHTML = "Alert";
			if (amount_topup.length>0) {
				document.getElementById("informModalBodyText").innerHTML = "Amount ["+amount_topup+"] is incorrect format";
			}else{
				document.getElementById("informModalBodyText").innerHTML = "Some Amount is empty";
			}
			$("#informModal").modal("show");
			return false;
		}
		
		list_of_msisdn_topup = list_of_msisdn_topup+(i+1)+") Topup to "+msisdn_topup+" with "+amount_topup+" baht<BR />";
	}
	if (list_of_msisdn_topup.length == 0) {
		document.getElementById("confirmModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
		document.getElementById("confirmModalHeaderText").innerHTML = "Alert";
		//document.getElementById("confirmModalBody").className = 'modal-body';
		document.getElementById("confirmModalBodyText").innerHTML = "MSISDN is empty";
		$('#confirmModal').modal('show');
		return false;
	}
	document.getElementById("confirmModalHeader").className = 'modal-header font-weight-bold bg-success text-white';
	document.getElementById("confirmModalHeaderText").innerHTML = "Confirm";
	//document.getElementById("confirmModalBody").className = 'modal-body';
	document.getElementById("confirmModalBodyText").innerHTML = "Do you want to Topup below ?<BR />"+list_of_msisdn_topup;
	$('#confirmModal').modal('show');
	return true;
}

var addTopupNumberToTopupListWithNumber = function (p_num){
	var disable = "";
	if (p_num>0) {
		disable = ' readonly="readonly"';
	}
	var row_index = $('<div id="index_topup"></div>');
	var row_container = $('<div class="row justify-content-center"></div>');
	var current_number = $('<div class="card mb-2 bg-warning" id="card_container" style="width: 20rem;"></div>');
	var phone_number_list = $('<div id="'+p_num+'_inside"><div class="card-body shadow form-group p-2 justify-content-between"><input type="text" id="t_msisdn[]" name="t_msisdn[]" class="form-control class-check-msisdn" style="width: 10rem;" value="'+p_num+'" placeholder="ex: 66864000000"'+disable+'> <input type="text" id="t_amount[]" name="t_amount[]" class="form-control class-check-amount" style="width: 6rem;" placeholder="ex: 100"> <button id="'+p_num+'" type="button" class="close" data-target="#'+p_num+'_inside"><span class="float-right"><i class="fa fa-times-circle"></i></span></button></div></div>');
	phone_number_list.appendTo(current_number);
	current_number.appendTo(row_container);
	row_container.appendTo(row_index);
	row_index.appendTo('#phone_number_list');
	$('.close').on('click', function(e){
		e.stopPropagation();  
		var $target = $(this).parents('#index_topup');
		$target.hide('slow', function(){ 
			$target.remove();
			updateTopupListCounter();
		});
    });
};

function clearListAll () {
	var ccs = document.querySelectorAll('#index_topup'), i;
	for (i = 0; i < ccs.length; ++i) {
		var contentToRemove = ccs[i];
		$(contentToRemove).hide(Math.floor((Math.random() * 400) + 300), function(){ 
			$(this).remove();
			updateTopupListCounter();
		});
	}
	localStorage.removeItem('save_topup_draft');
}

function addTopupNumberToTopupList (msisdn) {
	if (topup_list_counter < topup_list_max_number) {
		var dup = false;
		var msisdns = document.querySelectorAll('.class-check-msisdn'), i;
		for (i = 0; i < msisdns.length;  i++) {
			if (msisdns[i].value == msisdn) dup = true;
		}
		if (dup) {
			if (msisdn.length == 0) {
				document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-warning text-dark';
				document.getElementById("informModalHeaderText").innerHTML = "Warning";
				//document.getElementById("informModalBody").className = 'modal-body';
				document.getElementById("informModalBodyText").innerHTML = "Please use the empty input before add the new one";
			}else{
				document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
				document.getElementById("informModalHeaderText").innerHTML = "Alert";
				document.getElementById("informModalBodyText").innerHTML = "Duplicate phone number";
			}
			$("#informModal").modal("show");
		}else{
			addTopupNumberToTopupListWithNumber(msisdn);
			updateTopupListCounter();
		}
	}else{
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
		document.getElementById("informModalHeaderText").innerHTML = "Alert";
		document.getElementById("informModalBodyText").innerHTML = "Maximum phone number";
		$("#informModal").modal("show");
	}
}
function updateTopupListCounter () {
	topup_list_counter = document.querySelectorAll('.class-check-msisdn').length;
	if (topup_list_counter == 0) {
		document.getElementById("dummy_topup").style.display = "block";
	}else{
		document.getElementById("dummy_topup").style.display = "none";
	}
	document.getElementById("topup_count_down").innerHTML = "<h4>("+topup_list_counter+"/"+topup_list_max_number+")</h4>";
}
updateTopupListCounter();
</script>