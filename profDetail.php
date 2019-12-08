<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// ユーザIDのGETパラメータを取得
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
// DBからユーザーデータを取得
$otherUserdata = getOtherUser($u_id);

// if (empty($otherUserdata)) {
//   error_log('エラー発生:指定ページに不正な値が入りました');
//   header("Location:index.php"); //トップページへ
// }

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = $otherUserdata['name'].'のプロフィール';
require('head.php');
?>

<!-- ヘッダー -->
<?php
  require('header.php');
?>

<body class="profDetail page-1colum">
<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  <!-- Main -->
  <section id="main" >
    <h1><?php echo $otherUserdata['name'].'さんのプロフィール'?> </h1>
    <div class="profcard-area">
      <div class="profcard-area">

      </div>
    </div>
  </section>
</div>

<?php
require('footer.php');
 ?>
