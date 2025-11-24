<?php
require_once(__DIR__.'/../../php.lib/function.basic.php');
require_once(__DIR__.'/../../php.lib/api.sms.send.direct.php');
require_once(__DIR__.'/../api/bulk.api.engine.php');

$taskName = date('YmdHi');

$_POST['test_msisdn'] = convert2ThaiPhoneNumber($_POST['test_msisdn']);
if (strlen($_POST['sms_sender_name']) && strlen($_POST['sms_text']) && strlen($_POST['lang_type']) && strlen($_POST['test_msisdn'])) {
	if ($_SESSION[_SES_USR_TYP_ID]<=_ADMIN_L) {
		$send_data = Array();
		// $send_data['sms_client_ip'] = getClientIp();
		$send_data['sms_client_ip'] = $_POST['sms_task_name'] . '|' . getClientIp(); // message in UTF-8
		$send_data['sms_type'] = "submit"; 
		$send_data['sms_service_type'] = _SMS_SERVICE_TYPE;
		$send_data['sms_charge_account'] = $user;
		$send_data['sms_username_sub']=$_SESSION[_SES_USR_NAME];
		$send_data['sms_sender'] = $_POST['sms_sender_name'];
		$send_data['sms_receiver'] = $_POST['test_msisdn'];
		if (strlen($_POST['exp_y_m']) && strlen($_POST['exp_day']) && strlen($_POST['exp_h']) && strlen($_POST['exp_i'])) {
			$send_data['sms_validity_period'] = $_POST['exp_y_m'].$_POST['exp_day']." ".$_POST['exp_h'].":".$_POST['exp_i'];
		}
		$send_data['sms_delivery_report'] = $_SESSION['session_need_dr']; // 0 = no need dr sms; 1 = need dr sms
		$send_data['sms_langauge'] = $_POST['lang_type'];
		$send_data['sms_message'] = $_POST['sms_text'];
		
		if (strlen($_POST['sch_y_m']) && strlen($_POST['sch_day']) && strlen($_POST['sch_h']) && strlen($_POST['sch_i'])) {
			$send_data['scheduled_delivery_id'] = genTransId(30);
			$send_data['scheduled_delivery_dt'] = $_POST['sch_y_m'].$_POST['sch_day']." ".$_POST['sch_h'].":".$_POST['sch_i'];
		}
		$send_test_result = sendSmsToDb($send_data);
	}else{
		if (strlen($_POST['exp_y_m']) && strlen($_POST['exp_day']) && strlen($_POST['exp_h']) && strlen($_POST['exp_i'])) {
			$sms_validity_period = $_POST['exp_y_m'].$_POST['exp_day']." ".$_POST['exp_h'].":".$_POST['exp_i'];
		}
		$send_test_result = sendSMSEngine($_SESSION[_SES_USR_NAME],$_SESSION['session_hash_password'],$_POST['sms_sender_name'],$_POST['test_msisdn'],$_POST['sms_text'],$_POST['lang_type'],$sms_validity_period);
	}
}

