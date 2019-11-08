<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if (!empty($_POST)) {

  $email   = $_POST['email'];
  $pass    = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
  $age     = $_POST['age'];
  $sex     = $_POST['sex'];
  $name    = $_POST['name'];

  // var_dump($sex);
  // exit;

  //未入力チェック

  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');
  validRequired($age,'name');
  validRequired($age,'age');
  validRequired($sex,'sex');
  validRequired($name,'name');

  if(empty($err_msg)){

    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //email重複チェック
    validEmailDup($email);
    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');
    //名前の最大文字数チェック
    validMaxLen($name, 'name');


    if(empty($err_msg)){

    //パスワードとパスワード再入力が合っているかチェック
    validMatch($pass,$pass_re,'pass_re');

      if(empty($err_msg)){
        try {
          // DBへ接続
          $dbh  = dbConnect();
          // SQL文作成
          $sql  = 'INSERT INTO users (email,password,name,age,sex,login_time,create_time) VALUES(:email,:password,:name,:age,:sex,:login_time,:create_time)';
          $data = array(':email' => $email,
                         ':password' => password_hash($pass, PASSWORD_DEFAULT),
                         ':name' => $name,
                         ':age' => $age,
                         ':sex' => $sex,
                         ':login_time' => date('Y-m-d H:i:s'),
                         ':create_time' => date('Y-m-d H:i:s'));
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          // クエリ成功の場合
          if($stmt){
            //ログイン有効期限（デフォルトを１時間とする）
            $sesLimit = 60*60;
            // 最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();
            $_SESSION['msg_success'] = SUC03;

            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('マイメモリーへ遷移します。');

            header("Location:mypage.php"); //マイページへ
          }else{
            $err_msg['common'] = MSG09;
          }
        } catch (Exception $e) {
          error_log('エラー発生:' . $e->getMessage());
          $err_msg['common'] = MSG09;
        }
      }
    }
  }
}

 ?>

<?php
$siteTitle = 'ユーザ登録';
require('head.php');
 ?>

 <body class="page-signup page-1colum">
   <?php
   require('header.php');
   ?>

    <div id="contents" class="site-width">

      <section id="main">

        <div class="form-container">

          <form action="" method="post" class="form">
            <h2 class="title">ユーザ登録</h2>
            <div class="area-msg">
              <?php
                if (!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['email'])) echo 'err';  ?>">
              メールアドレス
              <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>
            <div class="area-msg">
              <?php
                if (!empty($err_msg['email'])) echo $err_msg['email'];
               ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass'])) echo 'err';  ?>">
              パスワード <span style="font-size:12px">※英数字６文字以上</span>
              <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
              パスワード(再入力) <span style="font-size:12px">※上記パスワードと同じもの</span>
              <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
              名前
              <input type="text" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['name'])) echo $err_msg['name'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
              年齢
              <input type="number" name="age" value="<?php if(!empty($_POST['age'])) echo $_POST['age']; ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['age'])) echo $err_msg['age'];
              ?>
            </div>
              性別
            <label style="display:inline;">
              <input id="men" type="radio" name="sex" value="1" <?php if(!empty($_POST['sex']) && $_POST['sex'] == "1") echo 'checked'; ?>>
              男
            </label>
            <label style="display:inline;">
              <input id="women" type="radio" name="sex" value="2" <?php if(!empty($_POST['sex']) && $_POST['sex'] == "2") echo 'checked'; ?> >
              女
            </label>

            <div class="area-msg">
              <?php
              if(!empty($err_msg['sex'])) echo $err_msg['sex'];
              ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="登録する">
            </div>
          </form>
        </div>
      </section>
    </div>

 <?php
  require('footer.php');
  ?>
