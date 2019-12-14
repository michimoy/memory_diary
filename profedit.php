<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

//post送信された場合

if (!empty($_POST)) {
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $age = $_POST['age'];
  $email = $_POST['email'];
  $my_comment = $str = str_replace(array(" ", "　","\r", "\n"), "", $_POST['my_comment']);
  //画像をアップロードし、パスを格納
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $background_img = ( !empty($_FILES['background_img']['name']) ) ? uploadImg($_FILES['background_img'],'background_img') : '';
  $background_img = ( empty($background_img) && !empty($dbFormData['background_img']) ) ? $dbFormData['background_img'] : $background_img;
  $pic = ( !empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'],'pic') : '';
  $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['name'] !== $name){
    //名前の最大文字数チェック
    validMaxLen($name, 'name');
    //名前の未入力チェック
    validRequired($name,'name');
  }

  if($dbFormData['age'] !== $age){
    //年齢の最大文字数チェック
    validMaxLen($age, 'age',3);
    //年齢の未入力チェック
    validRequired($age,'age');
  }

  if($dbFormData['email'] !== $email){
    //メールアドレスの最大文字数チェック
    validEmail($email,'email');
    //メールアドレスの未入力チェック
    validRequired($email,'email');
  }

  if($dbFormData['my_comment'] !== $my_comment){
    //自己紹介の最大文字数チェック
    validMaxLen($my_comment,'my_comment',500);
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users  SET name = :name, age = :age, email = :email, background_img = :background_img,pic = :pic,my_comment = :my_comment WHERE id = :u_id';
      $data = array(':name' => $name , ':age' => $age, ':email' => $email,':background_img' => $background_img,':pic' => $pic,':my_comment' => $my_comment,':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページへ
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG09;
    }
  }
}
?>

<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>
<body class="page-profEdit page-2colum">
  <!-- メニュー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">
    <h1 class="page-title">プロフィール編集</h1>
    <!-- Main -->
    <section id="main" >
      <div class="form-container">
        <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
            背景画像
          <label class="area-drop <?php if(!empty($err_msg['background_img'])) echo 'err'; ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="background_img" class="input-file">
            <img src="<?php echo getFormData('background_img'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('background_img'))) echo 'display:none;' ?>">
            ドラッグ＆ドロップ
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['background_img'])) echo $err_msg['background_img'];
            ?>
          </div>
            プロフィール画像
          <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>"
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic" class="input-file">
            <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
            ドラッグ＆ドロップ
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pic'])) echo $err_msg['pic'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
            名前
            <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['name'])) echo $err_msg['name'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
            年齢
            <input type="number" name="age" value="<?php echo getFormData('age'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['age'])) echo $err_msg['age'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            メールアドレス
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['my_comment'])) echo 'err'; ?>">
            自己紹介文<span>(500文字まで)</span>
            <textarea id="js-count" name="my_comment" rows="1000" cols="80"><?php echo getFormData('my_comment'); ?></textarea>
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['my_comment'])) echo $err_msg['my_comment'];
            ?>
          </div>
          <p class="counter-text"><span id="js-count-view">0</span>/500</p>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更する">
          </div>
        </form>
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
