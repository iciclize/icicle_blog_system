<?php
  // 貴様はログインしているかオラッ！

  require 'ice_validate.php';
  if (!validate()) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_login.php?redirect_source=edit.php');
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

  <link rel="stylesheet" href="inject.css">
</head>

<body>

  <section class="section">
    <div class="container">
      <div class="columns is-fullheight">
        <div class="column is-2">
          <aside class="menu">
            <p class="menu-label">
              General
            </p>
            <ul class="menu-list">
              <li><a href="ice_author_profile.php">プロフィール設定</a></li>
              <li><a href="newpost.php" class="is-active">記事の新規作成</a></li>
              <li><a href="edit.php">記事の編集</a></li>
            </ul>
          </aside>
        </div>

        <div class="column" id="posts">
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
                    <button v-on:click.prevent="updatePost()" type="submit" class="button is-info">更新</button>
                  </div>
                </div>
              </div>
            </div>
            <input name="title" v-bind:value="edit.title" placeholder="Title">
            <textarea></textarea>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    var simplemde = new SimpleMDE();

    var app = new Vue({
      el: '#post',
      data: {
      }
    });

  </script>
</body>
</html>
