<?php require 'ice_getNames.php'; ?>
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

  $entry = $res->fetch_assoc();
  $entry['author_uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/search.php?author_id=".$entry['author_id'];
  $entry['author_img'] = getAuthorImageURI($entry['author_id']);
  $entry['author_name'] = getAuthorName($entry['author_id']);
  $entry['author_biography'] = getAuthorBiography($entry['author_id']);

  $sql = "SELECT tag.* ".
  "FROM ice_tag_map map ".
  "LEFT JOIN ice_tag tag on map.tag_id = tag.tag_id ".
  "LEFT JOIN ice_post post on map.post_id = post.post_id ".
  "WHERE post.post_id = " . $_GET['post_id'] . ";" ;

  $res = $mysqli->query($sql);

  $tags = $res->fetch_all(MYSQLI_ASSOC);
  foreach ($tags as &$tag) {
    $tag['uri'] = "http://turkey.slis.tsukuba.ac.jp/~s1711430/search.php?tag_id=" . $tag['tag_id'];
  }

  $tags = $tags;

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($entry['title']); ?> - Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body>
  <section class="hero is-info">
    <?php require 'ice_hero_head.php'; ?>
  </section>

  <article class="article"
    v-init:post="<?php echo htmlspecialchars(json_encode($entry)); ?>"
    v-init:tags="<?php echo htmlspecialchars(json_encode($tags)); ?>" >

    <section class="section container">
      <div class="columns">
        <div class="column content is-7 is-offset-1">

          <article>
            <h1 class="title entry-title">
              {{ post.title }}
            </h1>
            <div class="level">
              <div class="level-left">
                <div class="tags">
                  <a v-for="tag in tags" class="tag is-info" v-bind:href="tag.uri">
                    {{ tag.tag_name }}
                  </a>
                </div>
              </div>
              <div class="level-right">
                <div>
                  <span>公開: {{ post.published }}</span><br>
                  <span v-if="post.modified">最終更新: {{ post.modified }}</span>
                </div>
              </div>
            </div>
            <div v-html="post.content_html" />
          </div>
        </article>
      </div>

      <div class="column is-3">
        <div class="card">
          <div class="card-content">
            <div class="media">
              <div class="media-left">
                <a v-bind:href="post.author_uri">
                  <figure class="image is-48x48">
                    <img v-bind:src="post.author_img" alt="Placeholder image">
                  </figure>
                </a>
              </div>
              <div class="media-content">
                <p class="title is-4">
                  <a class="title is-4"v-bind:href="post.author_uri">
                    {{ post.author_name }}
                  </a>
                </p>
                <p class="subtitle is-6">{{ post.author_biography }}</p>
              </div>
            </div>
          </div>
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
