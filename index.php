<?php

require('function.php');

debug('「「「「「「「「「「「');
debug('トップページ');
debug('「「「「「「「「「「「');
debugLogStart();


//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================

$siteTitle = 'HOME';
require('head.php');


if(isset($_POST['action']) && !empty($_POST['action'])) {
  if ($_POST['action'] === 'sessionreset') {
    sessionreset();
  }
}
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? (int)strstr($_GET['p'],"＆",true) : 1; //デフォルトは１ページめ

$category = isset($_SESSION['category']) ? $_SESSION['category'] : '';
$character = isset($_SESSION['character']) ? $_SESSION['character'] : '';
$kerword = isset($_SESSION['kerword']) ? $_SESSION['kerword'] : '';
$sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : '';

// パラメータに不正な値が入っているかチェック
if(!is_int($currentPageNum)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}

$c_idlist = '';

//カテゴリーが空でなければidを設定
if(!empty($category)){
  foreach ($category as $value) {
    $c_idlist.='＆c_id%5B%5D='.$value;
  }
}

$ch_idlist = '';
//登場人物が空でなければidを設定
if (!empty($character)){
  foreach ($character as $value) {
    $ch_idlist.='＆ch_id%5B%5D='.$value;
  }
}

//ページネーション保持のためにget要素を保存
$paginlist = $c_idlist.$ch_idlist.'＆kerword='.$kerword.'＆sort='.$sort;
// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
// DBから登場人物データを取得
$dbCharacterData = getCharacter();
// 表示件数
$listSpan = 10;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);//1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20

// DBから思い出データを取得
$memoryData = getMemoriesList($currentMinNum,$category,$character,$kerword,$sort,$listSpan);

?>
<body class="page-home page-2colum">
<?php
require('header.php');
?>
<div id="contents" class="site-width">
  <section id="sidebar">
      <form method="get">
      <h1 class="title">絞り込み</h1><br>
      <div class="sidebar_category">
        <h3>カテゴリー</h3>
          <?php
          foreach ($dbCategoryData as $key => $value){
          ?>
        <label>
          <input class="save-state-category" type="checkbox" name="ca_id[]" value="<?php echo $value['id']; ?>" <?php if(in_array($value['id'],(array)sanitize($category))) echo "checked"; ?> >
          <?php
            echo sanitize($value['name']);
          }
          ?>
        </label>
      </div>
      <div class="sidebar_character">
        <h3>登場人物</h3>
          <?php
          foreach ($dbCharacterData as $key => $value){
          ?>
        <label>
          <input class="save-state-character" type="checkbox" name="ch_id[]" value="<?php echo $value['id']; ?>" <?php if(in_array($value['id'],(array)sanitize($character))) echo "checked"; ?> >
          <?php
            echo sanitize($value['name']);
          }
          ?>
        </label>
      </div>
        <h3>表示順</h3>
      <div class="selectbox">
        <select name="sort">
          <option value="0" <?php if(sanitize($sort) == 0 ){ echo 'selected'; } ?>>選択してください</option>
          <option value="1" <?php if(sanitize($sort) == 1 ){ echo 'selected'; } ?> >撮影日が古い順</option>
          <option value="2" <?php if(sanitize($sort) == 2 ){ echo 'selected'; } ?> >撮影日が新しい順</option>
        </select>
      </div>
      <div class="search">
        <h3>キーワード</h3>
        <input type="search" class="kerword" name="kerword" value="<?php echo sanitize($kerword); ?>" placeholder="卒業式" >
      </div>
      <input type="submit" value="検索">
      <input type="button" class="clear-button" value="検索条件クリア" >
    </form>
  </section>
  <section id="main">
    <p style="text-align:center;">当サイトは、日々の生活を写真として残していく思い出日記となります。<br>
       自分の思い出だけでなく、ほかのご利用者様の思い出もご覧になれます。
    </p>
    <div class="main clearfix">
      <ul class="bb-custom-grid" id="bb-custom-grid">
        <?php if(!empty($memoryData['data'])) {
                foreach ($memoryData['data'] as $key => $value) {
          ?>
        <li>
          <h3  style="text-align:center; color:blue;"><?php echo $value['memory_title']; ?></h3>
          <a href="profDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&u_id='.$value['user_id'] : '?u_id='.$value['user_id']; ?>">
             <p style="text-align:right; color:blue;"><?php echo sanitize($value['name']); ?></p>
           </a>
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

          <?php
            if (isLogin()) {
           ?>
          <i class="fa fa-heart icn-like js-click-like <?php if(isMemoryFavorit($_SESSION['user_id'], sanitize($value['id']))){ echo 'active'; } ?>" aria-hidden="true" data-memoryid="<?php echo sanitize($value['id']); ?>" >
            <span><?php echo getMemoryFavoritCount($value['id']); ?></span>
          </i>
          <?php
          }else{
           ?>
          <i class="fa fa-heart icn-like active <?php if(isMemoryFavorit($_SESSION['user_id'], sanitize($value['id']))){ echo 'active'; } ?>" aria-hidden="true" data-memoryid="<?php echo sanitize($value['id']); ?>" >
             <span><?php echo getMemoryFavoritCount($value['id']); ?></span>
          </i>
          <?php
          }
          ?>
        </li>
        <?php
                }
              }
         ?>
      </ul>
    </div>

    <?php pagination($currentPageNum, $memoryData['total_page'],$paginlist); ?>
  </section>
</div>
<!-- footer -->
<?php
require('footer.php');
?>
