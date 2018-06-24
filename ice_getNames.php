<?php
  function getAuthorName($id) {
    require 'ice_mysqli_init.php';
    $sql = "SELECT name FROM ice_author WHERE author_id=".$id.";" ;
    return $mysqli->query($sql)->fetch_array(MYSQLI_ASSOC)['name'];
  }

  function getTagName($id) {
    require 'ice_mysqli_init.php';
    $sql = "SELECT tag_name FROM ice_tag WHERE tag_id=".$id.";" ;
    return $mysqli->query($sql)->fetch_array(MYSQLI_ASSOC)['tag_name'];
  }

  function getAuthorImageURI($id) {
    require 'ice_mysqli_init.php';
    $sql = "SELECT image_uri FROM ice_author WHERE author_id=".$id.";" ;
    return $mysqli->query($sql)->fetch_array(MYSQLI_ASSOC)['image_uri'];
  }

  function getAuthorBiography($id) {
    require 'ice_mysqli_init.php';
    $sql = "SELECT biography FROM ice_author WHERE author_id=".$id.";" ;
    return $mysqli->query($sql)->fetch_array(MYSQLI_ASSOC)['biography'];
  }

?>
