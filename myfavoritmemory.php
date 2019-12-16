<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　いいねした投稿一覧　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');
// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページめ
// 表示件数
$listSpan = 10;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);//1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
// いいねした投稿を取得するフラグ
$isfavoritflg = true;
// DBからいいねした思い出データを取得
$viewMemoryData = getMyFavorit($u_id,$currentMinNum,$listSpan);


debug('取得したいいねデータ：'.print_r($viewMemoryData,true));
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

//パラメータに不正な値が入っているかチェック
if(!is_numeric($currentPageNum) ){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
?>
<?php
$siteTitle = 'Myfavorit';
require('head.php');
?>

<body class="page-myfavorit page-2colum">
  <?php
  require('header.php')
  ?>
<div id="contents" class="site-width">
  <h1 class="page-title">MYFAVORIT</h1>
  <section id="main">
    <div class="main clearfix">
      <ul class="bb-custom-grid" id="bb-custom-grid">
        <?php if(!empty($viewMemoryData['data'])) {
                foreach ($viewMemoryData['data'] as $key => $value) {
          ?>
        <li>
          <h3  style="text-align:center;"><?php echo $value['memory_title']; ?></h3>
          <h3  style="text-align:right; font-size:13px;"><?php echo $value['name']; ?></h3>
          <div class="bb-bookblock">
            <div class="bb-item">
              <a href="memoryDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                <img src="<?php echo showImg(sanitize($value['pic1'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
              </a>
            </div>
            <div class="bb-item">
              <a href="memoryDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                <img src="<?php echo showImg(sanitize($value['pic2'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
              </a>
            </div>
            <div class="bb-item">
              <a href="memoryDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                <img src="<?php echo showImg(sanitize($value['pic3'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
              </a>
            </div>
            <div class="bb-item">
              <a href="memoryDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                <img src="<?php echo showImg(sanitize($value['pic4'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
              </a>
            </div>
          </div>
          <nav>
            <span class="bb-current"></span>
            <span></span>
            <span></span>
            <span></span>
          </nav>
          <i class="fa fa-heart icn-like js-click-like <?php if(isMemoryFavorit($_SESSION['user_id'], sanitize($value['id']))){ echo 'active'; } ?>" aria-hidden="true" data-memoryid="<?php echo sanitize($value['id']); ?>" >
               <span><?php echo getMemoryFavoritCount($value['id']); ?></span>
          </i>
        </li>
        <?php
                }
              }
         ?>
      </ul>
    </div>
    <?php pagination($currentPageNum, $viewMemoryData['total_page']); ?>
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
