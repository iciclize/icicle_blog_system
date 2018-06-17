<?php
  // 新規著者の登録に成功するとice_author_profile.phpにリダイレクトされます

  function insert($name, $screen_name, $password) {
    require 'ice_mysqli_init.php';
    $stmt = $mysqli->prepare("INSERT INTO ice_author (name, screen_name, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $screen_name, $password);
    $isInserted = $stmt->execute();
    $stmt->close();
    return $isInserted;
  }

  function isScreenNameOverlapped($screen_name) {
    require 'ice_mysqli_init.php';
    $stmt = $mysqli->prepare("SELECT screen_name FROM ice_author WHERE screen_name=?");
    $stmt->bind_param('s', $screen_name);
    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;
    $stmt->close();
    return ($num_rows == 0);
  }

  if (isset($_POST['name'])
  && isset($_POST['screen_name'])
  && isset($_POST['password']) ) {

    $name = $_POST["name"];
    $screen_name = $_POST["screen_name"];
    $password = $_POST["password"];

    if ( !isScreenNameOverlapped($screen_name) ) {
      if ( insert($name, $screen_name, $password) ) {
        header("Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_profile_settings.php");
      }
    }
  }

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>New Author</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body>

  <section class="hero is-info">
    <div class="hero-head">
      <nav class="navbar">
        <div class="container">
          <div class="navbar-brand">
            <a class="navbar-item">
              <img src="https://bulma.io/images/bulma-type-white.png" alt="Logo" />
            </a>
            <span class="navbar-burger burger" data-target="navbarMenuHeroA">
              <span></span>
              <span></span>
              <span></span>
            </span>
          </div>
          <div id="navbarMenuHeroA" class="navbar-menu">
            <div class="navbar-end">
              <a class="navbar-item is-active">Home</a>
              <a class="navbar-item">Examples</a>
              <a class="navbar-item">Documentation</a>
              <span class="navbar-item">
                <a class="button is-info is-inverted">
                  <span class="icon">
                    <i class="fab fa-github"></i>
                  </span>
                  <span>Download</span>
                </a>
              </span>
            </div>
          </div>
        </div>
      </nav>
    </div>

    <div class="hero-body">
      <div class="container has-text-centered">
        <h1 class="title">
          Create new author
        </h1>
      </div>
    </div>

  </section>
 

  <div class="section">
    <form action="ice_create_author.php" method="POST">

      <div class="field">
        <label class="label">Name</label>
        <div class="control">
          <input class="input" type="text" name="name">
        </div>
      </div>

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
  </div>


</body>

</html>