if (strlen($_POST['sms_sender_name']) && strlen($_POST['sms_text']) && strlen($_POST['lang_type']) && @strlen($_FILES['inputGroupFile01']['tmp_name'])) {
	if ($_FILES['inputGroupFile01']['error'] === UPLOAD_ERR_OK) {
		$send_fail = Array();
		if ($_SESSION[_SES_USR_TYP_ID]<=_ADMIN_L) {
			$send_data = Array();
			// $send_data['sms_client_ip'] = getClientIp();
			$send_data['sms_client_ip'] = $_POST['sms_task_name'] . '|' . getClientIp(); // message in UTF-8
			$send_data['sms_type'] = "submit"; 
			$send_data['sms_service_type'] = _SMS_SERVICE_TYPE;
			$send_data['sms_charge_account'] = $_SESSION[_SES_USR_NAME];
			$send_data['sms_sender'] = $_POST['sms_sender_name'];
			if (strlen($_POST['exp_y_m']) && strlen($_POST['exp_day']) && strlen($_POST['exp_h']) && strlen($_POST['exp_i'])) {
				$send_data['sms_validity_period'] = $_POST['exp_y_m'].$_POST['exp_day']." ".$_POST['exp_h'].":".$_POST['exp_i'];
			}
			$send_data['sms_delivery_report'] = $_SESSION['session_need_dr']; // 0 = no need dr sms; 1 = need dr sms
			$send_data['sms_langauge'] = $_POST['lang_type'];
			$send_data['sms_message'] = $_POST['sms_text'];
			
			if (strlen($_POST['sch_y_m']) && strlen($_POST['sch_day']) && strlen($_POST['sch_h']) && strlen($_POST['sch_i'])) {
				$send_data['scheduled_delivery_id'] = genTransId(30);
				$send_data['scheduled_delivery_dt'] = $_POST['sch_y_m'].$_POST['sch_day']." ".$_POST['sch_h'].":".$_POST['sch_i'];
			}
		}else{
			if (strlen($_POST['exp_y_m']) && strlen($_POST['exp_day']) && strlen($_POST['exp_h']) && strlen($_POST['exp_i'])) {
				$sms_validity_period = $_POST['exp_y_m'].$_POST['exp_day']." ".$_POST['exp_h'].":".$_POST['exp_i'];
			}
		}
		
		$fileSize = $_FILES['inputGroupFile01']['size']; // bytes
		$raw_data = file_get_contents($_FILES['inputGroupFile01']['tmp_name']);
		$msisdns = explode("\n",str_replace("\r","",$raw_data));
		if (count($msisdns) > _MAX_FILE_ROWS) {
			$_GET['toast_type'] = "F";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "This file is too large";
		}else{
			$validate = true;
			for($i=0; $i<count($msisdns); $i++) {
				$temp = $msisdns[$i];
				$msisdn = convert2ThaiPhoneNumber($msisdns[$i]);
				if ($msisdn) {
					$msisdns[$i] = $msisdn;
				}else{
					$validate = false;
					$invalid[] = " - line $i -> $temp";
				}
			}
			if ($validate) {
				$msisdns_max = array_chunk($msisdns,_MAX_PER_TIME);
				foreach($msisdns_max AS $msisdnss) {
					if ($_SESSION[_SES_USR_TYP_ID]<=_ADMIN_L) {
						$send_data['sms_receiver'] = $msisdnss[0];
						$send_data['sms_destination_addr'] = $msisdnss;
						$send_file_result = sendSmsToDb($send_data);
						if ($send_file_result['error_code'] != 0) {
							$send_fail[] = "Can not send SMS (".$msisdnss[0]." - ".$msisdnss[count($msisdnss)-1].") [".$send_file_result['error_code']."]";
						}
					}else{
						$send_file_result = sendSMSEngine($_SESSION[_SES_USR_NAME],$_SESSION['session_hash_password'],$_POST['sms_sender_name'],implode(",",$msisdnss),$_POST['sms_text'],$_POST['lang_type'],$sms_validity_period);
						if ($send_file_result['error_code'] != 0) {
							if (isset($send_file_result['fails'])&&count($send_file_result['fails'])) {
								$send_fail[] = "Can not send SMS (".$msisdnss[0]." - ".$msisdnss[count($msisdnss)-1].") [".implode(",",$send_file_result['fails'])."]";
							}else{
								$send_fail[] = "Can not send SMS (".$msisdnss[0]." - ".$msisdnss[count($msisdnss)-1].") [".$send_file_result['error_code']."]";
							}
						}
					}
				}
				$send_fail_count = @count($send_fail);
				if ($send_fail_count>10) {
					$send_fail_short[] = $send_fail[0];
					$send_fail_short[] = $send_fail[1];
					$send_fail_short[] = $send_fail[2];
					$send_fail_short[] = ".";
					$send_fail_short[] = ".";
					$send_fail_short[] = ".";
					$send_fail_short[] = $send_fail[$send_fail_count-1];
				}else{
					$send_fail_short = $send_fail;
				}
			}else{
				$_GET['toast_type'] = "F";
				$_GET['toast_header'] = "Result message";
				if (count($invalid)<=10) {
					$_GET['toast_message'] = "Invalid msisdn format of item below:<BR/>".implode("<BR>",$invalid);
				}else{
					$_GET['toast_message'] = "Invalid msisdn format ".count($invalid)." items Ex:<BR/>".$invalid[0]."<BR/>".$invalid[1]."<BR/>".$invalid[2]."<BR/>.<BR/>.<BR/>.<BR/>".$invalid[count($invalid)-1];
				}
			}
		}
	}else{
		$_GET['toast_type'] = "F";
		$_GET['toast_header'] = "Result message";
		$_GET['toast_message'] = "Upload file failed";
	}
}

