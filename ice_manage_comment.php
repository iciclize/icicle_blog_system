<?php
  // 貴様はログインしているかオラッ！

  require 'ice_validate.php';
  if (!validate()) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_login.php?redirect_source=edit.php');
  }
?>

<?php require 'ice_getData.php'; ?>

<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'ice_mysqli_init.php';
    $isDelete = isset($_POST['delete']);
    if ($isDelete) {
      if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
        // 削除
        $stmt = $mysqli->prepare("DELETE FROM ice_post WHERE post_id=?");
        $stmt->bind_param('i', $_POST['delete']);
        $stmt->execute();
        $stmt->close();
      }
    } else {
      // 編集
      $title = $_POST['title'];
      $content = $_POST['content'];
      $content_text = $_POST['content_text'];
      $content_html = $_POST['content_html'];
      $status = $_POST['status'] == 'publish' ? 1 : 0;
      $post_id = $_POST['post_id'];

      $stmt = $mysqli->prepare("SELECT * FROM ice_post WHERE post_id=?");
      $stmt->bind_param('i', $post_id);
      $stmt->execute();

      $isPublished = $stmt->get_result()->fetch_array(MYSQLI_ASSOC)['published'] ? true : false;
      $isFirstPublishing = !$isPublished && $status == 1;

      $stmt->close();

      $query = "UPDATE ice_post SET ".
      "`title`=?, ".
      "`content`=?, ".
      "`content_text`=?, ".
      "`content_html`=?, ".
      "`status`=? ".
      (
        ($isFirstPublishing)
          ? ", `published`=cast( now() as datetime) "
          : ""
      ).
      ( 
        ($isPublished && $status == 1)
          ? ", `modified`=cast( now() as datetime) "
          : ""
      ).
      "WHERE `post_id`=?";

      $stmt = $mysqli->prepare($query);
      $stmt->bind_param('ssssii', $title, $content, $content_text, $content_html, $status, $post_id);
      $stmt->execute();
      $stmt->close();
    }
  }
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>コメントの管理- Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <link rel="stylesheet" href="simplemde.min.css">
  <script src="simplemde.min.js"></script>
  <script src="html2plaintext.js"></script>

  <link rel="stylesheet" href="inject.css">
</head>

<body data-author_id="<?php echo htmlspecialchars(getData('author_id')) ?>">

  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column is-2">

        <?php require 'ice_author_menu.php' ?>
          
        <div class="column" id="posts">

          <h1 class="title author_title">コメントの管理</h1>

          <div v-if="selected">
            <section class="section">

              <h2 class="title is-2">{{ selectedPost.title }}</h2>
              <table class="table">
                <tbody>
                  <tr v-for="comment in comments" class="content">
                    <td>
                      <button v-on:click="deleteComment(comment)" class="button is-danger">削除</button>
                    </td>
                    <td>
                      <p class="title is-5">
                        {{ comment.title }}
                        <span class="subtitle is-6"> by {{ comment.name }}</span>
                      </p>
                      <div>
                        {{ comment.comment_text }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

            </section>
          </div>
          <div v-else>
            <div class="post content" v-for="post in posts">
              <a v-on:click="selectPost(post)">
                <h1 class="title is-2">
                  {{ post.title }}
                </h1>
              </a>
              <p class="content-text">{{ post.content_text | letter400 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    var simplemde;

    Vue.filter('letter400', function (value) {
        if (!value) return ''
        value = value.toString();
        return value.slice(0, 400);
    });

    var app = new Vue({
      el: '#posts',
      data: {
        selected: false,
        posts: [],
        selectedPostId: null,
        selectedPost: null,
        comments: []
      },
      methods: {
        add: function(post) {
          this.posts.push(post);
        },
        setComments: function(comments) {
          this.comments = comments;
        },
        selectPost: function(post) {
          this.selectedPost = post;
          this.selectedPostId = post.post_id;
          this.selected = true;
          pullComments(post.post_id);
        },
        deleteComment: function(comment) {
          axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_comment.php?delete=' + comment.comment_id)
            .then(function (response) {
              console.log(response);
              pullComments(app.selectedPostId);
            })
            .catch(function (error) {
              console.log(error);
            });
        }
      }
    });

    function pullComments(post_id) {
      app.setComments([]);
      axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_comment.php?post_id=' + post_id)
        .then(function (response) {
          console.log(response);
          app.setComments(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    var author_id = document.body.dataset.author_id;

    axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_post.php?author_id=' + author_id)
      .then(function (response) {
        console.log(response);
        response.data.forEach(app.add);
      })
      .catch(function (error) {
        console.log(error);
      });

  </script>
</body>
</html>
