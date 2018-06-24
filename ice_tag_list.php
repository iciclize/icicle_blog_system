<?php
  function createNewTag($tagName) {
    require 'ice_mysqli_init.php';

    if ($tagName == "") return;

    $sql = "INSERT INTO ice_tag (tag_name) VALUES ('".$tagName."');";
    $res = $mysqli->query($sql);
    $mysqli->close();

    return ($res);
  }

  function editTagName($tagId, $tagName) {
    require 'ice_mysqli_init.php';
    
    if ($tagName == "") return;

    $sql = "UPDATE ice_tag SET `tag_name`='".$tagName."' WHERE `tag_id`=".$tagId.";";
    $res = $mysqli->query($sql);
    $mysqli->close();

    return ($res);
  }

  function deleteTag($tagId) {
    require 'ice_mysqli_init.php';
    $sql = "DELETE FROM ice_tag_map WHERE tag_id=".$tagId.";";
    $res = $mysqli->query($sql);

    $sql = "DELETE FROM ice_tag WHERE tag_id=".$tagId.";";
    $res = $mysqli->query($sql);
    $mysqli->close();

    return ($res);
  }
?>

<?php

  if (isset($_GET['create_tag'])) {
    createNewTag($_GET['create_tag']);
    echo http_response_code( 200 );
  } else if (isset($_GET['edit_tag']) && isset($_GET['tag_id'])) {
    echo editTagName($_GET['tag_id'], $_GET['edit_tag']);
    // echo http_response_code( 200 );
  } else if (isset($_GET['delete_tag'])) {
    deleteTag($_GET['delete_tag']);
    echo http_response_code( 200 );
  } else {

    require 'ice_mysqli_init.php';

    $sql = "SELECT * FROM ice_tag WHERE 1;";
    $res = $mysqli->query($sql);

    $tags = $res->fetch_all(MYSQLI_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($tags);

    $mysqli->close();

  }

?>
