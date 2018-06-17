<?php
  require 'ice_mysqli_init.php';

  if ( !isset($_GET['tag_id']) ) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/');
    exit();
  } elseif ( !ctype_digit($_GET['tag_id']) ) {
    // 数字でなかった場合
    // 404
    exit();
  }

  $sql = "SELECT tag_name ".
  "FROM ice_tag ".
  "WHERE tag_id = " . $_GET['tag_id'] . ";";
  $res = $mysqli->query($sql);
  
  if (!$res) {
    exit();
  }

  $tag = $res->fetch_assoc();

  $sql = "SELECT post.* ".
  "FROM ice_post post ".
  "LEFT JOIN ice_tag_map map on post.post_id = map.post_id ".
  "LEFT JOIN ice_tag tag on map.tag_id = tag.tag_id ".
  "WHERE tag.tag_id = " . $_GET['tag_id'] . ";";
  $res = $mysqli->query($sql);

  if (!$res) {
    exit();
  }
  
  $posts = $res->fetch_all(MYSQLI_ASSOC);
  
  foreach ($posts as &$post) {
    $post['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/post.php?post_id=" . $post['post_id'];
  }

?>
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

  <section class="hero is-info is-medium">
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
        <h1 class="title" v-init:tag="<?php echo htmlspecialchars(json_encode($tag)); ?>">
          {{ tag.tag_name }}
        </h1>
      </div>
    </div>

  </section>

  <section class="section" id="ice_articles"
    v-init:posts="<?php echo htmlspecialchars(json_encode($posts)); ?>" >
    <div class="columns" v-for="post in posts">
      <div class="column content is-three-fifths is-offset-one-fifth">
        <a v-bind:href="post.uri">
          <h1>
            {{ post.title }}
          </h1>
        </a>
        <p>
          {{ post.content_text }}
        </p>
      </div>
    </div>
  </section>

  <script>
    Vue.directive('init', {
      bind: function(el, binding, vnode) {
        vnode.context[binding.arg] = binding.value;
      }
    });

    var vm = new Vue({
      el: '#ice_articles',
      data: {
        posts: []
      }
    });

    new Vue({
      el: '.hero-body',
      data: {
        tag: {}
      }
    });

  </script>
</body>

</html>
