<?php
  function getData($key) {
    require 'ice_mysqli_init.php';
    $screen_name = $_COOKIE["screen_name"];
    $password = $_COOKIE["password"];

    $stmt = $mysqli->prepare("SELECT * FROM ice_author WHERE screen_name=? and password=?");
    $stmt->bind_param('ss', $screen_name, $password);

    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) return "";
    $result = $result->fetch_array(MYSQLI_ASSOC)[$key];
    $stmt->close();

    return htmlspecialchars($result);
  }
?> 

