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
              <li><a class="is-active">記事の編集</a></li>
            </ul>
          </aside>
        </div>

        <div class="column" id="posts">
          <div v-if="selected">
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
                {{ post.content }}
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
          this.selected = true;
          setTimeout((function(post) {
            simplemde = new SimpleMDE();
            simplemde.value(post.content);
          }).bind(null, post), 200);
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
