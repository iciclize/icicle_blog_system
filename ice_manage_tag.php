<?php require 'ice_getData.php'; ?>
<?php
  // 貴様はログインしているかオラッ！

  require 'ice_validate.php';
  if (!validate()) {
    header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_login.php?redirect_source=edit.php');
  }
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>タグの管理- Icicle Blog System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

  <link rel="stylesheet" href="inject.css">
</head>

<body>

  <section class="section">
    <div class="container">
      <div class="columns is-fullheight">
        <div class="column is-2">

        <?php require 'ice_author_menu.php' ?>
          
        <div class="column" id="tags">

          <h1 class="title author_title">タグの管理</h1>

          <table class="table">
            <tbody>
              <tr v-for="tag in tags">
                <td>
                  <p v-on:click="modifyTagName(tag, true)" v-if="!tag.isModifying">{{ tag.tag_name }}</p>
                  <input required v-on:blur="modifyTagName(tag, false)" v-on:change="modifyTagName(tag, false)" v-model="tag.tag_name" v-if="tag.isModifying" class="input" />
                </td>
                <td>
                  <button v-on:click="deleteTag(tag)" class="button is-danger">削除</button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td>
                  <input name="newtag" class="input" />
                </td>
                <td>
                  <button v-on:click="createTag()" class="button is-info">タグの追加</button>
                </td>
              </tr>
            </tfoot>
          </table>

        </div>
      </div>
    </div>
  </section>

  <script>
    var app = new Vue({
      el: '#tags',
      data: {
        tags: []
      },
      methods: {
        add: function(tag) {
          tag.isModifying = false;
          this.tags.push(tag);
        },
        deleteTag: function(tag) {
          var xhr = new XMLHttpRequest();
          xhr.onload = updateTagList;
          xhr.open('GET', 'http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_tag.php?delete_tag={{X}}'
            .replace("{{X}}", tag.tag_id) );
          xhr.send();
        },
        createTag: function() {
          var tag = document.querySelector('input[name="newtag"]');
          if (tag.value == '') return;

          var xhr = new XMLHttpRequest();
          xhr.onload = updateTagList;
          xhr.open('GET', 'http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_tag.php?create_tag={{X}}'
            .replace("{{X}}", tag.value) );
          tag.value = '';
          xhr.send();
        },
        modifyTagName: function(e, s) {
          this.tags.forEach(function(tag) {
            tag.isModifying = false;
          });
          e.isModifying = s;
          if (s) return;
          var xhr = new XMLHttpRequest();
          xhr.onload = updateTagList;

          xhr.open('GET', 'http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_tag.php?edit_tag={{X}}&tag_id={{Y}}'
            .replace("{{X}}", e.tag_name).replace("{{Y}}", e.tag_id) );
          xhr.send();
        }
      }
    });

    function updateTagList() {
      while (app.tags.length > 0) app.tags.pop();

      axios.get('http://turkey.slis.tsukuba.ac.jp/~s1711430/ice_tag.php')
        .then(function (response) {
          console.log(response);
          response.data.forEach(app.add);
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    updateTagList();

  </script>
</body>
</html>
