<?php require 'ice_validate.php'; ?>
<?php
  function getComments($postId) {
    require 'ice_mysqli_init.php';
    $sql = "SELECT * FROM ice_comment WHERE post_id=".$postId." ORDER BY ice_comment.comment_id;";
    $res = $mysqli->query($sql);
    $posts = $res->fetch_all(MYSQLI_ASSOC);

    return $posts;
  }

  function postComment($post_id, $name, $title, $comment_text) {
    if ( $name == '' ) return;
    if ( $title == '' ) return;
    if ( $comment_text == '' ) return;

    require 'ice_mysqli_init.php';
    $sql = "INSERT INTO ice_comment (post_id, name, title, comment_text) VALUES (".
    $post_id.", ".
    "'".$name."', ".
    "'".$title."', ".
    "'".$comment_text."');";
    $res = $mysqli->query($sql);

    return $res;
  }

  function deleteComment($comment_id) {
    require 'ice_mysqli_init.php';
    $stmt = $mysqli->prepare("DELETE FROM ice_comment WHERE comment_id=?");
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();
    $stmt->close();
  }

?>

<?php
  if (isset($_GET['post_id'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(getComments($_GET['post_id']));
  } else if (isset($_GET['delete'])) {
    if (validate())
      deleteComment($_GET['delete']);
    echo http_response_code( 200 );
  } else if (
    isset($_POST['post_id']) &&
    isset($_POST['name']) &&
    isset($_POST['title']) &&
    isset($_POST['comment_text']) )
  {
    postComment($_POST['post_id'], $_POST['name'], $_POST['title'], $_POST['comment_text']);
    header("Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/post.php?post_id=".$_POST['post_id']);
  }
  
?>