if (!strlen($_GET['toast_type']) && !strlen($_GET['toast_header']) && !strlen($_GET['toast_message'])) {
	if (strlen($_POST['test_msisdn']) && @strlen($_FILES['inputGroupFile01']['tmp_name'])) {
		$send_fail_check = @count($send_fail);
		if ($send_test_result['error_code']==0 && !$send_fail_check) {
			$_GET['toast_type'] = "S";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "Server received data [".@implode(",",$send_test_result['transaction_ids'])."] and file [".$_FILES['inputGroupFile01']['name']."]";
		} else if ($send_test_result['error_code']==0) {
			$_GET['toast_type'] = "W";
			$_GET['toast_header'] = "Warning";
			$_GET['toast_message'] = "Server received data [".@implode(",",$send_test_result['transaction_ids'])."]<BR.>".@implode("<BR/>",$send_fail_short);
		} else if ($send_fail_check) {
			$_GET['toast_type'] = "W";
			$_GET['toast_header'] = "Warning";
			$_GET['toast_message'] = "Can not send SMS [".$send_test_result['error_code']."] ".$send_test_result['error']." but received file [".$_FILES['inputGroupFile01']['name']."]";
		}else{
			$_GET['toast_type'] = "F";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "Can not send SMS [".$send_test_result['error_code']."] ".$send_test_result['error']."<BR.>".@implode("<BR/>",$send_fail_short);
		}
	} else if (strlen($_POST['test_msisdn'])) {
		if ($send_test_result['error_code']==0) {
			$_GET['toast_type'] = "S";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "Server received data [".@implode(",",$send_test_result['transaction_ids'])."]";
		}else{
			$_GET['toast_type'] = "F";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "Can not send SMS [".$send_test_result['error_code']."] ".$send_test_result['error'];
		}
	} else if (@strlen($_FILES['inputGroupFile01']['tmp_name'])) {
		if ($send_file_result['error_code']==0) {
			$_GET['toast_type'] = "S";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "Server received file [".$_FILES['inputGroupFile01']['name']."]";
		}else{
			$_GET['toast_type'] = "F";
			$_GET['toast_header'] = "Result message";
			$_GET['toast_message'] = "Can not send SMS to file [".$_FILES['inputGroupFile01']['name']."] [".$send_file_result['error_code']."] ".$send_file_result['error'];
		}
	}
}
?>
			<div class="row col-md-12 justify-content-center"><!-- # ทดสอบภาษาไทย -->
				<div class="col-md-8">
					<div class="card ml-2 mb-4">
						<form class="d-md-inline-block form-inline" name="formAdd5" method="post" action="<?=_MAIN_DF."by_file";?>" onsubmit="" enctype="multipart/form-data">
							<div class="card-header bg-primary text-white">
								<div class="row">
									<div class="col">
										<h4>Send form</h4>
									</div>
									<div class="col text-right" id="province_counting">
										<h4></h4>
									</div>
								</div>
							</div>
							<div class="card-body justify-content-center" id="province_list">
							<div class="form-group mb-0 p-0 justify-content-center">
								<label class="form-control-label mb-0">Task name * (up to 9 digits a-Z, 0-9 and @ ! # _ - . )</label>
							</div>
							<div class="form-group mb-4 p-0 justify-content-center">
								<input class="form-control" name="sms_task_name" id="sms_task_name" value="<?php echo $taskName; ?>" maxlength="20"/>
							</div>
							<?php
								if ($_SESSION[_SES_USR_TYP_ID]<=_ADMIN_L) {
							?>
								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0">SMS Sender Name * (up to 9 digits a-Z,0-9 and @ ! # _ - . )</label>
								</div>
								<div class="form-group mb-4 p-0 justify-content-center">
									<input class="form-control" name="sms_sender_name" id="sms_sender_name" value="" />
								</div>
							<?php }else{?>
								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0">Select Sender Name</label>
								</div>
								<div class="form-group mb-4 p-0 justify-content-center">
									<select name="sms_sender_name" id="sms_sender_name" class="custom-select ml-2 mr-1">
										<?php
										$default = querySqlSingleFieldEx("SELECT CUSTOMER_Sender FROM SBG.dbo.CUSTOMERS WHERE CUSTOMER_Username=?",[$_SESSION[_SES_USR_NAME]]);
										?>
										<?php
											$stmt=querySqlEx("SELECT SENDER_Sender FROM SBG.dbo.SENDERS WHERE SENDER_Status = 1 AND SENDER_Username=?",[$_SESSION[_SES_USR_NAME]]);
											$count = 0;
											while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) 
											{
												$count++;
												echo "<option value='".$row['SENDER_Sender']."'>".$row['SENDER_Sender']."</option>";
											}
											if($count == 0)
											{
												echo "<option readonly>* no sender</option>";
											}
										?>
									</select>
								</div>
							<?php }?>
								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0" id="text_counter">SMS Text *</label>
									<input type="hidden" id="lang_type" name="lang_type" />
								</div>
								<div class="form-group mb-4 p-0 justify-content-center">
									<textarea class="form-control" rows="3" maxlength="500" name="sms_text" id="sms_text" style="width: 32rem;" onkeyup="text_counting('sms_text','text_counter')"></textarea>
								</div>
								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0">Test number (Ex:66864600000)</label>
								</div>
								<div class="form-group mb-4 p-0 justify-content-center">
									<input class="form-control" name="test_msisdn" id="test_msisdn" value="" />
								</div>
