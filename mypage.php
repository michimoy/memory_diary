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

// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページめ
// パラメータに不正な値が入っているかチェック
if(!is_int($currentPageNum)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
// 表示件数
$listSpan = 20;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);//1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
// DBから思い出データを取得
$memoryData = getMyMemory($u_id,$currentMinNum);

debug('取得した思い出データ：'.print_r($memoryData,true));
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

 ?>

<?php
$siteTitle = 'Mypage';
require('head.php');
?>

<body class="page-mypage page-2colum">

<style>
    #main{
      border: none !important;
    }
</style>
<?php
require('header.php')
?>

<p id="js-show-msg" style="display:none;" class="msg-slide">
  <?php echo getSessionFlash('msg_success'); ?>
</p>

<div id="contents" class="site-width">
  <h1 class="page-title">MYPAGE</h1>
  <section id="main">
    <div class="main clearfix">
      <ul class="bb-custom-grid" id="bb-custom-grid">
        <?php if(!empty($memoryData)) {
                foreach ($memoryData as $key => $value) {
          ?>
        <li>
          <h3><?php echo $value['memory_title']; ?></h3>
          <div class="bb-bookblock">
            <div class="bb-item">
              <a href="registmemory.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>"><img src="<?php echo showImg(sanitize($value['pic1'])); ?>" alt="NonImage" style="width:300px;height:180px;"/></a>
            </div>
            <div class="bb-item">
              <a href="registmemory.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                <img src="<?php echo showImg(sanitize($value['pic2'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
              </a>
            </div>
            <div class="bb-item">
              <a href="registmemory.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                <img src="<?php echo showImg(sanitize($value['pic3'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
              </a>
            </div>
            <div class="bb-item">
              <a href="registmemory.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
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
        </li>
        <?php   }
              }
         ?>
      </ul>
    </div>
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
