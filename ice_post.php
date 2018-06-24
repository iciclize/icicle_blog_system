<?php require 'ice_getNames.php'; ?>
<?php require 'ice_validate.php' ?>

<?php
  function getOnePost($postId) {
    require 'ice_mysqli_init.php';
    $sql = "SELECT * FROM ice_post WHERE post_id=".$postId.";";
    $res = $mysqli->query($sql);
    $post = $res->fetch_array(MYSQLI_ASSOC);
    
    $post['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/post.php?post_id=".$post['post_id'];
    $post['author_uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/search.php?author_id=".$post['author_id'];
    $post['author_name'] = getAuthorName($post['author_id']);
    $post['author_img'] = getAuthorImageURI($post['author_id']);
    $post['author_biography'] = getAuthorBiography($post['author_id']);

    $sql = "SELECT tag.* ".
    "FROM ice_tag_map map ".
    "LEFT JOIN ice_tag tag on map.tag_id = tag.tag_id ".
    "LEFT JOIN ice_post post on map.post_id = post.post_id ".
    "WHERE post.post_id = " . $post['post_id'] . ";" ;

    $res = $mysqli->query($sql);

    $tags = $res->fetch_all(MYSQLI_ASSOC);

    foreach ($tags as &$tag) {
      $tag['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/search.php?tag_id=" . $tag['tag_id'];
      $post['tags'][] = $tag;
    }

   return $post;
  }
?>

<?php
  if (isset($_GET['post_id'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(getOnePost($_GET['post_id']));
    exit();
  }
?>

<?php
  require 'ice_mysqli_init.php';
  $sql = "SELECT * FROM ice_post WHERE status=1;";

  if (isset($_GET['author_id'])) {
    $sql = "SELECT * FROM ice_post WHERE author_id=".( (int)$_GET['author_id'] ).";" ;
  } else if (isset($_GET['tag_id'])) {
    $sql = "SELECT post.* ".
    "FROM ice_post post ".
    "LEFT JOIN ice_tag_map map on post.post_id = map.post_id ".
    "LEFT JOIN ice_tag tag on map.tag_id = tag.tag_id ".
    "WHERE tag.tag_id = " . (int)$_GET['tag_id'] . ";";
  } else if (isset($_GET['keyword'])) {
    $sql = "SELECT * FROM ice_post WHERE ".
    "content_text LIKE '%".(htmlspecialchars($_GET['keyword']))."%' ".
    "or title LIKE '%".(htmlspecialchars($_GET['keyword']))."%' ".";";
  }

  $res = $mysqli->query($sql);

  $posts = $res->fetch_all(MYSQLI_ASSOC);
  
  if ( !validate() ) {
    foreach ($posts as $key => $post) {
      if ($post['status'] == 0) {
        unset( $posts[$key] );
      }
    }
  }

  $posts = array_values($posts);

  foreach ($posts as &$post) {

    $post['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/post.php?post_id=".$post['post_id'];
    $post['author_uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/search.php?author_id=".$post['author_id'];
    $post['author_name'] = getAuthorName($post['author_id']);
    $post['author_img'] = getAuthorImageURI($post['author_id']);
    $post['author_biography'] = getAuthorBiography($post['author_id']);

    $sql = "SELECT tag.* ".
    "FROM ice_tag_map map ".
    "LEFT JOIN ice_tag tag on map.tag_id = tag.tag_id ".
    "LEFT JOIN ice_post post on map.post_id = post.post_id ".
    "WHERE post.post_id = " . $post['post_id'] . ";" ;

    $res = $mysqli->query($sql);

    $tags = $res->fetch_all(MYSQLI_ASSOC);
    foreach ($tags as &$tag) {
      $tag['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/search.php?tag_id=" . $tag['tag_id'];

      $post['tags'][] = $tag;
    }

  }

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($posts);

  $mysqli->close();

?>
