<?php
//================================
// ログ
//================================
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime ', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

define('MSG01','入力必須です');
define('MSG02','emailの形式で入力してください');
define('MSG03','255文字以内で入力してください');
define('MSG04','6文字以上で入力してください');
define('MSG05','英数字で入力してください。');
define('MSG06', 'メールアドレスが既に登録されています');
define('MSG07', 'メールアドレスまたはパスワードが違います');
define('MSG08', 'パスワード(再入力)がパスワードと異なります');
define('MSG09','エラーが発生しました。しばらく経ってからやり直してください。');
define('SUC01','ログインしました。');
define('SUC02','プロフィールを更新しました。');
define('SUC03','ユーザ登録が完了しました。');


//エラーメッセージ格納用の配列
$err_msg = array();

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if($str === ''){ //金額フォームなどを考えると数値の０はOKにし、空文字はダメにする
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//バリデーション関数（Email形式チェック）
function validEmail($str, $key){
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

//バリデーション関数(最大文字数)
function validMaxLen($str,$key,$max = 255){
  if (mb_strlen($str) > $max) {
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

//バリデーション関数(最大文字数)
function validMinLen($str,$key,$min = 6){
  if (mb_strlen($str) < $min) {
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}

//バリデーション関数（英数字チェック）
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}

function validMatch($str1,$str2,$key){
  if ($str1 !== $str2) {
    global $err_msg;
    $err_msg[$key] = MSG08;
  }
}

//バリデーション関数（Email重複チェック)

function validEmailDup($email){
  global $err_msg;

  try {
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email and delete_flg = 0';
    //プレースホルダー
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    //クエリ結果を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //値が0以外であれば、エラーメッセージを格納
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG06;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG09;
  }

}

//================================
// データベース
//================================
//DB接続関数
function dbconnect(){
  //DBへの接続準備
  $dsn  = 'mysql:dbname=memory_diary;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}
//SQL実行関数
function queryPost($dbh,$sql,$data){
  //クエリ作成
  $stmt = $dbh->prepare($sql);

  //プレースホルダーに値をセットし、SQL分を実行する。
  if(!$stmt->execute($data)){

    debug('クエリ発行に失敗しました');
    $err_msg['common'] = MSG09;
    return 0;
  }
  debug('クエリ発行に成功');
  return $stmt;
}

function getUser($u_id){
  debug('ユーザ情報を取得します');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //クエリ作成
    $sql  = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    // クエリ結果のデータを１レコード返却
    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }

}
// サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

// フォーム入力保持
function getFormData($str, $flg = false){
if ($flg) {
  $method = $_GET;
}else{
  $method = $_POST;
}
global $dbFormData;
// ユーザーデータがある場合
  if(!empty($dbFormData)){
  //フォームのエラーがある場合
  if(!empty($err_msg[$str])){
    //POSTにデータがある場合
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }else{
      //ない場合（基本ありえない）はDBの情報を表示
      return sanitize($dbFormData[$str]);
    }
  }else{
    //POSTにデータがあり、DBの情報と違う場合
    if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
      return sanitize($method[$str]);
    }else{
      return sanitize($dbFormData[$str]);
    }
  }
}else{
  if(isset($method[$str])){
    return sanitize($method[$str]);
  }
}
}

// 画像処理
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  if (isset($file['error']) && is_int($file['error'])) {
    try {
      // バリデーション
      // $file['error'] の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
      //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
      switch ($file['error']) {
          case UPLOAD_ERR_OK: // OK
              break;
          case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
              throw new RuntimeException('ファイルが選択されていません');
          case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
          case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
              throw new RuntimeException('ファイルサイズが大きすぎます');
          default: // その他の場合
              throw new RuntimeException('その他のエラーが発生しました');
      }

      // $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
      // exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
          throw new RuntimeException('画像形式が未対応です');
      }

      // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
      // ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
      // DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
      // image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      // 保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e) {

      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();

    }
  }
}

//sessionを１回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
