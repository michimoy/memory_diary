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


// // post送信されていた場合
// if(!empty($_POST['submit'])){
//   debug('POST送信があります。');
//
//   //ログイン認証
//   require('auth.php');
//
//   //例外処理
//   try {
//     // DBへ接続
//     $dbh = dbConnect();
//     // SQL文作成
//     $sql = 'INSERT INTO bord (sale_user, buy_user, memory_id, create_date) VALUES (:s_uid, :b_uid, :p_id, :date)';
//     $data = array(':s_uid' => $viewMemoryData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
//     // クエリ実行
//     $stmt = queryPost($dbh, $sql, $data);
//
//     // クエリ成功の場合
//     if($stmt){
//       $_SESSION['msg_success'] = SUC05;
//       debug('連絡掲示板へ遷移します。');
//       header("Location:msg.php?m_id=".$dbh->lastInsertID()); //連絡掲示板へ
//     }
//
//   } catch (Exception $e) {
//     error_log('エラー発生:' . $e->getMessage());
//     $err_msg['common'] = MSG07;
//   }
// }

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
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic1'])); ?>" alt="aa" id="js-switch-img-main">
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

          <div class="item-left">
            <a href="index.php<?php echo appendGetParam(array('m_id')); ?>">&lt; 思い出一覧に戻る</a>
          </div>

      </section>
    </div>

    <!-- footer -->
    <?php
    require('footer.php');
    ?>
