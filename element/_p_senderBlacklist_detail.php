<?php
  session_start();

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
          echo 'Adjust successfully.' . '<br>';
          echo '<pre>'.$str_msg.'</pre>';
        }
        if (isset($str_err1_msg)) {
          echo 'Can not adjust because sender does not exist.' . '<br>';
          echo '<pre>'.$str_err1_msg.'</pre>';
        }
        if (isset($str_err2_msg)) {
          echo 'Can not adjust because sender and status already exist.' . '<br>';
          echo '<pre>'.$str_err2_msg.'</pre>';
        }
      ?>
    </div>
  </body>
</html>