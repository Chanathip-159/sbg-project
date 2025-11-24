<?php
if (strlen($_POST['group_id'])) {
	if (validateNumber($_POST['group_id'])) {
		$g_id = $_POST['group_id'];
		if ($_POST['group-delete-'.$g_id] == "1") {
			// delete
			$sql = "DELETE FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Id] = ?; DELETE FROM [ITOP].[dbo].[GROUPMAP] WHERE [GROUPMAP_Id] = ?;";
			if ($stmt = querySqlEx($sql, [$g_id,$g_id])) {
				$_GET['toast_type']="S";
				$_GET['toast_header']="Result";
				$_GET['toast_message']="Delete [".$_POST['group_ori_name']."] successfully";
			}else{
				$_GET['toast_type']="E";
				$_GET['toast_header']="Alert";
				$_GET['toast_message']="Delete [".$_POST['group_ori_name']."] fail";
			}
		}else{
			if (strlen($_POST['input-'.$g_id])>_GROUP_NAME_LENGTH) {
				// max length
				$_GET['toast_type']="E";
				$_GET['toast_header']="Error";
				$_GET['toast_message']="Name can not over "._GROUP_NAME_LENGTH." characters";
			}else{
				if ($_POST['input-'.$g_id] == $_POST['group_ori_name']) {
					// dup
					$_GET['toast_type']="W";
					$_GET['toast_header']="Warning";
					$_GET['toast_message']="Same name, no update";
				}else{
					// update
					$sql = "UPDATE [ITOP].[dbo].[GROUPINFO] SET [GROUPINFO_Name] = ? WHERE [GROUPINFO_Id] = ?";
					if ($stmt = querySqlEx($sql, [$_POST['input-'.$g_id],$g_id])) {
						$_GET['toast_type']="S";
						$_GET['toast_header']="Result";
						$_GET['toast_message']="Change [".$_POST['group_ori_name']."] to [".$_POST['input-'.$g_id]."] successfully";
					}else{
						$_GET['toast_type']="E";
						$_GET['toast_header']="Alert";
						$_GET['toast_message']="Update [".$_POST['group_ori_name']."] fail";
					}
				}
			}
		}
	}else if (substr($_POST['group_id'], 0, 4) == "new-") {
		// insert
		$g_id = substr($_POST['group_id'], 4);
		if (validateNumber($g_id)) {
			$sql = "SELECT COUNT(*) FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?";
			$total_group = querySqlSingleFieldEx($sql, [$_SESSION[_SES_USR_NAME]]);
			if ($total_group >= _GROUP_NUM) {
				$_GET['toast_type']="W";
				$_GET['toast_header']="Warning";
				$_GET['toast_message']="Can not add new group more than "._GROUP_NUM;
			}else{
				if (strlen($_POST['input-new-'.$g_id])>_GROUP_NAME_LENGTH) {
					// max length
					$_GET['toast_type']="E";
					$_GET['toast_header']="Error";
					$_GET['toast_message']="Name can not over "._GROUP_NAME_LENGTH." characters";
				}else{
					$sql = "INSERT INTO [ITOP].[dbo].[GROUPINFO] ([GROUPINFO_Msisdn],[GROUPINFO_Name]) VALUES (?,?)";
					if ($stmt = querySqlEx($sql, [$_SESSION[_SES_USR_NAME], $_POST['input-new-'.$g_id]])) {
						$_GET['toast_type']="S";
						$_GET['toast_header']="Result";
						$_GET['toast_message']="Add group [".$_POST['input-new-'.$g_id]."] successfully";
					}else{
						$_GET['toast_type']="E";
						$_GET['toast_header']="Alert";
						$_GET['toast_message']="Add group [".$_POST['input-new-'.$g_id]."] fail";
					}
				}
			}
		}else{
			$_GET['toast_type']="E";
			$_GET['toast_header']="Alert";
			$_GET['toast_message']="Incorrect group id";
		}
	}else{
		// unknow
		$_GET['toast_type']="E";
		$_GET['toast_header']="Alert";
		$_GET['toast_message']="Internal error";
	}
} else if (strlen($_POST['delete-id'])>0 && strlen($_POST['delete-msisdn'])>0) {
	$group_id = $_POST['delete-id'];
	$msisdn = $_POST['delete-msisdn'];
	$verify1 = validateNumber($group_id);
	$verify2 = validatePhoneNumber($msisdn);
	$msisdn = convert2ThaiPhoneNumber($msisdn);
	if (strlen($msisdn) && $verify1 && $verify2) {
		$sql = "DELETE FROM [ITOP].[dbo].[GROUPMAP] WHERE [GROUPMAP_Id] = ? AND [GROUPMAP_Msisdn] = ?";
		if ($stmt = querySqlEx($sql, [$group_id, $msisdn])) {
			$_GET['toast_type']="S";
			$_GET['toast_header']="Result";
			$_GET['toast_message']="Detete contact [$msisdn] successfully";
		}else{
			$_GET['toast_type']="E";
			$_GET['toast_header']="Alert";
			$_GET['toast_message']="Delete contact [$msisdn] fail";
		}
	}else{
		$_GET['toast_type']="E";
		$_GET['toast_header']="Alert";
		$_GET['toast_message']="Incorrect parameter";
	}
} else if (strlen($_POST['edit_contact'])>0 && $_POST['edit_contact']==1 && strlen($_POST['new_contact'])==0) {
	$group_id = $_POST['GROUPINFO_Id'];
	$msisdn = $_POST['GROUPMAP_Msisdn'];
	
	$edit_group_id = $_POST['edit_contact_id'];
	$edit_msisdn = $_POST['edit_contact_msisdn'];
	
	$verify1 = validateNumber($group_id);
	$verify2 = validatePhoneNumber($msisdn);
	$msisdn = convert2ThaiPhoneNumber($msisdn);
	
	$verify3 = validateNumber($edit_group_id);
	$verify4 = validatePhoneNumber($edit_msisdn);
	$edit_msisdn = convert2ThaiPhoneNumber($edit_msisdn);
	
	if (strlen($msisdn) && $verify1 && $verify2 && strlen($edit_msisdn) && $verify3 && $verify4) {
		$sql = "UPDATE [ITOP].[dbo].[GROUPMAP] SET [GROUPMAP_Id] = ?,[GROUPMAP_Msisdn] = ?,[GROUPMAP_Name] = ?,[GROUPMAP_Description] = ?
WHERE [GROUPMAP_Id] = ? AND [GROUPMAP_Msisdn] = ?";
		if ($stmt = querySqlEx($sql, [$group_id,$msisdn,$_POST['GROUPMAP_Name'], (strlen($_POST['GROUPMAP_Description'])>0?$_POST['GROUPMAP_Description']:NULL), $edit_group_id, $edit_msisdn])) {
			$_GET['toast_type']="S";
			$_GET['toast_header']="Result";
			$_GET['toast_message']="Add contact [$msisdn] successfully";
		}else{
			$_GET['toast_type']="E";
			$_GET['toast_header']="Alert";
			$_GET['toast_message']="Add contact [$msisdn] fail";
		}
	}else{
		$_GET['toast_type']="E";
		$_GET['toast_header']="Alert";
		$_GET['toast_message']="Incorrect parameter";
	}
} else if (strlen($_POST['new_contact'])>0 && $_POST['new_contact']==1) {
	$group_id = $_POST['GROUPINFO_Id'];
	$msisdn = $_POST['GROUPMAP_Msisdn'];
	$verify1 = validateNumber($group_id);
	$verify2 = validatePhoneNumber($msisdn);
	$msisdn = convert2ThaiPhoneNumber($msisdn);
	if (strlen($msisdn) && $verify1 && $verify2) {
		$sql = "SELECT COUNT(*) FROM [ITOP].[dbo].[GROUPMAP] WHERE [GROUPMAP_Id] IN (SELECT [GROUPINFO_Id] FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?)";
		$total_contact = querySqlSingleFieldEx($sql, [$_SESSION[_SES_USR_NAME]]);
		if ($total_contact >= _CONTACT_NUM) {
			$_GET['toast_type']="W";
			$_GET['toast_header']="Warning";
			$_GET['toast_message']="Can not add new contact more than "._CONTACT_NUM;
		}else{
			$sql = "INSERT INTO [ITOP].[dbo].[GROUPMAP] ([GROUPMAP_Id],[GROUPMAP_Msisdn],[GROUPMAP_Name],[GROUPMAP_Description])VALUES(?,?,?,?)";
			if ($stmt = querySqlEx($sql, [$group_id,$msisdn,$_POST['GROUPMAP_Name'],(strlen($_POST['GROUPMAP_Description'])>0?$_POST['GROUPMAP_Description']:NULL)])) {
				$_GET['toast_type']="S";
				$_GET['toast_header']="Result";
				$_GET['toast_message']="Add contact [$msisdn] successfully";
			}else{
				$_GET['toast_type']="E";
				$_GET['toast_header']="Alert";
				$_GET['toast_message']="Add contact [$msisdn] fail";
			}
		}
	}else{
		$_GET['toast_type']="E";
		$_GET['toast_header']="Alert";
		$_GET['toast_message']="Incorrect parameter";
	}
}
?>
			<div class="row col-md-12 justify-content-right"><!-- # ทดสอบภาษาไทย -->
				<div class="col-md-4">
					<div class="card ml-2">
						<div class="card-header bg-primary text-white">
							<div class="row">
								<div class="col">
									<h4>Groups</h4>
								</div>
								<div id="group-count" class="col text-right"><h4><?php
