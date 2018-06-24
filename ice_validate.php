<?php
  function validate() {
    require 'ice_mysqli_init.php';
    if ( !isset($_COOKIE["screen_name"]) || !isset($_COOKIE["password"]) ) {
      return false;
    }

    $screen_name = $_COOKIE["screen_name"];
    $password = $_COOKIE["password"];

    $stmt = $mysqli->prepare("SELECT author_id FROM ice_author WHERE screen_name=? and password=?");
    $stmt->bind_param('ss', $screen_name, $password);

    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;
    $stmt->close();

    return ($num_rows > 0);
  }
?>

