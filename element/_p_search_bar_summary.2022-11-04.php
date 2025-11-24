<?php
  $php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];
	if (!$year_list) $hidden = "visibility: hidden;"; else $hidden = "";
?>
  <div class="col-md-12 <?=($year_list == true ? 'text-center' : 'text-right')?> pr-0">
    <form class="d-md-inline-block form-inline" name="formSearch" id="formSearch" method="post" action="<?=_MAIN_DF.$dest_file.$_get_url;?>" onsubmit='var save_topup_draft = document.getElementById("save_topup_draft").innerHTML; if (save_topup_draft != "undefined") {localStorage.setItem("save_topup_draft", save_topup_draft);}'>
      <div class="form-row align-items-center">
      <?php  
        if ($year_list) {
      ?>				
        <div class="form-group" style="<?php echo $hidden;?>">
          <select class="form-control" name="start_year" class="custom-select mr-1" onchange="setEndDateWithStartDate();">
          <?php
            foreach($year_list AS $year) {
          ?>
          <option value="<?=$year;?>" <?=($year==$start_year ? "selected":"");?>><?=$year;?></option>
          <?php
            }
          ?>
          </select>
          <select class="form-control" name="start_month" onchange="setEndDateWithStartDate();">
          <?php
            for($i=1;$i<=12;$i++) {
          ?>
          <option value="<?=$i;?>" <?=(strlen($start_month)&&$i==$start_month ? "selected":"");?>><?=$month_eng_full[$i];?></option>
          <?php 
            }
          ?>
          </select>
        </div> &nbsp;

        <div class="form-group">
          <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> search</button>
        </div>
        <?php 
          }
        ?>
      </div><br>

    </form>
  </div>

  <script type="text/javascript">
setEndDateWithStartDate();
function setEndDateWithStartDate() {
	document.getElementById('end_year').value = document.getElementById('start_year').value;
	document.getElementById('end_month').value = document.getElementById('start_month').value;
}
</script>