<!--								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0">Scheduled datetime (YYYY-MM-DD hh:mm)</label>
								</div>
-->								
<script>/*
function setScheduledSend() {
	if (document.getElementById("sch_y_m").selectedIndex == 0) {
		document.getElementById("sch_day").disabled=true;
		document.getElementById("sch_h").disabled=true;
		document.getElementById("sch_i").disabled=true;
	}else{
		document.getElementById("sch_day").disabled=false;
		document.getElementById("sch_h").disabled=false;
		document.getElementById("sch_i").disabled=false;
	}
}//*/
function setExpireSend() {
	if (document.getElementById("exp_y_m").selectedIndex == 0) {
		document.getElementById("exp_day").disabled=true;
		document.getElementById("exp_h").disabled=true;
		document.getElementById("exp_i").disabled=true;
	}else{
		document.getElementById("exp_day").disabled=false;
		document.getElementById("exp_h").disabled=false;
		document.getElementById("exp_i").disabled=false;
	}
}
</script>
<!--								<div class="form-group mb-4 p-0 justify-content-center">
									<select name="sch_y_m" id="sch_y_m" class="custom-select ml-2 mr-1" onchange="setScheduledSend();">
										<option value="">Not set</option>
										<option value="<?=date("Y-m-")?>"><?=date("Y-m")?></option>
										<?php if (date("Y-m",strtotime("+15 days")) != date("Y-m")) {?><option value="<?=date("Y-m-",strtotime("+15 days"))?>"><?=date("Y-m",strtotime("+15 days"))?></option><?php }?>
									</select>-
									<select name="sch_day" id="sch_day" class="custom-select ml-1 mr-4" disabled>
										<?php
										$max_date_store = strtotime("+15 day");
										$current_date_store = time();
										while($max_date_store > $current_date_store) {
											?>
											<option value="<?=date("d",$current_date_store)?>"><?=date("d",$current_date_store)?></option>
											<?php
											$current_date_store += 86400;
										}
										?>
									</select>
									<select name="sch_h" id="sch_h" class="custom-select ml-2 mr-2" disabled>
										<?php
										for($i=0; $i< 24; $i++) {
											?>
											<option value="<?=str_pad($i,2,"0",STR_PAD_LEFT)?>"><?=str_pad($i,2,"0",STR_PAD_LEFT)?></option>
											<?php
										}
										?>
									</select>:
									<select name="sch_i" id="sch_i" class="custom-select ml-2 mr-2" disabled>
										<?php
										for($i=0; $i< 60; $i+=5) {
											?>
											<option value="<?=str_pad($i,2,"0",STR_PAD_LEFT)?>"><?=str_pad($i,2,"0",STR_PAD_LEFT)?></option>
											<?php
										}
										?>
									</select>
								</div>-->
								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0">Expire datetime (48 hours)</label>
								</div>
								<div class="form-group mb-4 p-0 justify-content-center">
									<select name="exp_y_m" id="exp_y_m" class="custom-select ml-2 mr-1" onchange="setExpireSend();">
										<option value="">Not set</option>
										<option value="<?=date("Y-m-")?>"><?=date("Y-m")?></option>
										<?php if (date("Y-m",strtotime("+15 days")) != date("Y-m")) {?><option value="<?=date("Y-m-",strtotime("+15 days"))?>"><?=date("Y-m",strtotime("+15 days"))?></option><?php }?>
									</select>-
									<select name="exp_day" id="exp_day" class="custom-select ml-1 mr-4" disabled>
										<?php
										$max_date_store = strtotime("+15 day");
										$current_date_store = time();
										while($max_date_store > $current_date_store) {
											?>
											<option value="<?=date("d",$current_date_store)?>"><?=date("d",$current_date_store)?></option>
											<?php
											$current_date_store += 86400;
										}
										?>
									</select>
									<select name="exp_h" id="exp_h" class="custom-select ml-2 mr-2" disabled>
										<?php
										for($i=0; $i< 24; $i++) {
											?>
											<option value="<?=str_pad($i,2,"0",STR_PAD_LEFT)?>"><?=str_pad($i,2,"0",STR_PAD_LEFT)?></option>
											<?php
										}
										?>
									</select>:
									<select name="exp_i" id="exp_i" class="custom-select ml-2 mr-2" disabled>
										<?php
										for($i=0; $i< 60; $i+=5) {
											?>
											<option value="<?=str_pad($i,2,"0",STR_PAD_LEFT)?>"><?=str_pad($i,2,"0",STR_PAD_LEFT)?></option>
											<?php
										}
										?>
									</select>
								</div>
								<div class="form-group mb-0 p-0 justify-content-center">
									<label class="form-control-label mb-0">File (maximum 100,000 numbers)</label>
								</div>
								<div class="input-group mb-2 p-0">
									<div class="custom-file">
										<input type="file" class="custom-file-input" id="inputGroupFile01" name="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
										<label class="custom-file-label" for="inputGroupFile01" id="labelGroupFile01">Choose file</label>
									</div>
								</div>
								<div class="form-group mb-1 p-0 justify-content-center">
									<label class="form-control-label mb-0 small">File format example :</label>
								</div>
								<div class="form-group mb-2 p-0 justify-content-center"><img src="img/file_format.png" /></div>
							</div>
							<div class="card-footer text-center">
								<button type="button" class="btn btn-primary" onclick="return sendSmsToFile();">Send</button>
								<button type="reset" class="btn btn-default">Clear</button>
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