$sql = "SELECT COUNT(*) FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?";
$total_group = querySqlSingleFieldEx($sql, [$_SESSION[_SES_USR_NAME]]);
echo "($total_group/"._GROUP_NUM.")";
								?></h4></div>
							</div>
						</div>
						<div class="card-body justify-content-center">
<script type="text/javascript">
</script>
<?php
$sql = "SELECT TOP "._GROUP_NUM." [GROUPINFO_Id], [GROUPINFO_Name] FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?";
if ($stmt = querySqlEx($sql, [$_SESSION[_SES_USR_NAME]])) {
	$groups = $stmt->fetchAll();
	for($i=0; $i<_GROUP_NUM; $i++) {
		if ($groups[$i]['GROUPINFO_Id']) {
			$g_bg = "bg-success";
			$g_id = $groups[$i]['GROUPINFO_Id'];
			$g_label = $groups[$i]['GROUPINFO_Name'];
			$g_name = $groups[$i]['GROUPINFO_Name'];
			$g_del = "style=\"display: block;\"";
		}else{
			$g_id = "new-$i"; $g_label = "untitled"; $g_name = ""; $g_bg = "bg-warning"; $g_del = "style=\"display: none;\"";
		}
?>
							<div class="form-row justify-content-center" >
								<div class="card mb-2 <?=$g_bg;?> text-dark" id="card_container">
									<div class="shadow">
										<form class="d-md-inline-block form-inline" id="form_group-<?=$g_id;?>" name="form_group-<?=$g_id;?>" method="post" action="#" onsubmit="">
											<div class="card-body form-group form-inline p-2 justify-content-between" style="width: 22rem;">
												<div class="ml-3 form-inline">
													<input type="hidden" id="select_page" name="select_page" value="<?php echo $select_page;?>">
													<input type="hidden" id="group_id" name="group_id" value="<?=$g_id;?>">
													<input type="hidden" id="group_ori_name" name="group_ori_name" value="<?=$g_name;?>">
													<input type="hidden" id="group-delete-<?=$g_id;?>" name="group-delete-<?=$g_id;?>" value="0">
													<span class="font-weight-bold font-italic mr-2 class-group-count"><?=($i+1);?>)</span>
													<span style="display: block;" id="label-text-<?=$g_id;?>" 
														class="font-weight-bold font-italic"><?=$g_label;?></span>
													<input style="display: none;width: 12rem;" id="input-<?=$g_id;?>" name="input-<?=$g_id;?>" 
														class="form-control class-group-name" type="text" value="<?=$g_name;?>" placeholder="ex: Group A">
												</div>
												<div class=" ml-3 form-inline">
													<button type="button" style="display: block;" id="edit-<?=$g_id;?>" name="edit-<?=$g_id;?>"
														class="close" onclick="enableEditGroupById('<?=$g_id;?>');"><i class="fa fa-edit"></i></button>
													<button type="button" style="display: none;" id="save-<?=$g_id;?>" name="save-<?=$g_id;?>"
														class="close" onclick="submitGroupNameById('<?=$g_id;?>');"><i class="fa fa-save"></i></button>
													<button type="button" <?=$g_del?> id="del-<?=$g_id;?>" name="del-<?=$g_id;?>"
														class="close mx-2" onclick="deleteGroupNameById('<?=$g_id;?>');"><i class="fa fa-trash"></i></button>
													<button type="button" style="display: none;" id="close-<?=$g_id;?>" name="close-<?=$g_id;?>"
														class="close mx-2" onclick="enableEditGroupById('<?=$g_id;?>');"><i class="fa fa-times-circle"></i></button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
<?php
	}
}
$total_contact = 0;
?>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card mr-2">
						<div class="card-header bg-primary text-white">
							<div class="row">
								<div class="col"><h4>Contacts <!--<a href="#" id="new-0" name="new-0" data-toggle="modal" data-target="#editGroConModal"><font color="#FFF"><i class="fa fa-fw fa-plus-circle"></i></font></a>--><a href="#" id="new-0" name="new-0" onclick="addContactValidate();"><font color="#FFF"><i class="fa fa-fw fa-plus-circle"></i></font></a></h4></div>
								<div class="col text-right">
									<h4>
									<?php
									switch($_SESSION[_SES_USR_TYP_ID]) {
										case 211: # master
											$sql = "SELECT COUNT(*) FROM [ITOP].[dbo].[ACCPAIR] WHERE [ACCPAIR_Parent] = ?";
											echo "(".querySqlSingleFieldEx($sql, [$_SESSION[_SES_USR_NAME]]).")";
										break;
										case 212:
										case 213:
											$sql = "SELECT COUNT(*) FROM [ITOP].[dbo].[GROUPMAP] WHERE [GROUPMAP_Id] IN (SELECT [GROUPINFO_Id] FROM [ITOP].[dbo].[GROUPINFO] WHERE [GROUPINFO_Msisdn] = ?)";
											$total_contact = querySqlSingleFieldEx($sql, [$_SESSION[_SES_USR_NAME]]);
											echo "($total_contact/"._CONTACT_NUM.")";
										break;
										default:
											echo "(--)";
										break;
									}
									?>
									</h4>
								</div>
							</div>
						</div>
						<div class="card-body">
							<?php 
							$max_rows = 5;
							require_once("_p_contacts_form_table.php");
							?>
						</div>
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
				<button class="btn btn-primary" type="submit" id="confirmModalConfirmButton">Apply</button>
				<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#confirmModalConfirmButton').click(function(e){
      e.preventDefault();
	  if (form_to_submit.length>0) {
		  document.getElementById(form_to_submit).submit();
	  }
});
var form_to_submit = "";

