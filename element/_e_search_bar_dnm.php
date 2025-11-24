<?php 
switch ($dest_file) {
  case "manage_cus":
    $values = array (
      'CUSTOMER_Username' => 'Username',
      'CUSTOMER_Telephone' => 'Phone no.',
      'CUSTOMER_MonthlyUsage' => 'Monthly usage',
      'CUSTOMER_AccountUsage' => 'Account usage',
    );
    $numAppend = 5; // 4 + (1 from first search bar)
  break;
  case "sub_user":
    $values = array (
      'CUSTOMER_Parent_Username' => 'Username',
      'CUSTOMER_Username' => 'Sub-user',
      'CUSTOMER_MonthlyUsage' => 'Monthly usage',
      'CUSTOMER_AccountUsage' => 'Account usage',
    );
    $numAppend = 5; // 4 + (1 from first search bar)
  break;
  case "report":
    $values = array (
      'transaction_id' => 'Transaction ID',
      'charge_account' => 'Username',
      'service_type' => 'Service type',
      'originate_number' => 'Originate number',
      'error_code' => 'Error code',
      'request_ip' => 'Request IP',
      'sm_id' => 'SM ID',
      'terminate_number' => 'Terminate number',
    );
    $numAppend = 9; // 8 + (1 from first search bar)
  break;
  case "sum_report":
    $values = array (
      'SUMRPT_Username' => 'Username',
      'SUMRPT_Sender' => 'Sender',
      'SUMRPT_Deliver_code' => 'Deliver code',
    );
    $numAppend = 4; // 3 + (1 from first search bar)
  break;
  case "blacklist":
    $values = array (
      'SENDER_Sender' => 'Sender',
      'SENDER_Username' => 'Username',
    );
    $numAppend = 3; // 2 + (1 from first search bar)
  break;
}

$selectBy_values = array(
  '1' => 'Contain',
  '2' => 'Not contain',
  '3' => '=',
  '4' => '!=',
  '5' => '>',
  '6' => '<',
  '7' => '>=',
  '8' => '<=',
);
$selectAndOr_values = array(
  'and' => 'AND',
  'or' => 'OR',
);
?>
<div class="col-md-12 text-center pr-0">  
  <form class="d-md-inline-block form-inline" id="subscribe_frm" method="post" action="<?=_MAIN_DF.$dest_file.$_get_url;?>" onsubmit="return toSubmit();">
    <div id="formClassDM">
      <?php 
      	if ($year_list) { 
      ?>
      <div class="form-group justify-content-center">
        <input class="form-control" type="datetime-local" id="start_date" name="start_date" value="<?php echo $_POST['start_date']; ?>" required>
        &nbsp;-&nbsp;
        <input class="form-control" type="datetime-local" id="end_date" name="end_date" value="<?php echo $_POST['end_date']; ?>" required>
      </div><br>
      <?php 
        }
      ?>
      <div class="form-group">
        <select class="form-control" name="title_1" id="select_1" >
          <option disabled selected value> - Select title for search - </option>
          <?php
            foreach( $values as $names => $display) {
          ?>
          <option <?=($_POST['title_1'] == $names ? "selected":"");?> value="<?php echo $names; ?>" ><?php echo $display; ?></option>
          <?php
            }
          ?>
        </select>
        &nbsp;
        <select class="form-control" name="selectBy_1">
          <?php
              foreach( $selectBy_values as $names => $display) {
            ?>
          <option <?=($_POST['selectBy_1'] == $names ? "selected":"");?> value="<?php echo $names; ?>" ><?php echo $display; ?></option>
          <?php
            }
          ?>
        </select>
        &nbsp;
        <input class="form-control" type="text" name="text_1" value="<?php echo $_POST['text_1']; ?>" >
      </div>
      <?php
      $counter = array();
        for($i=2;$i<=$numAppend;$i++) {
          if($_POST['title_'.$i] != '') {
            // $counter = $i;
            array_push($counter,$i);
?>
      <div id='dm_formSearch' class='dm_formSearch' id='myid_<?php echo $i; ?>'>
        <select class='form-control' style='background-color: #85929E;color: #FFFFFF' name="selectAndOr_<?php echo $i;?>" id="selectAndOr_<?php echo $i;?>">
          <?php
            foreach( $selectAndOr_values as $names => $display) {
          ?>
          <option <?=($_POST['selectAndOr_'.$i] == $names ? "selected":"");?> value="<?php echo $names; ?>" ><?php echo $display; ?></option>
          <?php
            }
          ?>
        </select>
        <div class='form-group'>
          <select class="form-control" name="title_<?php echo $i;?>" id="select_<?php echo $i; ?>" required>
            <option disabled selected value> - Select title for search - </option>
            <?php
              foreach( $values as $names => $display) {
            ?>
            <option <?=($_POST['title_'.$i] == $names ? "selected":"");?> value="<?php echo $names; ?>" ><?php echo $display; ?></option>
            <?php
              }
            ?>
          </select>
          &nbsp;
          <select class="form-control" name="selectBy_<?php echo $i; ?>">
          <?php
            foreach( $selectBy_values as $names => $display) {
          ?>
          <option <?=($_POST['selectBy_'.$i] == $names ? "selected":"");?> value="<?php echo $names; ?>" ><?php echo $display; ?></option>
          <?php
            }
          ?>
          </select>
          &nbsp;
          <input class="form-control" type="text" name="text_<?php echo $i;?>" value="<?php echo $_POST['text_'.$i]; ?>" required>
        </div>
      </div>
      <?php
          }
        }
        switch ($dest_file) {
          case "manage_cus":
            $counterState = max($counter[0],$counter[1],$counter[2]);
          break;
          case "report":
            $counterState = max($counter[0],$counter[1],$counter[2],$counter[3],$counter[4],$counter[5],$counter[6]);
          break;
        }
      ?>
    </div>
    <input class="form-control" type="hidden" id="counterState" value="<?php echo $counterState; ?>" >
    <br>
    <!-- button -->
    <div class="col-md-12">
      <button type="button" id="add_field" class="btn btn-dark"><i class="fa fa-plus"></i></button>
      <button type="button" id="del_field" class="btn btn-dark" ><i class="fa fa-minus"></i></button>
      <button type="submit" id="search" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
    </div>
  </form>
</div>
