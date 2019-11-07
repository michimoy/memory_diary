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
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
            思い出画像1
          <div class="imgDrop-container">
            <label class="area-drop <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic1" class="input-file">
              <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
            </label>
          </div>
            思い出画像2
          <div class="imgDrop-container">
            <label class="area-drop <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic2" class="input-file">
              <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
            </label>
          </div>
            思い出画像3
          <div class="imgDrop-container">
            <label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic1" class="input-file">
              <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
            </label>
          </div>
      
        </form>
      </div>
    </section >

    <?php
    require('sidebar_mypage.php');
     ?>
  </div>


  <?php
  require('footer.php');
  ?>
