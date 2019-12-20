<?php

require('function.php');

debug('「「「「「「「「「「「');
debug('ログインページ');
debug('「「「「「「「「「「「');
debugLogStart();


//================================
// ログイン画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass  = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;


  //emailの形式チェック
  validEmail($email,'email');
  //emailの最大文字数チェック
  validMaxLen($email, 'email');

  //パスワードの英数字チェック
  validHalf($pass,'pass');
  //パスワードの最大文字数チェック
  validMaxlen($pass,'pass');
  //パスワードの最小文字数チェック
  validMinlen($pass,'pass');

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションOK');

    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //SQL分作成
      $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);
      //クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身:'.print_r($result,true));

      //パスワード照合
      // password_verify($pass,array_shift($result)

      if (!empty($result) && password_verify($pass,array_shift($result))) {
         debug('パスワードがマッチしました');

         //ログイン有効期間(デフォルトを1時間とする)
         $sesLimit = 60*60;
         //最終ログイン日時を現在日時に
         $_SESSION['login_date'] = time();//time関数は1970年1月1日 00:00:00 を0として、1秒経過するごとに1ずつ増加させた値が入る

         //ログイン保持日チェックがある場合
         if($pass_save){
           debug('ログイン保持にチェックがあります');
           //ログイン有効期間を30日にセット
           $_SESSION['login_limit'] = $sesLimit * 24 * 30;
         }else{
           debug('ログイン保持にチェックはありません');
           //次回からログイン保持しないため、1時間
           $_SESSION['login_limit'] = $sesLimit;
         }
         $_SESSION['user_id'] = $result['id'];
         $_SESSION['msg_success'] = SUC01;
         debug('セッション変数の中身：'.print_r($_SESSION,true));
         debug('マイメモリーへ遷移します。');
         header("Location:mypage.php");
      }else{
        debug('パスワードがアンマッチです');
        $err_msg['common'] = MSG07;
      }

    } catch(Exception $e){
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG09;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'ログイン';
require('head.php');
?>
<body class="page-login page-1colum">
  <!-- ヘッダー -->
  <?php
    require('header.php');
  ?>

  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <?php //ログイン認証
  require('auth.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">
    <!--main -->
    <section id="main">
      <div class="form-container">
        <form action="" method="post" class="form">
          <h2 class="title">ログイン</h2>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
             ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
          メールアドレス
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
          パスワード
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass'])) echo $err_msg['pass'];
            ?>
          </div>
          <label>
            <input type="checkbox" name="pass_save">次回ログインを省略する
          </label>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="ログイン">
          </div>
          パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a>
        </form>
      </div>
    </section>
  </div>
<?php
require("footer.php")
?>
