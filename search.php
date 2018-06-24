<?php require 'ice_getNames.php'; ?>
<?php
  if (isset($_GET['author_id'])) {
    $given = $_GET['author_id'];
    $query_type = "author_id";
    $query_name = "著者";
    $query_filter = getAuthorName($given);
  } else if (isset($_GET['tag_id'])) {
    $given = $_GET['tag_id'];
    $query_type = "tag_id";
    $query_name = "タグ";
    $query_filter = getTagName($given);
  } else if (isset($_GET['keyword'])) {
    $given = $_GET['keyword'];
    $query_type = "keyword";
    $query_name = "キーワード";
    $query_filter = $given;
  }
  
  $query = $query_type."=".$given;

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $query_name.":".$query_filter; ?> - Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <link rel="stylesheet" href="inject.css" >
  <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body data-query="<?php echo htmlspecialchars($query); ?>">

  <section class="hero is-info is-medium">
    <?php require 'ice_hero_head.php'; ?>
    
    <div class="hero-body">
      <div class="container has-text-centered">
        <h1 class="title">
          <?php echo $query_name; ?>:
          <?php echo htmlspecialchars($query_filter); ?>
        </h1>
      </div>
    </div>

  </section>

  <section class="section" id="ice_articles">
    <div class="columns" v-for="post in posts">
      <div class="column content is-three-fifths is-offset-one-fifth">
        <article class="column">
          <h1>
            <a class="title" v-bind:href="post.uri">
              {{ post.title }}
            </a>
            <a v-bind:href="post.author_uri">
              <span class="subtitle is-6">
                by {{ post.author_name }}
              </span>
            </a>
          </h1>
          <p class="content-text">
            {{ post.content_text | letter400 }}
          </p>
          <div class="tags">
            <a v-for="tag in post.tags" class="tag is-info" v-bind:href="tag.uri">
              {{ tag.tag_name }}
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <script>
    Vue.filter('letter400', function (value) {
        if (!value) return ''
        value = value.toString();
        return value.slice(0, 400);
    });

    Vue.directive('init', {
      bind: function(el, binding, vnode) {
        vnode.context[binding.arg] = binding.value;
      }
    });

    var app = new Vue({
      el: '#ice_articles',
      data: {
        posts: []
      },
      methods: {
        add: function (post) {
          this.posts.push(post);
        }
      }
    });

    axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_post.php?'
      + document.body.dataset.query )
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
