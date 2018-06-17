<?php
  setcookie('screen_name', $_GET['screen_name'], 0, "/~s1711430/");
  setcookie('password', $_GET['password'], 0, "/~s1711430/");

  header('Location: http://turkey.slis.tsukuba.ac.jp/~s1711430/'.$_GET['redirect_source']);
?>



