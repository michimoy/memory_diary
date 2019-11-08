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
            撮影日<span class="label-require">必須</span>
            <input type="date" name="shooting_date" value="<?php if(!empty($_POST['shooting_date'])) echo $_POST['shooting_date']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['shooting_date'])) echo $err_msg['shooting_date'];
            ?>
          </div>

          <label class="<?php if(!empty($err_msg['character_id'])) echo 'err';  ?>">
            登場人物<span class="label-require">必須</span>
            <select name="character_id">
              <option value="0" <?php if(getFormData('character_id') == 0 ){ echo 'selected'; } ?> >選択してください</option>
              <?php
                foreach($dbCharacterData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; } ?> >
                  <?php echo $val['name']; ?>
                </option>
              <?php
                }
              ?>
          </label>

          <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
            カテゴリ<span class="label-require">必須</span>
            <select name="category_id">
              <option value="0" <?php if(getFormData('category_id') == 0 ){ echo 'selected'; } ?> >選択してください</option>
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
            if(!empty($err_msg['shooting_date'])) echo $err_msg['shooting_date'];
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
