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
  <title>Hello Bulma!</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <link rel="stylesheet" href="simplemde.min.css">
  <script src="simplemde.min.js"></script>
  <script src="html2plaintext.js"></script>

  <link rel="stylesheet" href="inject.css">
</head>

<body>

  <section class="section">
    <div class="container">
      <div class="columns is-fullheight">
        <div class="column is-2">

          <div class="columns">
            <div class="column">
              <figure class="image is-64x64">
                <img alt="icon" src="<?php echo getData('image_uri'); ?>" />
              </figure>
              <p>id: <?php echo getData('screen_name'); ?></p>
            </div>
          </div>

          <aside class="menu">
            <p class="menu-label">
              General
            </p>
            <ul class="menu-list">
              <li><a href="ice_author_profile.php">プロフィール設定</a></li>
              <li><a href="newpost.php">記事の新規作成</a></li>
              <li><a href="edit.php" class="is-active">記事の編集</a></li>
            </ul>
          </aside>
        </div>

        <div class="column" id="posts">
          <div v-if="selected">
            <div class="level">
              <div class="level-left">

                <div class="level-item">
                  <div class="control">
                    <button v-on:click="deletePost()" type="submit" class="button is-danger">この記事を削除</button>
                  </div>
                </div>

              </div>
              <div class="level-right">
                <div class="level-item">
                  <div class="control">
                    <div class="select">
                      <select name="status">
                        <option value="publish">公開</option>
                        <option value="unpublish">非公開</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="level-item">
                  <div class="control">
                    <button v-on:click="updatePost()" type="submit" class="button is-info">更新</button>
                  </div>
                </div>

              </div>
            </div>
            <input name="title" v-bind:value="edit.title" placeholder="Title">
            <textarea></textarea>
          </div>
          <div v-else>
            <div class="post content" v-for="post in posts">
              <a v-on:click="selectPost(post)">
                <h1>
                  {{ post.title }}
                </h1>
              </a>
              <p>
                {{ post.content_text }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    var simplemde;

    var app = new Vue({
      el: '#posts',
      data: {
        selected: false,
        posts: [],
        editPostId: null,
        edit: {}
      },
      methods: {
        add: function(post) {
          this.posts.push(post);
        },
        selectPost: function(post) {
          this.edit = post;
          this.editPostId = post.post_id;
          this.selected = true;
          setTimeout((function(post) {
            simplemde = new SimpleMDE();
            simplemde.value(post.content);
          }).bind(null, post), 200);
        },
        updatePost: function() {
          this.edit.title = document.querySelector('input[name="title"]').value;
          this.edit.content = simplemde.value();
          this.edit.content_text = (function() {
            var e = document.createElement('div');
            e.innerHTML = SimpleMDE.prototype.markdown(simplemde.value() );
            console.log( html2plaintext(e) );
            return html2plaintext(e);
          }());
          this.edit.content_html = SimpleMDE.prototype.markdown(simplemde.value());
          this.edit.status = document.querySelector('select')[document.querySelector('select').selectedIndex].value;
          if (this.edit.title === "") return false;
          if (this.edit.content === "") return false;

          var form = new FormData();

          form.append("post_id", this.editPostId);
          form.append("title", this.edit.title);
          form.append("content", this.edit.content);
          form.append("content_text", this.edit.content_text);
          form.append("content_html", this.edit.content_html);
          form.append("status", this.edit.status);

          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            console.log(this.responseText);
            // location.reload();
          };
          xhr.open("POST", window.location.href);
          xhr.send(form);
        },
        deletePost: function() {
          var form = new FormData();
          form.append("delete", this.editPostId);
          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            console.log(this.responseText);
            location.reload();
          };
          xhr.open("POST", window.location.href);
          xhr.send(form);
        }
      }
    });

    axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_post.php')
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
