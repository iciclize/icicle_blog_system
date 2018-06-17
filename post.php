<?php
  require 'ice_mysqli_init.php';

  if ( !isset($_GET['post_id']) ) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/');
    exit();
  } elseif ( !ctype_digit($_GET['post_id']) ) {
    // 数字でなかった場合
    // 404
    exit();
  }

  $sql = 'SELECT * FROM ice_post WHERE post_id=' . $_GET['post_id'];
  $res = $mysqli->query($sql);

  if (!$res) {
    exit();
  }

  $entry = json_encode($res->fetch_assoc());

  $sql = 'SELECT tag.* '.
  'FROM ice_post post '.
  'LEFT JOIN ice_tag_map map on post.post_id = map.post_id '.
  'LEFT JOIN ice_tag tag on map.tag_id = tag.tag_id '.
  'WHERE post.post_id = ' . $_GET['post_id'] . ";";
  $res = $mysqli->query($sql);

  if (!$res) exit();

  $tags = $res->fetch_all(MYSQLI_ASSOC);
  foreach ($tags as &$tag) {
    $tag['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_tag.php?tag_id=" . $tag['tag_id'];
  }
  $tags = json_encode($tags);

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

  </section>

  <article class="article"
    v-init:post="<?php echo htmlspecialchars($entry); ?>"
    v-init:tags="<?php echo htmlspecialchars($tags); ?>" >

    <section class="section">
      <div class="columns">
        <div class="column content is-three-fifths is-offset-one-fifth">
          <h1 class="title entry-title">
            {{ post.title }}
          </h1>
          <div class="level">
            <div class="level-left">
              <div class="tags">
                <a v-for="tag in tags" v-bind:href="tag.uri">
                  <span>{{ tag.tag_name }}</span>
                </a>
              </div>
            </div>
          </div>
          <div v-html="post.content_html" />
        </div>
      </div>
    </section>

  </article>

  <script>
    Vue.directive('init', {
      bind: function(el, binding, vnode) {
        vnode.context[binding.arg] = binding.value;
      }
    });

    var vm = new Vue({
      el: '.article',
      data: {
        post: {},
        tags: []
      }
    });
  </script>
</body>

</html>
