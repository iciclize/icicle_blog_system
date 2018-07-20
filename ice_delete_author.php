<?php
  require 'ice_validate.php';

  if ( validate() ) {
    require 'ice_mysqli_init.php';

    $screen_name = $_COOKIE['screen_name'];

    $s = $mysqli->prepare("SELECT * FROM ice_author WHERE screen_name=?");
    $s->bind_param('s', $screen_name);
    $s->execute();

    $author_id = $s->get_result()->fetch_array(MYSQLI_ASSOC)['author_id'];

    $s = $mysqli->prepare("DELETE a FROM ice_comment a LEFT JOIN ice_post b ON a.post_id = b.post_id WHERE b.author_id=?");
    $s->bind_param('i', $author_id);
    $s->execute();

    $s = $mysqli->prepare("DELETE a FROM ice_tag_map a LEFT JOIN ice_post b ON a.post_id = b.post_id WHERE b.author_id=?");
    $s->bind_param('i', $author_id);
    $s->execute();

    $s = $mysqli->prepare("DELETE FROM ice_post WHERE author_id=?");
    $s->bind_param('i', $author_id);
    $s->execute();

    $fileName = "./images/".$author_id."_profile.*";
    foreach ( glob($fileName) as $val ) {
      unlink($val);
    }

    $s = $mysqli->prepare("DELETE FROM ice_author WHERE author_id=?");
    $s->bind_param('i', $author_id);
    $s->execute();
  
    $s->close();

    header("Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/");

  }
?>