function enableEditGroupById (id) {
	var label_text = document.getElementById("label-text-"+id);
	var input = document.getElementById("input-"+id);
	var edit = document.getElementById("edit-"+id);
	var save = document.getElementById("save-"+id);
	var del = document.getElementById("del-"+id);
	var close = document.getElementById("close-"+id);
	
	if (label_text.style.display === 'block') {
		input.style.display = "block";
		input.focus();
		label_text.style.display = "none";
		save.style.display = "block";
		edit.style.display = "none";
		del.style.display = "none";
		close.style.display = "block";
	} else {
		input.style.display = "none";
		label_text.style.display = "block";
		save.style.display = "none";
		edit.style.display = "block";
		if (label_text.innerHTML.length>0 && label_text.innerHTML != "untitled") {
			del.style.display = "block";
		}else{
			del.style.display = "none";
		}
		close.style.display = "none";
	}
}
function updateLabelById(id) {
	var input = document.getElementById("input-"+id);
	var label_text = document.getElementById("label-text-"+id);
	label_text.innerHTML = input.value;
}
function submitGroupNameById(id) {
	var f = document.getElementById("form_group-"+id);
	var input = document.getElementById("input-"+id);
	if (input.value == null || input.value == "") {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-warning text-dark';
		document.getElementById("informModalHeaderText").innerHTML = "Warning";
		document.getElementById("informModalBodyText").innerHTML = "Group name is empty";
		$("#informModal").modal("show");
		return true;
	} else if (input.value == "untitled") {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-warning text-dark';
		document.getElementById("informModalHeaderText").innerHTML = "Warning";
		document.getElementById("informModalBodyText").innerHTML = "Can not use 'untitled'";
		$("#informModal").modal("show");
	} else if (input.value.length > 15) {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-warning text-dark';
		document.getElementById("informModalHeaderText").innerHTML = "Warning";
		document.getElementById("informModalBodyText").innerHTML = "Name can not over <?=_GROUP_NAME_LENGTH;?> characters";
		$("#informModal").modal("show");
		return true;
	}else{
		enableEditGroupById(id);
		updateLabelById(id);
		f.submit();
	}
}
function deleteGroupNameById(id) {
	form_to_submit = "form_group-"+id;
	var group_name = document.getElementById("label-text-"+id).innerHTML;
	var group_del = document.getElementById("group-delete-"+id);
	group_del.value = 1;
	document.getElementById("confirmModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
	document.getElementById("confirmModalHeaderText").innerHTML = "Delete ["+group_name+"]";
	document.getElementById("confirmModalBodyText").innerHTML = "If ["+group_name+"] has been deleted, all of contacts in this group will be delete.<BR/>Do you want to delete group ["+group_name+"] ?";
	$('#confirmModal').modal('show');
	return true;
}
function deleteContactNameById(id) {
	form_to_submit = "form_group-"+id;
	var group_name = document.getElementById("label-text-"+id).innerHTML;
	var group_del = document.getElementById("group-delete-"+id);
	group_del.value = 1;
	document.getElementById("confirmModalHeader").className = 'modal-header font-weight-bold bg-danger text-white';
	document.getElementById("confirmModalHeaderText").innerHTML = "Confirm";
	document.getElementById("confirmModalBodyText").innerHTML = "If ["+group_name+"] has been deleted, all of contacts in this group will be delete.<BR/>Do you want to delete it ?";
	$('#confirmModal').modal('show');
	return true;
}
function addContactValidate() {
	if (<?=$total_contact;?> >= <?=_CONTACT_NUM;?>) {
		document.getElementById("informModalHeader").className = 'modal-header font-weight-bold bg-warning text-dark';
		document.getElementById("informModalHeaderText").innerHTML = "Warning";
		document.getElementById("informModalBodyText").innerHTML = "Can not add new contact more than <?=_CONTACT_NUM;?>";
		$("#informModal").modal("show");
		return false;
	}else{
		$("#editGroConModal").modal("show");
		return true;
	}
}
</script>