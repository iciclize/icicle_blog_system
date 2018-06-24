<?php
  // 貴様はログインしているかオラッ！

  require 'ice_validate.php';
  if (!validate()) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_login.php?redirect_source=edit.php');
  }
?>

<?php require 'ice_getData.php'; ?>

<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'ice_mysqli_init.php';
    $isDelete = isset($_POST['delete']);
    if ($isDelete) {
      if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
        $post_id = $_POST['delete'];
        // 削除
        $stmt = $mysqli->prepare("DELETE FROM ice_post WHERE post_id=?");
        $stmt->bind_param('i', $post_id);
        $stmt->execute();

        $stmt = $mysqli->prepare("DELETE FROM ice_tag_map WHERE post_id=?");
        $stmt->bind_param('i', $post_id);
        $stmt->execute();

        $stmt = $mysqli->prepare("DELETE FROM ice_comment WHERE post_id=?");
        $stmt->bind_param('i', $post_id);
        $stmt->execute();

        $stmt->close();
      }
    } else {
      // 編集
      $title = $_POST['title'];
      $content = $_POST['content'];
      $content_text = $_POST['content_text'];
      $content_html = $_POST['content_html'];
      $status = $_POST['status'] == 'publish' ? 1 : 0;
      $post_id = $_POST['post_id'];
      $tag_list = split(",", $_POST['tag_list']);

      if ($title == "") $title = "No Title";

      $stmt = $mysqli->prepare("SELECT * FROM ice_post WHERE post_id=?");
      $stmt->bind_param('i', $post_id);
      $stmt->execute();

      $isPublished = $stmt->get_result()->fetch_array(MYSQLI_ASSOC)['published'] ? true : false;
      $isFirstPublishing = !$isPublished && $status == 1;

      $query = "UPDATE ice_post SET ".
      "`title`=?, ".
      "`content`=?, ".
      "`content_text`=?, ".
      "`content_html`=?, ".
      "`status`=? ".
      (
        ($isFirstPublishing)
          ? ", `published`=cast( now() as datetime) "
          : ""
      ).
      ( 
        ($isPublished && $status == 1)
          ? ", `modified`=cast( now() as datetime) "
          : ""
      ).
      "WHERE `post_id`=?";

      $stmt = $mysqli->prepare($query);
      $stmt->bind_param('ssssii', $title, $content, $content_text, $content_html, $status, $post_id);
      $stmt->execute();

      $query = "DELETE FROM ice_tag_map WHERE post_id=?";
      $stmt = $mysqli->prepare($query);
      $stmt->bind_param('i', $post_id);
      $stmt->execute();

      $query = "INSERT INTO ice_tag_map (post_id, tag_id) VALUES (?, ?)";
      $stmt = $mysqli->prepare($query);
      foreach($tag_list as $tag_id) {
        $stmt->bind_param('ii', $post_id, $tag_id);
        $stmt->execute();
      }
      $stmt->close();

    }
  }
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>記事の編集 - Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <link rel="stylesheet" href="simplemde.min.css">
  <script src="simplemde.min.js"></script>
  <script src="html2plaintext.js"></script>
  <script src="https://unpkg.com/vue@latest"></script>
  <script src="https://unpkg.com/vue-select@latest"></script>

  <link rel="stylesheet" href="inject.css">
</head>

<body data-author_id="<?php echo htmlspecialchars(getData('author_id')) ?>">

  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column is-2">

        <?php require 'ice_author_menu.php' ?>
          
        <div class="column" id="posts">

          <h1 class="title author_title">記事の編集</h1>

          <div v-if="editing">
            <div class="level">
              <div class="level-left">

                <div class="level-item">
                  <div class="control">
                    <button v-on:click="deletePost()" type="submit" class="button is-danger">この記事を削除</button>
                  </div>
                </div>

              </div>
              <div class="level-right">
                <div class="level-item">
                  <div class="control">
                    <div class="select">
                      <select name="status">
                        <option value="publish">公開</option>
                        <option value="unpublish">非公開</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="level-item">
                  <div class="control">
                    <button v-on:click="updatePost()" type="submit" class="button is-info">更新</button>
                  </div>
                </div>

              </div>
            </div>
            <input name="title" v-bind:value="edit.title" placeholder="Title">
            <v-select multiple v-model="selected" :options="tags"></v-select>
            <textarea></textarea>
          </div>
          <div v-else>
            <div class="post content" v-for="post in posts">
              <a v-on:click="selectPost(post)">
                <h1 class="title is-2">
                  {{ post.title }}
                </h1>
              </a>
              <p class="content-text">
                {{ post.content_text | letter400 }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    var simplemde;
    Vue.component('v-select', VueSelect.VueSelect);

    Vue.filter('letter400', function (value) {
        if (!value) return ''
        value = value.toString();
        return value.slice(0, 400);
    });

    var app = new Vue({
      el: '#posts',
      data: {
        author_id: null,
        editing: false,
        posts: [],
        editPostId: null,
        edit: {},
        tags: [],
        selected: []
      },
      methods: {
        add: function(post) {
          this.posts.push(post);
        },
        setTag: function(tags) {
          tags.forEach(function(e, i, arr) {
            arr[i].label = e.tag_name;
          });
          this.tags = tags;

          this.edit.tags.forEach(function(tag) {
            this.selected.push(tags[tags.findIndex(function(t) {
              return t.tag_id == tag.tag_id;
            })]);
          }.bind(this));

        },
        selectPost: function(post) {
          this.edit = post;
          console.log(post.tags);
          this.editPostId = post.post_id;
          this.editing = true;

          axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_tag.php')
            .then(function (response) {
              console.log(response);
              app.setTag(response.data);
            })
            .catch(function (error) {
              console.log(error);
            });

          setTimeout((function(post) {
            document.querySelector('select[name="status"]').selectedIndex = (post.status == 0) ? 1 : 0;
            simplemde = new SimpleMDE();
            simplemde.value(post.content);
          }).bind(null, post), 145);
        },
        updatePost: function() {
          this.edit.title = document.querySelector('input[name="title"]').value;
          this.edit.content = simplemde.value();
          this.edit.content_text = (function() {
            var e = document.createElement('div');
            e.innerHTML = SimpleMDE.prototype.markdown(simplemde.value() );
            return html2plaintext(e);
          }());
          this.edit.content_html = SimpleMDE.prototype.markdown(simplemde.value());
          this.edit.status = document.querySelector('select')[document.querySelector('select').selectedIndex].value;
          if (this.edit.title === "") return false;
          if (this.edit.content === "") return false;

          var form = new FormData();

          form.append("post_id", this.editPostId);
          form.append("title", this.edit.title);
          form.append("content", this.edit.content);
          form.append("content_text", this.edit.content_text);
          form.append("content_html", this.edit.content_html);
          form.append("status", this.edit.status);
          form.append("tag_list", this.selected.map(function(tag) {
            return tag.tag_id;
          }).join(',') );

          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            console.log(this.responseText);
            location.reload();
          };
          xhr.open("POST", window.location.href);
          xhr.send(form);
        },
        deletePost: function() {
          var form = new FormData();
          form.append("delete", this.editPostId);
          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            console.log(this.responseText);
            location.reload();
          };
          xhr.open("POST", window.location.href);
          xhr.send(form);
        }
      }
    });

    var author_id = document.body.dataset.author_id;

    axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_post.php?author_id=' + author_id)
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
