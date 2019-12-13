<?php

require('function.php');

debug('「「「「「「「「「「「');
debug('トップページ');
debug('「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');
// 画面表示用データ取得
//================================
$siteTitle = 'HOME';
require('head.php');

// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページめ
// カテゴリー
$category = (!empty($_GET['ca_id'])) ? $_GET['ca_id'] : '';
// 登場人物
$character = (!empty($_GET['ch_id'])) ? $_GET['ch_id'] : '';
// キーワード
$kerword = (!empty($_GET['kerword'])) ? $_GET['kerword'] : '';
// ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';

// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
// DBから登場人物データを取得
$dbCharacterData = getCharacter();
// 表示件数
$listSpan = 15;
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
  <p style = "text-align:center;">当サイトは、日々の生活を写真として残していく思い出日記となります。<br>
     自分の思い出だけでなく、ほかのご利用者様の思い出もご覧になれます。
  </p>
  <section id="sidebar">
    <form name="" method="get">
      <h1 class="title">絞り込み</h1><br>
      <div class="sidebar_category">
        <h3>カテゴリー</h3>
          <?php
          foreach ($dbCategoryData as $key => $value){
          ?>
        <label>
          <input type="checkbox" name="ca_id[]" value="<?php echo $value['id'] ?>" <?php if(!empty($category))if(in_array($value['id'],getFormData('ca_id',true))){echo 'checked';} ?>>
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
          <input type="checkbox" name="ch_id[]" value="<?php echo $value['id'] ?>"<?php if(!empty($character))if(in_array($value['id'],getFormData('ch_id',true))){echo 'checked';} ?>>
          <?php
            echo sanitize($value['name']);
          }
          ?>
        </label>
      </div>
        <h3>表示順</h3>
      <div class="selectbox">
        <select name="sort">
          <option value="0" <?php if(getFormData('sort',true) == 0 ){ echo 'selected'; } ?> >選択してください</option>
          <option value="1" <?php if(getFormData('sort',true) == 1 ){ echo 'selected'; } ?> >撮影日が古い順</option>
          <option value="2" <?php if(getFormData('sort',true) == 2 ){ echo 'selected'; } ?> >撮影日が新しい順</option>
        </select>
      </div>
      <div class="search">
        <h3>キーワード</h3>
        <input type="search" name="kerword" value="<?php echo getFormData('kerword',true); ?>" placeholder="卒業式">
      </div>
      <input type="submit" value="検索">
    </form>
  </section>
  <section id="main">
    <div class="panel-list">
      <?php
        foreach ($memoryData['data'] as $key => $val){
      ?>
         <div class="panel">
           <p class="panel-title" style="text-align:center;"><?php echo sanitize($val['memory_title']); ?></p>
           <a href="profDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&u_id='.$val['user_id'] : '?u_id='.$val['user_id']; ?>">
             <p class="panel-auther" style="text-align:right;"><?php echo sanitize($val['name']); ?></p>
           </a>
           <a href="memoryDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$val['id'] : '?m_id='.$val['id']; ?>" class="panel">
             <div class="panel-head">
               <img src="<?php echo showImg($val['pic1']); ?>">
             </div>
           </a>
           <i class="fa fa-heart icn-like js-click-like <?php if(isMemoryFavorit($_SESSION['user_id'], sanitize($val['id']))){ echo 'active'; } ?>" aria-hidden="true" data-memoryid="<?php echo sanitize($val['id']); ?>" >
             <span><?php echo getMemoryFavoritCount($val['id']); ?></span>
           </i>
         </div>
      <?php
      }
       ?>
    </div>
    <?php pagination($currentPageNum, $memoryData['total_page']); ?>
  </section>

</div>
<!-- footer -->
<?php
require('footer.php');
?>
