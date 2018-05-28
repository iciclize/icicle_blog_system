<?php
  require 'ice_mysqli_init.php';
  $mysqli->set_charset("utf8");
  $sql = "SELECT * FROM ice_post";
  $res = $mysqli->query($sql);
  
  print("[");
  for ( $i = 1;  $row = $res->fetch_array(MYSQL_ASSOC); $i++ ) {
    print(json_encode($row));
    if ( $i < $res->num_rows ) print(", ");
  }
  print("]");

?>
