<?php
  $files = scandir('./music');
  array_splice($files, 0, 2);
  foreach ($files as &$value) {
    $value = pathinfo($value, PATHINFO_FILENAME);
  }
  $json = json_encode($files);
  print_r($json);
?>