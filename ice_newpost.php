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
    $author_id = getData('author_id');
    $title = $_POST['title'];
    $content = $_POST['content'];
    $content_text = $_POST['content_text'];
    $content_html = $_POST['content_html'];
    $status = $_POST['status'] == 'publish' ? 1 : 0;

    $query = "INSERT INTO ice_post ".
    ( ($status == 1)
        ? "(author_id, title, content, content_text, content_html, status, published) ".
          "VALUES (?, ?, ?, ?, ?, ?, cast( now() as datetime) )"
        : "(author_id, title, content, content_text, content_html, status) ".
          "VALUES (?, ?, ?, ?, ?, ?)"
    );

    var_dump($query);

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssssii', $author_id, $title, $content, $content_text, $content_html, $status);
    $stmt->execute();
    $stmt->close();

  }
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>記事の新規作成 - Icicle Blog System</title>
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

        <?php require 'ice_author_menu.php' ?>

        <div class="column" id="post">

          <h1 class="title author_title">記事の新規作成</h1>

            <div class="level">
              <div class="level-left">
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
                    <button v-on:click.prevent="updatePost" type="submit" class="button is-info">記事を投稿する</button>
                  </div>
                </div>
              </div>
            </div>

            <input name="title" v-bind:value="edit.title" placeholder="Title" required>
            <textarea></textarea>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    var simplemde;

    document.addEventListener('DOMContentLoaded', function () {
      simplemde = new SimpleMDE();
    });

    var app = new Vue({
      el: '#post',
      data: {
        edit: {
          title: '',
          content: '',
          content_text: '',
          content_html: '',
          status: 0
        }
      },
      methods: {
        updatePost: function() {
          this.edit.title = document.querySelector('input[name="title"]').value;
          this.edit.content = simplemde.value();
          this.edit.content_text = (function() {
            var e = document.createElement('div');
            e.innerHTML = SimpleMDE.prototype.markdown(simplemde.value());
            console.log( html2plaintext(e) );
            return html2plaintext(e);
          }());
          this.edit.content_html = SimpleMDE.prototype.markdown(simplemde.value());
          this.edit.status = document.querySelector('select')[document.querySelector('select').selectedIndex].value;
          console.log(this.edit.title, this.edit.content, this.edit.content_text, this.edit.content_html, this.edit.status, this.editPostId);

          if (this.edit.title === "") return false;
          if (this.edit.content === "") return false;

          var form = new FormData();

          form.append("title", this.edit.title);
          form.append("content", this.edit.content);
          form.append("content_text", this.edit.content_text);
          form.append("content_html", this.edit.content_html);
          form.append("status", this.edit.status);

          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            console.log(this.responseText);
            window.location.href = "http://turkey.slis.tsukuba.ac.jp/~s1711430/edit.php";
          };
          xhr.open("POST", window.location.href);
          xhr.send(form);
        }
      }
    });

  </script>
</body>
</html>
