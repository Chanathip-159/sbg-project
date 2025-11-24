<?php
  session_start();

  date_default_timezone_set("Asia/Bangkok");
  require_once(__DIR__."/../php.lib/function.basic.php");
  require_once(__DIR__."/../php.lib/function.basic.db.php");
  require_once("element/_1_defines.php"); #use for define variable such as _CEO,_DEB
  require_once("element/_2_var_static.php"); #use for static variable such as month name
  require_once("element/_3_functions_base.php"); #use for basic functions such as echoMsg
  require_once("element/_4_functions_db.php"); # db connect and db functions

  $php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];

  $dest_file="senderUpload_details";	
  $_SESSION[_SES_CUR_PAGE]=$dest_file;
  require_once("element/_e_check_permission.php");

  $str_msg = $_SESSION['str_msg'];
  $str_err1_msg = $_SESSION['str_err1_msg'];
  $str_err2_msg = $_SESSION['str_err2_msg'];
?>
<html>
  <head>
    <title>Sender details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container"><br>
      <div class="from-control">
        <!-- <button type="button" class="btn btn-primary">Export</button> -->
        <button style="width: 100px;" type="button" class="btn btn-danger" onclick="window.open('', '_self', ''); window.close();">Close</button>
      </div><br>
      <?php
        if (isset($str_msg)) {
          echo 'Upload sender successfully.' . '<br>';
          echo '<pre>'.$str_msg.'</pre>';
        }
        if (isset($str_err1_msg)) {
          echo 'Can not upload sender because user account does not exist.' . '<br>';
          echo '<pre>'.$str_err1_msg.'</pre>';
        }
        if (isset($str_err2_msg)) {
          echo 'Can not upload sender because already exist.' . '<br>';
          echo '<pre>'.$str_err2_msg.'</pre>';
        }
      ?>
    </div>
  </body>
</html>