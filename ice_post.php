<?php
  require 'ice_mysqli_init.php';
  $mysqli->set_charset("utf8");
  $sql = "SELECT * FROM ice_post";
  $res = $mysqli->query($sql);
  
  $posts = $res->fetch_all(MYSQLI_ASSOC);
  
  foreach ($posts as &$post) {
    $post['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/post.php?post_id=" . $post['post_id'];
  }

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($posts);

?>
