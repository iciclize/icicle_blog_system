<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ログイン - Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <link rel="stylesheet" href="simplemde.min.css">
  <script src="simplemde.min.js"></script>

  <link rel="stylesheet" href="inject.css">
</head>

<body>
  <div class="section">
    <div class="container">
      <h1 class="title">ログイン</h1>
      <form action="ice_setcookie.php" method="GET">
        <input type="hidden" name="redirect_source" value="<?php echo $_GET['redirect_source'] ?>">
        <div class="field">
          <label class="label">ID</label>
          <div class="control">
            <input class="input" type="text" name="screen_name">
          </div>
        </div>

        <div class="field">
          <label class="label">Password</label>
          <div class="control">
            <input class="input" type="password" name="password">
          </div>
        </div>

        <div class="field">
          <div class="control">
            <input class="button is-primary" type="submit">
          </div>
        </div>
      </form>

      <p><a href="./ice_create_author.php">新規ユーザー登録</a></p>

    </div>


  </div>
</body>

</html>
