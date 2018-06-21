<?php
  require 'ice_validate.php';
  if (!validate()) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_login.php?redirect_source=ice_author_profile.php');
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
              <li><a href="ice_author_profile.php" class="is-active">プロフィール設定</a></li>
              <li><a href="newpost.php">記事の新規作成</a></li>
              <li><a href="edit.php">記事の編集</a></li>
            </ul>
          </aside>
        </div>

        <div class="column">
        </div>
      </div>
    </div>
  </section>

</body>
</html>
