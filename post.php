<?php require 'ice_getNames.php'; ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars(getPostTitle($_GET['post_id'])); ?> - Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <link rel="stylesheet" href="inject.css">
  <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body
  data-post_id="<?php echo htmlspecialchars($_GET['post_id']); ?>"
>
  <section class="hero is-info">
    <?php require 'ice_hero_head.php'; ?>
  </section>

  <article class="article">

    <section class="section container">
      <div class="columns">
        <div class="column is-7 is-offset-1">

            <h1 class="title entry-title">
              {{ post.title }}
            </h1>
            <div class="tags">
              <a v-for="tag in post.tags" class="tag is-info" v-bind:href="tag.uri">
                {{ tag.tag_name }}
              </a>
            </div>
            <h2 class="subtitle is-6">
              <span>公開: {{ post.published }}</span><br>
              <span v-if="post.modified">最終更新: {{ post.modified }}</span>
            </h2>
            <div class="content" v-html="post.content_html" />
          </div>

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
                <p class="subtitle is-6 content-text">{{ post.author_biography }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

    <section class="container">
      <div class="columns">
        <div class="column is-7 is-offset-1">

          <section class="section">

            <h2 class="title is-4">コメント</h2>
            <div v-for="comment in comments" class="content">
              <p class="title is-5">
                {{ comment.title }}
                <span class="subtitle is-6"> by {{ comment.name }}</span>
              </p>
              <div class="comment">{{ comment.comment_text }}</div>
            </div>

          </section>

          <section class="section card">

            <h2 class="title is-4">コメントを残す</h2>

            <form action="ice_comment.php" method="POST">
              <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['post_id']); ?>" />
              <div class="columns">

                <div class="column is-three-fifths">
                  <div class="field">
                    <label class="label">タイトル</label>
                    <div class="control">
                      <input required class="input" type="text" name="title">
                    </div>
                  </div>
                </div>

                <div class="column is-two-fifths">
                  <div class="field">
                    <label class="label">お名前</label>
                    <div class="control">
                      <input required class="input" type="text" name="name">
                    </div>
                  </div>
                </div>

              </div>

              <div class="field">
                <label class="label">コメント</label>
                <div class="control">
                  <textarea required class="textarea" name="comment_text"></textarea>
                </div>
              </div>
              
              <button type="submit" class="button is-info">コメントを投稿する</button>

            </form>

          </section>

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

    var app = new Vue({
      el: '.article',
      data: {
        post: {},
        comments: []
      },
      methods: {
        pushComment: function(comment) {
          this.comments.push(comment);
        }
      }
    });

    axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_post.php?post_id='
      + document.body.dataset.post_id )
      .then(function (response) {
        console.log(response);
        app.post = response.data;
      })
      .catch(function (error) {
        console.log(error);
      });

    axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_comment.php?post_id='
      + document.body.dataset.post_id )
      .then(function (response) {
        console.log(response);
        response.data.forEach(app.pushComment);
      })
      .catch(function (error) {
        console.log(error);
      });

  </script>
</body>

</html>
