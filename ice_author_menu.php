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
              <li><a href="ice_newpost.php">記事の新規作成</a></li>
              <li><a href="edit.php">記事の編集</a></li>
            </ul>
            <p class="menu-label">CONTROL</p>
            <a href="ice_setcookie.php" class="button is-danger">ログアウト</a>
          </aside>
        </div>

        <script>
          document.addEventListener('DOMContentLoaded', function() {
            var anchors = document.querySelectorAll('.menu-list>li>a');
            [].find.call(anchors, function(e) {
              if (new URL(e.href).pathname != location.pathname) return false;
              e.classList.add('is-active');
              return true;
            });
          });
        </script>
