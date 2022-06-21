<?php
  $array = array(
    '1' => 'Значение 1', 
    '2' => 'Значение 2', 
    '3' => 'Значение 3', 
    '4' => 'Значение 4', 
    '5' => 'Значение 5'
  );
  
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: THE_FRONTEND_DOMAIN");
  $json = json_encode($array, JSON_UNESCAPED_UNICODE);
  echo $json;
  exit();
?>