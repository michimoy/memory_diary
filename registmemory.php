<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　思い出登録ページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
$siteTitle = '思い出を登録';
require('head.php');


//================================
// 画面処理
//================================

//ログイン認証

// 画面表示用データ取得
//================================

// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
// DBから登場人物データを取得
$dbCharacterData = getCharacter();

require('auth.php');
?>
<body class = "page-2colum">
  <?php
  require('header.php');
  ?>
  <div id="contents" class="site-width">
    <h1 class="page-title">思い出を登録する</h1>

    <section id="main">
      <div class="form-container">
        <form class="form" action="" method="post" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
          <label class="<?php if(!empty($err_msg['shooting_date'])) echo 'err';  ?>">
            記録日<span class="label-require">必須</span>
            <input type="date" name="shooting_date" value="<?php if(!empty($_POST['shooting_date'])) echo $_POST['shooting_date']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['shooting_date'])) echo $err_msg['shooting_date'];
            ?>
          </div>

          <label class="<?php if(!empty($err_msg['character_id'])) echo 'err'; ?>">
            登場人物<span class="label-require">必須</span>
            <select multiple="multiple" id="character-multiselect" name="character_id">
              <?php
                foreach($dbCharacterData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('character_id') == $val['id'] ){ echo 'selected'; } ?> >
                  <?php echo $val['name']; ?>
                </option>
              <?php
                }
              ?>
            </select>
          </label>

          <div class="area-msg">
            <?php
            if(!empty($err_msg['character_id'])) echo $err_msg['character_id'];
            ?>
          </div>

          <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
            カテゴリー<span class="label-require">必須</span>
            <select multiple="multiple" id="category-multiselect" name="category_id">
              <?php
                foreach($dbCategoryData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; } ?> >
                  <?php echo $val['name']; ?>
                </option>
              <?php
                }
              ?>
            </select>
          </label>

          <div class="area-msg">
            <?php
            if(!empty($err_msg['category_id'])) echo $err_msg['category_id'];
            ?>
          </div>

          <label class="<?php if(!empty($err_msg['area'])) echo 'err';  ?>">
            記録エリア<span class="label-require">必須</span>
            <input type="text" name="area" value="<?php echo getFormData('area'); ?>">
          </label>

          <div class="area-msg">
            <?php
            if(!empty($err_msg['area'])) echo $err_msg['area'];
            ?>
          </div>

          <label class="<?php if(!empty($err_msg['memory_explanation'])) echo 'err';  ?>">
            思い出の詳細<span class="label-require">必須</span><span>(500文字まで)</span>
            <textarea id="js-count"　name="memory_explanation"></textarea>
          </label>
          <p class="counter-text"><span id="js-count-view">0</span>/500</p>

          <div class="area-msg">
            <?php
            if(!empty($err_msg['memory_explanation'])) echo $err_msg['memory_explanation'];
            ?>
          </div>

          <div style="overflow:hidden;">
            <div class="imgDrop-container">
              画像1
              <label class="area-drop <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic1" class="input-file">
                <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;' ?>">
                  ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];
                ?>
              </div>
            </div>
            <div class="imgDrop-container">
              画像２
              <label class="area-drop <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic2" class="input-file">
                <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;' ?>">
                  ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['pic2'])) echo $err_msg['pic2'];
                ?>
              </div>
            </div>
            <div class="imgDrop-container">
              画像３
              <label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic3" class="input-file">
                <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;' ?>">
                  ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['pic3'])) echo $err_msg['pic3'];
                ?>
              </div>
            </div>
            <div class="imgDrop-container">
              画像4
              <label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic4" class="input-file">
                <img src="<?php echo getFormData('pic4'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic4'))) echo 'display:none;' ?>">
                  ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['pic4'])) echo $err_msg['pic4'];
                ?>
              </div>
            </div>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="記録する">
          </div>
        </form>
      </div>
    </section>
    <?php
    require('sidebar_mypage.php');
     ?>
  </div>

  <?php
  require('footer.php');
  ?>
