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
// 表示件数
$listSpan = 10;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);//1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
// DBから思い出データを取得
$memoryData = getMymemory($u_id,$currentMinNum,$listSpan);

debug('取得した思い出データ：'.print_r($memoryData,true));
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

//パラメータに不正な値が入っているかチェック
if(!is_numeric($currentPageNum) ){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
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
    <?php
      if(!empty($memoryData['total'])){
    ?>
      <div class="main clearfix">
        <ul class="bb-custom-grid" id="bb-custom-grid">
          <?php if(!empty($memoryData['data'])) {
                  foreach ($memoryData['data'] as $key => $value) {
            ?>
          <li>
            <h3  style="text-align:center;"><?php echo $value['memory_title']; ?></h3>
            <h3  style="text-align:right; font-size:13px;"><?php echo $value['name']; ?></h3>
            <div class="bb-bookblock">
              <div class="bb-item">
                <a href="registmemory.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$value['id'] : '?m_id='.$value['id']; ?>">
                  <img src="<?php echo showImg(sanitize($value['pic1'])); ?>" alt="NonImage" style="width:300px;height:180px;"/>
                </a>
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
            <h3  style="text-align:right; font-size:14px;"><i class="fa fa-heart" style="color:red;"><?php echo getMemoryFavoritCount($value['id']); ?></i></h3>
          </li>
          <?php
                  }
                }
           ?>
        </ul>
      </div>
      <?php pagination($currentPageNum, $memoryData['total_page']); ?>
    <?php
      }else{
    ?>
        <div class="non-memorydata">
          <p style="text-align:center;">ここに、登録した思い出が表示されます。<br>
             まずは登録してみましょう！
          </p>
          <i class="fa fa-arrow-down fa-4x" style="width:100%;text-align:center;color:#4F8AB3;"></i>
          <div class="main clearfix">
            <p style="text-align:center">投稿例</p>
            <ul class="bb-custom-grid" id="bb-custom-grid">
              <li>
                <h3  style="text-align:center;">初めての登山</h3>
                <h3  style="text-align:right; font-size:13px;">管理者</h3>
                <div class="bb-bookblock">
                  <div class="bb-item">
                      <img src="img/サンプル1.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                  <div class="bb-item">
                      <img src="img/サンプル2.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                  <div class="bb-item">
                      <img src="img/サンプル3.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                  <div class="bb-item">
                      <img src="img/サンプル4.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                </div>
                <nav>
                  <span class="bb-current"></span>
                  <span></span>
                  <span></span>
                  <span></span>
                </nav>
                <h3 style="text-align:right; font-size:14px;"><i class="fa fa-heart" style="color:red;">50</i></h3>
              </li>
              <li>
                <h3  style="text-align:center;">イルミネーション in 汐留</h3>
                <h3  style="text-align:right; font-size:13px;">管理者</h3>
                <div class="bb-bookblock">
                  <div class="bb-item">
                      <img src="img/サンプル5.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                  <div class="bb-item">
                      <img src="img/サンプル6.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                  <div class="bb-item">
                      <img src="img/サンプル7.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                  <div class="bb-item">
                      <img src="img/サンプル8.jpg" alt="NonImage" style="width:300px;height:180px;"/>
                  </div>
                </div>
                <nav>
                  <span class="bb-current"></span>
                  <span></span>
                  <span></span>
                  <span></span>
                </nav>
                <h3 style="text-align:right; font-size:14px;"><i class="fa fa-heart" style="color:red;">70</i></h3>
              </li>
            </ul>
          </div>
        </div>
    <?php
      }
    ?>
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