var province_list_counter = 0;

$('#confirmModalConfirmButton').click(function(e){
      e.preventDefault();
	  formAdd5.submit();
});

$('#inputGroupFile01').on('change',function(){
	var fileName = $(this).val();
	$(this).next('.custom-file-label').html(fileName);
})

function text_counting(text,label) {
	var sms_text = document.getElementById(text);
	if (checkThai(text)) { // check thai
		if (sms_text.value.length <= 70) {
			document.getElementById(label).innerHTML = "SMS Text * ("+Math.ceil(sms_text.value.length/70)+" SMS) Char:"+sms_text.value.length;
		}else{
			document.getElementById(label).innerHTML = "SMS Text * ("+Math.ceil(sms_text.value.length/67)+" SMS) Char:"+sms_text.value.length;
		}
		document.getElementById("lang_type").value = "T";
	}else{
		document.getElementById(label).innerHTML = "SMS Text * ("+Math.ceil(sms_text.value.length/140)+" SMS) Char:"+sms_text.value.length;
		document.getElementById("lang_type").value = "E";
	}
}
function checkThai(text) {
	var txt = document.getElementById(text).value;
	for(var i = 0;i < txt.length; i++) {
		if( (txt.charCodeAt(i) > 127) || (txt.charCodeAt(i)==94) || (txt.charCodeAt(i)==92) ) { 
			return true;
		}
	}
	return false;
}

