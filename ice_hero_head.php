    <div class="hero-head">

      <nav class="navbar">
        <div class="container">
          <div class="navbar-brand">
            <a class="navbar-item" href="./"/>
              <img src="yjsnpi.png" alt="Logo" />
            </a>
            <span class="navbar-burger" data-target="navbarMenuHeroA">
              <span></span>
              <span></span>
              <span></span>
            </span>

            <script>
              document.addEventListener('DOMContentLoaded', function () {
                var $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
                if ($navbarBurgers.length == 0) return;
                $navbarBurgers.forEach(function ($el) {
                  $el.addEventListener('click', function () {
                    var target = $el.dataset.target;
                    var $target = document.getElementById(target);
                    $el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                  });
                });
              });
            </script>

          </div>

          <div id="navbarMenuHeroA" class="navbar-menu">

            <div class="navbar-item is-expanded">
              <div class="control is-expanded has-icons-left" style="flex-grow: 1;">
                <form method="GET" action="search.php">
                  <input name="keyword" type="search" class="input" placeholder="検索">
                </form>
                <span class="icon is-left">
                    <i class="fas fa-search"></i>
                </span>
              </div>
            </div>

            <div class="navbar-end">
              <a class="navbar-item" href="./ice_author_profile.php">ログイン</a>
              <a class="navbar-item" href="./ice_create_author.php">新規登録</a>
              <span class="navbar-item">
                <a class="button is-info is-inverted" href="./edit.php">
                  <span class="icon">
                    <i class="fas fa-edit"></i>
                  </span>
                  <span>記事を書く</span>
                </a>
              </span>
            </div>

          </div>
        </div>

      </nav>
    </div>

