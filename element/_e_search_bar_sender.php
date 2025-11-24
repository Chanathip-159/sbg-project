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
          <select class="form-control" name="start_year" class="custom-select mr-1">
          <?php
            foreach($year_list AS $year) {
          ?>
          <option value="<?=$year;?>" <?=($year==$start_year ? "selected":"");?>><?=$year;?></option>
          <?php
            }
          ?>
          </select>
          <select class="form-control" name="start_month">
          <?php
            for($i=1;$i<=12;$i++) {
          ?>
          <option value="<?=$i;?>" <?=(strlen($start_month)&&$i==$start_month ? "selected":"");?>><?=$month_eng_full[$i];?></option>
          <?php 
            }
          ?>
          </select>
          <select class="form-control" name="start_day">
          <?php
            for($i=1;$i<=31;$i++) {
          ?>
          <option value="<?=$i;?>" <?=(strlen($start_day)&&$i==$start_day ? "selected":"");?>><?=$i;?></option>
          <?php 
            }
          ?>
          </select>
        </div> &nbsp; To &nbsp;

        <div class="form-group" style="<?php echo $hidden;?>">
          <select class="form-control" name="end_year">
          <?php
            foreach($year_list AS $year) {
          ?>
          <option value="<?=$year; ?>" <?=($year==$end_year ? "selected":"");?>><?=$year; ?></option>
          <?php
            }
          ?>
          </select>
          <select class="form-control" name="end_month">
          <?php
            for($i=1;$i<=12;$i++) {
          ?>
          <option value="<?=$i;?>" <?=(strlen($end_month)&&$i==$end_month ? "selected":"");?>><?=$month_eng_full[$i];?></option>
          <?php 
            }
          ?>
          </select>
          <select class="form-control" name="end_day">
          <?php
            for($i=1;$i<=31;$i++) {
          ?>
          <option value="<?=$i;?>" <?=(strlen($end_day)&&$i==$end_day ? "selected":"");?>><?=$i;?></option>
          <?php 
            }
          ?>
          </select>
        </div>&nbsp;&nbsp;
        <?php 
          }
        ?>

        <div class="form-group">
          <input type="text" id="sender" name="sender" value="<?php echo $sender; ?>" class="form-control" placeholder="Sender name" size="10">
        </div>&nbsp;
        <div class="form-group">
          <input type="text" id="username" name="username" value="<?php echo $username; ?>" class="form-control" placeholder="Username" size="10">
        </div>&nbsp;
        <div class="form-group">
          <input type="text" id="text_search" name="text_search" value="<?php echo $text_search; ?>" class="form-control" placeholder="Search by text" size="10">
        </div>&nbsp;
        <div class="form-group"><br>
          <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> search</button>
        </div>

      </div>
    </form>
  </div>