function sendSmsToFile() {
	var numbers = /^[0-9]+$/;
	var list_msisdn = /^[0-9,]+$/;
	
	var test_msisdn = document.getElementById("test_msisdn");

	if (test_msisdn.value != "") {
		if (!numbers.test(test_msisdn.value) || test_msisdn.value.length<10 || test_msisdn.value.length>11) {
			document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
			document.getElementById("informModalHeaderText").innerHTML = "Alert";
			document.getElementById("informModalBodyText").innerHTML = "Test number is incorrect format";
			$('#informModal').modal('show');
			test_msisdn.focus();
			return false;
		}
		
	}
	
	var sms_text = document.getElementById("sms_text");
	if (sms_text.value.length == 0) {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
		document.getElementById("informModalHeaderText").innerHTML = "Alert";
		document.getElementById("informModalBodyText").innerHTML = "SMS Text is Empty";
		$('#informModal').modal('show');
		sms_text.focus();
		return false;
	}
	var sms_sender_name = document.getElementById("sms_sender_name");
	if (sms_sender_name.value.length == 0) {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
		document.getElementById("informModalHeaderText").innerHTML = "Alert";
		document.getElementById("informModalBodyText").innerHTML = "SMS Sender Name is Empty";
		$('#informModal').modal('show');
		sms_sender_name.focus();
		return false;
	}
	if (sms_sender_name.value.length > 20) {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
		document.getElementById("informModalHeaderText").innerHTML = "Alert";
		document.getElementById("informModalBodyText").innerHTML = "SMS Sender Name is too long";
		$('#informModal').modal('show');
		sms_sender_name.focus();
		return false;
	}
	var list_of_send = "",l1=false;
	if (test_msisdn.value.length > 0) {
		l1 = true;
		list_of_send = "1) Send SMS to "+test_msisdn.value;
	}
	if (l1) {
		if (document.getElementById("labelGroupFile01").innerHTML.length && document.getElementById("labelGroupFile01").innerHTML != "Choose file") {
			list_of_send = list_of_send+"<BR />2) Send SMS to "+document.getElementById("labelGroupFile01").innerHTML+"<BR/>It take a few seconds to processing depend on file size";
		}
	}else{
		if (document.getElementById("labelGroupFile01").innerHTML.length && document.getElementById("labelGroupFile01").innerHTML != "Choose file") {
			list_of_send = "1) Send SMS to "+document.getElementById("labelGroupFile01").innerHTML+"<BR/>It take a few seconds to processing depend on file size";
		}
	}
	document.getElementById("confirmModalHeader").className = 'modal-header font-weight-bold bg-success text-white';
	document.getElementById("confirmModalHeaderText").innerHTML = "Confirm";
	//document.getElementById("confirmModalBody").className = 'modal-body';
	document.getElementById("confirmModalBodyText").innerHTML = "Do you want to send SMS as below ?<BR />"+list_of_send;
	$('#confirmModal').modal('show');
	return true;
}
</script>