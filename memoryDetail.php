<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　思い出詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 思い出IDのGETパラメータを取得
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
// DBから思い出データを取得
$viewMemoryData = getMemoryOne($m_id);
debug('取得したDBデータ：'.print_r($viewMemoryData,true));
// パラメータに不正な値が入っているかチェック
if(empty($viewMemoryData)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '思い出詳細';
require('head.php');
?>
<style>

</style>

  <body class="page-memoryDetail page-1colum">

    <!-- ヘッダー -->
    <?php
      require('header.php');
    ?>
    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
      <!-- Main -->
      <section id="main" >
        <div class="memory_infomation">
          <h2>カテゴリー</h2>
          <?php foreach ($viewMemoryData['category_data'] as $key => $value): ?>
              <span class="badge"><?php echo sanitize($value['name']); ?></span>
          <?php endforeach; ?>
          <h2>登場人物</h2>
          <?php foreach ($viewMemoryData['character_data'] as $key => $value): ?>
              <span class="badge"><?php echo sanitize($value['name']); ?></span>
          <?php endforeach; ?>
          <div class="memory_title">
            <h2>
              <i class="fa fa-heart icn-like js-click-like <?php if(isMemoryFavorit($_SESSION['user_id'], sanitize($viewMemoryData['memory_data']['id']))){ echo 'active'; } ?>" aria-hidden="true" data-memoryid="<?php echo sanitize($viewMemoryData['memory_data']['id']); ?>" >
                <span><?php echo getMemoryFavoritCount($m_id); ?></span>
              </i>
            </h2>
            <h2 style="text-align:center;"><?php echo sanitize($viewMemoryData['memory_data']['memory_title']);  ?></h2>
          </div>
        </div>
        <div class="memory-img-container">
          <div class="img-main">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic1'])); ?>" alt="" id="js-switch-img-main">
          </div>
          <div class="img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic1'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic2'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic3'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic4'])); ?>" alt="" class="js-switch-img-sub">
          </div>
        </div>
        <div class="memory-detail">
          <p><?php echo sanitize($viewMemoryData['memory_data']['memory_explanation']); ?></p>
        </div>
        <div class="button-area">
          <a href="index.php<?php echo appendGetParam(array('m_id')); ?>">&lt; 思い出一覧に戻る</a>
          <?php
            if (isLogin() && $viewMemoryData['memory_data']['user_id'] === $_SESSION['user_id']) {
           ?>
            <button type="button" name="button" class="btn-flat-border" data-memoryid="<?php echo sanitize($viewMemoryData['memory_data']['id']); ?>">削除する</button>
          <?php
            }
           ?>
        </div>
      </section>
    </div>

    <!-- footer -->
    <?php
    require('footer.php');
    ?>
