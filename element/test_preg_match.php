<?php
  //$raw_data = Array("66+845523690","66915522269");
  $raw_data = file_get_contents("test_preg_match.txt");
  $pattern1 = "/[\/'^£$%&*()}{@#~?><>,|=_+¬-]/i";
  $pattern2 = "/[a-zA-Z]/";

 //  print_r($raw_data);

  if (preg_match($pattern1, $raw_data, $matches1, PREG_OFFSET_CAPTURE)) {
      echo "Found from pattern1";
    print_r($matches1);
  } else if (preg_match($pattern2, $raw_data, $matches2, PREG_OFFSET_CAPTURE)) {
        echo "Found from pattern2";
        print_r($matches2);
  } else {
    echo "Not found at all";
  }
?>