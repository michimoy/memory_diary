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
$otherUserMemoryData = getOtherMemoryCount($u_id);

if (empty($otherUserdata)) {
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = $otherUserdata['name'].'のProfile';
require('head.php');
?>

<!-- ヘッダー -->
<?php
  require('header.php');
?>

<body class="page-1colum page-profDetail">
<!-- メインコンテンツ -->
<div id="contents" class="site-width">
  <!-- Main -->
  <section id="main" >
    <h1 class="page-title">Profile</h1>
    <div class="profcard-container">
      <img class="background-img" src="<?php echo showImg($otherUserdata['background_img']); ?>" alt="">
      <div class="prof-img">
        <img src="<?php echo $otherUserdata['pic']; ?>" alt="">
      </div>
      <h2 class="profcard-name" style="text-align:center;"><?php echo $otherUserdata['name']; ?></h2>
      <div class="profcard-my_comment">
        <?php echo $otherUserdata['my_comment']; ?>
      </div>
      <div class="profcard-countarea">
        <p><?php echo '投稿数:'.$otherUserMemoryData['memory_count']; ?></p>
      </div>
      <div class="item-left" style="text-align:center;">
        <a href="index.php<?php echo appendGetParam(array('u_id')); ?>">&lt; 思い出一覧に戻る</a>
      </div>
    </div>
  </section>
</div>

<?php
require('footer.php');
 ?>
