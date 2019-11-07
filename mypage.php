<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');
 ?>
<?php
$siteTitle = 'Mypage';
require('head.php');
?>

<body class="page-2colum">
<?php
require('header.php')
?>

<p id="js-show-msg" style="display:none;" class="msg-slide">
  <?php echo getSessionFlash('msg_success'); ?>
</p>

<div id="contents" class="site-width">
  <h1 class="page-title">MYPAGE</h1>
  <section id="main">

  </section>
  <!-- サイドバー -->
  <?php
    require('sidebar_mypage.php');
  ?>
</div>
<!-- footer -->
<?php
require('footer.php');
?>
