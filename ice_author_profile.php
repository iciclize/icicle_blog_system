<?php
  require 'ice_validate.php';
  if (!validate()) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_login.php?redirect_source=ice_author_profile.php');
  }
?>

<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'ice_mysqli_init.php';
    $screen_name = $_COOKIE["screen_name"];
    $password = $_COOKIE["password"];

    $stmt = $mysqli->prepare("SELECT author_id FROM ice_author WHERE screen_name=? and password=?");
    $stmt->bind_param('ss', $screen_name, $password);
    $stmt->execute();

    $author_id = $stmt->get_result()->fetch_object()->author_id;

    $stmt->close();

    if (isset($_FILES['profile_img']['error']) && is_int($_FILES['profile_img']['error'])) {

      if ($_FILES['profile_img']['error'] == 0) {
        $filepath = "./images/".$author_id."_profile.".pathinfo($_FILES['profile_img']['name'])['extension'];

        $stmt = $mysqli->prepare("UPDATE ice_author SET `image_uri`=? WHERE author_id=?");
        $stmt->bind_param('si', $filepath, $author_id);
        $stmt->execute();
        $stmt->close();

        move_uploaded_file($_FILES['profile_img']['tmp_name'], $filepath) or die("Coudn't copy");
      }
    }

    $newname = $_POST['name'];
    $newbio = $_POST['biography'];
    
    $stmt = $mysqli->prepare("UPDATE ice_author SET `name`=?,`biography`=? WHERE author_id=?");
    $stmt->bind_param('ssi', $newname, $newbio, $author_id);
    $stmt->execute();
    $stmt->close();

  }
?>

<?php require 'ice_getData.php'; ?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hello Bulma!</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

</head>

<body>

  <section class="section">
    <div class="container">
      <div class="columns is-fullheight">
        <div class="column is-2">

          <div class="columns">
            <div class="column">
              <figure class="image is-64x64">
                <img alt="icon" src="<?php echo getData('image_uri').'?'.time(); ?>" />
              </figure>
              <p>id: <?php echo getData('screen_name'); ?></p>
            </div>
          </div>

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

        <form action="ice_author_profile.php" method="POST" enctype="multipart/form-data">

          <div class="field">
            <label class="label">Name</label>
            <div class="control">
              <input name="name" class="input" type="text" placeholder="Your Name" value="<?php echo getData('name'); ?>">
            </div>
          </div>

          <div class="field">
            <label class="label">Biography / 自己紹介</label>
            <div class="control">
              <textarea name="biography" class="textarea" placeholder="Biography"><?php echo getData('biography'); ?></textarea>
            </div>
          </div>

          <div class="field">
            <label class="label">プロフィール画像</label>
            <figure class="image is-64x64">
              <img id="icon-preview" alt="icon" src="<?php echo getData('image_uri').'?'.time(); ?>" />
            </figure>
            <div class="file has-name">
              <label class="file-label">
                <input class="file-input" type="file" name="profile_img">
                <span class="file-cta">
                  <span class="file-icon">
                    <i class="fas fa-upload"></i>
                  </span>
                  <span class="file-label">
                    プロフィール画像の変更
                  </span>
                </span>
                <span class="file-name">
                </span>
              </label>
            </div>
            <script>
              document.addEventListener('DOMContentLoaded', function(e) {
                document.querySelector('.file-input').addEventListener('change', function() {
                  var reader = new FileReader();
                  reader.addEventListener('load', function() {
                    document.querySelector('#icon-preview').src = reader.result;
                  });
                  reader.readAsDataURL(e.target.activeElement.files[0]);
                  document.querySelector('.file-name').innerText = e.target.activeElement.files[0].name;
                });
              });
            </script>
          </div>

          <div class="field is-grouped">
            <div class="control">
              <button type="submit" class="button is-link">Submit</button>
            </div>
          </div> 

        </form>

        </div>
      </div>
    </div>
  </section>

</body>
</html>
