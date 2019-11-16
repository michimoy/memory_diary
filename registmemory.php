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

// 画面表示用データ取得
//================================
// GETデータを格納
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
//DBから思い出データを取得
$dbFormData = (!empty($m_id)) ? getMemory($_SESSION['user_id'], $m_id) : '';
// 新規登録画面か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
// DBから登場人物データを取得
$dbCharacterData = getCharacter();

debug('思い出ID：'.$m_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('カテゴリデータ：'.print_r($dbCategoryData,true));
debug('キャラクターデータ：'.print_r($dbCharacterData,true));

// パラメータ改ざんチェック
//================================
if(!empty($m_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ遷移します。');
  header("Location:mypage.php"); //マイページへ
}

// POST送信時処理
//================================

if (!empty($_POST)) {
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $title = $_POST['title'];
  $shooting_date = $_POST['shooting_date'];
  $area = $_POST['area'];
  $category = $_POST['category_id'];
  $character = $_POST['character_id'];
  $memory_explanation = $_POST['memory_explanation'];
  //画像をアップロードし、パスを格納
  $pic1 = ( !empty($_FILES['pic1']['name']) ) ? uploadImg($_FILES['pic1'],'pic1') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic1 = ( empty($pic1) && !empty($dbFormData['pic1']) ) ? $dbFormData['pic1'] : $pic1;
  $pic2 = ( !empty($_FILES['pic2']['name']) ) ? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic2 = ( empty($pic2) && !empty($dbFormData['pic2']) ) ? $dbFormData['pic2'] : $pic2;
  $pic3 = ( !empty($_FILES['pic3']['name']) ) ? uploadImg($_FILES['pic3'],'pic3') : '';
  $pic3 = ( empty($pic3) && !empty($dbFormData['pic3']) ) ? $dbFormData['pic3'] : $pic3;
  $pic4 = ( !empty($_FILES['pic4']['name']) ) ? uploadImg($_FILES['pic4'],'pic4') : '';
  $pic4 = ( empty($pic4) && !empty($dbFormData['pic4']) ) ? $dbFormData['pic4'] : $pic4;

  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if (empty($dbFormData)) {
    //未入力チェック
    validRequired($title,'title');
    //最大文字数チェック
    validMaxLen($title,'title');
    //未入力チェック
    validRequired($shooting_date,'shooting_date');
    validRequired($area,'area');
    validRequired($category,'category_id');
    validRequired($character,'character');
    validRequired($memory_explanation,'memory_explanation');
  }
}

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

          <label class="<?php if(!empty($err_msg['memory_title'])) echo 'err';  ?>">
            思い出の名前<span class="label-require">必須</span>
            <input type="text" name="memory_title" placeholder="最愛の家族" value="<?php if(!empty($_POST['memory_title'])) echo $_POST['memory_title'];  ?>">

          </label>

          <label class="<?php if(!empty($err_msg['shooting_date'])) echo 'err';  ?>" style = "width:120px;">
            記録日<span class="label-require">必須</span>
            <input id="datepicker" type="text" name="shooting_date" value="<?php if(!empty($_POST['shooting_date'])) echo $_POST['shooting_date']; ?>" style = "width:120px;">
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
            <input type="text" name="area" placeholder="祖父の家" value="<?php echo getFormData('area'); ?>">
          </label>

          <div class="area-msg">
            <?php
            if(!empty($err_msg['area'])) echo $err_msg['area'];
            ?>
          </div>

          <label class="<?php if(!empty($err_msg['memory_explanation'])) echo 'err';  ?>">
            思い出の詳細(500文字まで)<span class="label-require">必須</span>
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
