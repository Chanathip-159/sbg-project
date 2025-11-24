<?php
$php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];
/*
# _e_<filename> need _#_<filename> at host file
require_once("_1_defines.php"); #use for define variable such as _CEO, _DEB
require_once("_2_static.var.php"); #use for static variable such as month name
require_once("_3_base.functions.php"); #use for basic functions such as echoMsg
require_once("_4_db.functions.php"); # db connect and db functions
//*/
/*
function searchBar($db, $dest_file, $text_search=null, $year_list=null, $start_year=null, $start_month=null, $start_day=null
			, $end_year=null, $end_month=null, $end_day=null) {
	global $month_eng_full;//*/
	if (!$year_list) $hidden = "visibility: hidden;"; else $hidden = "";
?>
		<div class="col-md-12 <?=($year_list == true ? 'text-center' : 'text-right')?> pr-0">
			<form class="d-md-inline-block form-inline" name="formSearch" id="formSearch" method="post" action="<?php echo _MAIN_DF.$dest_file.$_get_url;?>" onsubmit='var save_topup_draft = document.getElementById("save_topup_draft").innerHTML; if (save_topup_draft != "undefined") {localStorage.setItem("save_topup_draft", save_topup_draft);}'>
				<div class="form-row align-items-center">
<?php if ($year_list) { ?>				
					<div class="card ml-2 mr-2 mt-2" style="<?php echo $hidden;?>">
						<div class="shadow">
							<div class="card-header text-white text-center p-2 bg-info">Start date</div>
							<div class="card-body form-group p-2">
								<select name="start_year" id="start_year" class="custom-select mr-1" onchange="setEndDateWithStartDate();">
									<?php
									foreach($year_list AS $year) {
									?>
									<option value="<?=$year;?>" <?=($year==$start_year ? "selected":"");?>><?=$year;?></option>
									<?php
									}
									?>
								</select>
								<select name="start_month" id="start_month" class="custom-select mr-1" onchange="setEndDateWithStartDate();">
									<?php
									for($i=1;$i<=12;$i++) {
									?>
									<option value="<?=$i;?>" <?=(strlen($start_month)&&$i==$start_month ? "selected":"");?>><?=$month_eng_full[$i];?></option>
									<?php 
									}
									?>
								</select>
								<select name="start_day" class="custom-select">
									<?php
									for($i=1;$i<=31;$i++) {
									?>
									<option value="<?=$i;?>" <?=(strlen($start_day)&&$i==$start_day ? "selected":"");?>><?=$i;?></option>
									<?php 
									}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="card ml-2 mr-2 mt-2" style="<?=$hidden;?>">
						<div class="shadow">
							<div class="card-header text-white text-center p-2 bg-info">End date</div>
							<div class="card-body form-group p-2">
								<select name="end_year" id="end_year" class="custom-select mr-1" disabled>
									<?php
									foreach($year_list AS $year) {
									?>
									<option value="<?=$year; ?>" <?=($year==$end_year ? "selected":"");?>><?=$year; ?></option>
									<?php
									}
									?>
								</select>
								<select name="end_month" id="end_month" class="custom-select mr-1" disabled>
									<?php
									for($i=1;$i<=12;$i++) {
									?>
									<option value="<?=$i;?>" <?=(strlen($end_month)&&$i==$end_month ? "selected":"");?>><?=$month_eng_full[$i];?></option>
									<?php 
									}
									?>
								</select>
								<select name="end_day" class="custom-select">
									<?php
									for($i=1;$i<=31;$i++) {
									?>
									<option value="<?=$i;?>" <?=(strlen($end_day)&&$i==$end_day ? "selected":"");?>><?=$i;?></option>
									<?php 
									}
									?>
								</select>
							</div>
						</div>
					</div>
<script type="text/javascript">
setEndDateWithStartDate();
function setEndDateWithStartDate() {
	document.getElementById('end_year').value = document.getElementById('start_year').value;
	document.getElementById('end_month').value = document.getElementById('start_month').value;
}
</script>
<?php } ?>
					<div class="card ml-2 mr-2 mt-2">
						<div class="shadow">
							<div class="card-header text-white text-center p-2 bg-info">Search</div>
							<div class="card-body form-group p-2">
								<div class="form-group input-group" style="width: <?=($text_search_width >0 ? $text_search_width : '18')?>rem;">
									<input type="text" id="text_search" name="text_search" value="<?php echo $text_search; ?>" class="form-control" placeholder="ex: 'word1 1|word2' or '0|word1++1|word2'">
									<div class="input-group-append">
										<button class="btn btn-default" type="reset" title="Clear" onClick="document.getElementById('text_search').value = '';document.getElementById('formSearch').action='<?=_MAIN_DF.$dest_file;?>';document.getElementById('formSearch').submit();">
											<i class="fas fa-trash fa-sm"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card ml-2 mr-2 mt-2">
						<span class="input-group-btn"><button class="btn btn btn-primary shadow" type="submit" title="Search"><h3>&nbsp;<i class="fa fa-search"></i>&nbsp;</h3></button></span>
					</div>
				</div>
			</form>
		</div>