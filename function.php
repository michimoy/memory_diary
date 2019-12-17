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

define('MSG01','入力必須です。');
define('MSG02','emailの形式で入力してください。');
define('MSG03','255文字以内で入力してください。');
define('MSG04','6文字以上で入力してください。');
define('MSG05','英数字で入力してください。');
define('MSG06', 'メールアドレスが既に登録されています。');
define('MSG07', 'メールアドレスまたはパスワードが違います。');
define('MSG08', 'パスワード(再入力)がパスワードと異なります。');
define('MSG09','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG10','日付のフォーマットが異なります。');
define('MSG11','30文字以内で入力してください。');
define('MSG12','いずれか一つは選択してください。');
define('MSG13','500文字以内で入力してください。');
define('MSG14', '古いパスワードが違います。');
define('MSG15', '古いパスワードと同じです。');
define('SUC01','ログインしました。');
define('SUC02','プロフィールを更新しました。');
define('SUC03','ユーザ登録が完了しました。');
define('SUC04','思い出の記録が完了しました。');
define('SUC05','思い出の修正が完了しました。');
define('SUC06','パスワードを変更しました。');


//エラーメッセージ格納用の配列
$err_msg = array();

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if($str === '' || empty($str) ){ //金額フォームなどを考えると数値の０はOKにし、空文字はダメにする
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
  if ($max == 30 && mb_strlen($str) > $max) {
    global $err_msg;
    $err_msg[$key] = MSG11;
  }

  if ($max == 500 && mb_strlen($str) > $max) {
    global $err_msg;
    $err_msg[$key] = MSG13;
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

//パスワードチェック
function validPass($str, $key){
  //半角英数字チェック
  validHalf($str, $key);
  //最大文字数チェック
  validMaxLen($str, $key);
  //最小文字数チェック
  validMinLen($str, $key);
}

//バリデーション関数(日付フォーマット)
function validDateformat($date,$key){
  global $err_msg;
  $intflag = true;

  if(empty($date) || mb_strlen($date) < 10){
    $intflag = false;
    $err_msg[$key] = MSG10;
  }else{
    list($Y, $m, $d) = explode('-', $date);
    $joinstr = ($Y.$m.$d);

    if(!preg_match("/^[0-9]+$/", $joinstr)){
      $intflag = false;
      $err_msg[$key] = MSG10;
    }
    if ($intflag && !checkdate($m, $d, $Y)) {
      $err_msg[$key] = MSG10;
    }
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

  //ローカル用
  // $dsn  = 'mysql:dbname=memory_diary;host=localhost;charset=utf8';
  // $user = 'root';
  // $password = 'root';
  //本番用
  $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
  $db['dbname'] = ltrim($db['path'], '/');
  $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
  $user = $db['user'];
  $password = $db['pass'];
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
    return false;
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

function getOtherUser($u_id){
  debug('他の人のユーザ情報を取得します');
  //例外処理
  try{
    //DBへ接続
    $dbh = dbConnect();
    //クエリ作成
    $sql  = 'SELECT id, name, age, sex, background_img,pic, my_comment FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    // クエリ結果のデータを１レコード返却
    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
// サニタイズ
function sanitize($str){
  if (is_array($str)) {
    return array_map('htmlspecialchars',$str);
  }else{
    return htmlspecialchars($str,ENT_QUOTES);
  }
}

// フォーム入力保持
function getFormData($str,$flg = false){

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

//================================
// ログイン認証
//================================
function isLogin(){
  // ログインしている場合
  if( !empty($_SESSION['login_date']) ){
    debug('ログイン済みユーザーです。');

    // 現在日時が最終ログイン日時＋有効期限を超えていた場合
    if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです。');

      // セッションを削除（ログアウトする）
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限以内です。');
      return true;
    }

  }else{
    debug('未ログインユーザーです。');
    return false;
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


      require 'vendor/autoload.php';


      $type = @exif_imagetype($file['tmp_name']);

      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
          throw new RuntimeException('画像形式が未対応です');
      }

      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

      $s3client = new Aws\S3\S3Client([
        'credentials' => [
            'key' => 'AKIA5GUWEMTDQNDKYUH3',
            'secret' => '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',
        ],
        'region' => 'ap-northeast-1',
        'version' => 'latest',
      ]);

      if (!is_uploaded_file($file['tmp_name'])) {
        return;
      }

      // $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      // S3バケットに画像をアップロード
      $result = $s3client->putObject(array(
          'Bucket' => 'memorydiary',
          'Key' => $path,
          'Body' => fopen($file['tmp_name'], 'rb'),
          // 'ACL' => 'public-read', // 画像は一般公開されます
          'ContentType' => mime_content_type($file['tmp_name']),
      ));

      // $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
      // exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      // $type = @exif_imagetype($file['tmp_name']);
      // if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
      //     throw new RuntimeException('画像形式が未対応です');
      // }

      // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
      // ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
      // DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
      // image_type_to_extension関数はファイルの拡張子を取得するもの
      // // // $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      // // if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
      // //     throw new RuntimeException('ファイル保存時にエラーが発生しました');
      // // }
      // // 保存したファイルパスのパーミッション（権限）を変更する
      // chmod($path, 0644);

      // debug('ファイルは正常にアップロードされました');
      // debug('ファイルパス：'.$path);

      return $result['ObjectURL'];

    } catch (RuntimeException $e) {

      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();

    }
  }
}


function getCategory(){

  debug('カテゴリー情報を取得します。');
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM categories';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
  }

  function getCharacter(){

    debug('カテゴリー情報を取得します。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT * FROM characters';
      $data = array();
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        // クエリ結果の全データを返却
        return $stmt->fetchAll();
      }else{
        return false;
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
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

function getMemory($u_id,$m_id){
  debug('思い出情報を取得します。');
  debug('ユーザーID：'.$u_id);
  debug('思い出ID：'.$m_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM memories WHERE user_id = :u_id and id = :m_id and delete_flg = 0';
    $data = array(':u_id' => $u_id,':m_id' => $m_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      // クエリ結果のデータを１レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

function getMyMemory($u_id,$currentMinNum,$span = 10){
  debug('思い出情報を取得します。');
  debug('ユーザーID：'.$u_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    // 件数用のSQL文作成
    $sql = 'SELECT id FROM memories WHERE user_id = :u_id';
    $data = array(':u_id'=>$u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if (!$stmt) {
      return false;
    }

    $rst['total'] = $stmt->rowCount(); //総レコード数
    $rst['total_page'] = ceil($rst['total']/$span); //総ページ数

    // ページング用のSQL文作成
    $sql = 'SELECT
            m.id,
            m.category_id,
            m.character_id,
            m.pic1,
            m.pic2,
            m.pic3,
            m.pic4,
            m.shooting_date,
            m.memory_explanation,
            m.area,
            m.memory_title,
            u.name
            FROM memories as m
            INNER JOIN users as u
            on m.user_id = u.id
            WHERE m.user_id = :u_id and m.delete_flg = 0';
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array(':u_id' => $u_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      // クエリ結果のデータを全レコードを格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

function getMyFavorit($u_id,$currentMinNum,$span = 10){

  try{
    //DBへ接続
    $dbh = dbConnect();

    // 件数用のSQL文作成
    $sql = 'SELECT
            m.id
            FROM memories as m
            LEFT JOIN memory_favorit as mf
            on mf.memory_id = m.id
            INNER JOIN users as u
            on m.user_id = u.id
            WHERE mf.user_id = :u_id';

    $data = array(':u_id'=>$u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if (!$stmt) {
      return false;
    }

    $rst['total'] = $stmt->rowCount(); //総レコード数
    $rst['total_page'] = ceil($rst['total']/$span); //総ページ数

    $sql = 'SELECT
            m.id,
            m.pic1,
            m.pic2,
            m.pic3,
            m.pic4,
            m.user_id,
            m.memory_title,
            u.name
            FROM memories as m
            LEFT JOIN memory_favorit as mf
            on mf.memory_id = m.id
            INNER JOIN users as u
            on m.user_id = u.id
            WHERE mf.user_id = :u_id';

    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全データを返却
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }

  } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
}

function getMemoriesList($currentMinNum,$category,$character,$kerword,$sort,$span = 10){
  debug('思い出情報リストを取得します。');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    // 件数用のSQL文作成
    $sql = 'SELECT
            m.id,
            m.category_id,
            m.character_id,
            m.pic1,
            m.pic2,
            m.pic3,
            m.pic4,
            m.shooting_date,
            m.memory_explanation,
            m.area,
            m.user_id,
            m.memory_title,
            u.name
            FROM memories as m
            INNER JOIN users as u
            on m.user_id = u.id';
    $where = [];
    $wherecharacter = [];
    $wherecategory  = [];
    if(!empty($character)){
      foreach ((array) $character as $value) {
        $wherecharacter[] =" FIND_IN_SET($value,character_id)";
      }
      $where[] = implode(' OR ',$wherecharacter);
    }
    if(!empty($category)){
      foreach ((array) $category as $value) {
        $wherecategory[] =" FIND_IN_SET($value,category_id)";
      }
      $where[] = implode(' OR ',$wherecategory);
    }
    if (!empty($kerword)) {
      $where[] = "memory_title LIKE '%$kerword%' OR memory_explanation LIKE '%$kerword%'";
    }
    if(!empty($where)){
      $wheresql = implode(' AND ',$where);
      $sql.= " WHERE m.user_id = u.id and m.delete_flg = 0 AND ".$wheresql;
    }else{
      $sql.= " WHERE m.user_id = u.id and m.delete_flg = 0 ";
    }

    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= " ORDER BY shooting_date ASC";
          break;
        case 2:
          $sql .= " ORDER BY shooting_date DESC";
          break;
      }
    }

    $data = array();

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      //総レコード数
      $rst['total'] = $stmt->rowCount();
      //総ページ数
      $rst['total_page'] = ceil($rst['total']/$span);
    }else{
      return false;
    }

    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      // クエリ結果のデータを全レコードを格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
function getMemoryFavoritCount($m_id){
  debug('お気に入り件数を取得します。');
  debug('思い出ID：'.$m_id);
  //例外処理
  try {
    // DBへ接続
    $dbh  = dbConnect();
    // SQL文作成
    $sql  = 'SELECT * FROM memory_favorit WHERE memory_id = :m_id AND delete_flg = 0';
    $data = array(':m_id' => $m_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->rowCount();
    }else{
      return 0;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
function getOtherMemoryCount($u_id){
  debug('他の人の思い出情報を取得します。');
  debug('ユーザID：'.$u_id);
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) AS memory_count
            FROM memories
            WHERE user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return  $stmt->fetch();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

function getMemoryOne($m_id){
  debug('思い出情報を取得します。');
  debug('思い出ID：'.$m_id);
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT
            m.id,
            m.pic1,
            m.pic2,
            m.pic3,
            m.pic4,
            m.shooting_date,
            m.memory_explanation,
            m.area,
            m.user_id,
            m.memory_title,
            u.name
            FROM memories AS m
            INNER JOIN users AS u ON m.user_id = u.id
            WHERE m.id = :m_id AND
            m.delete_flg = 0';

    $data = array(':m_id' => $m_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      $rst['memory_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
      $rst['memory_count']  = $stmt->fetchcolumn();
    }else{
      return false;
    }
    //カテゴリー情報取得
    $sql = 'SELECT
            c.name
            FROM memories AS m
            INNER JOIN users AS u ON m.user_id = u.id
            INNER JOIN categories AS c ON FIND_IN_SET(c.id,m.category_id)
            WHERE m.id = :m_id AND
            m.delete_flg = 0';

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      $rst['category_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

    //キャラクター情報取得
    $sql = 'SELECT
            ch.name
            FROM memories AS m
            INNER JOIN users AS u ON m.user_id = u.id
            INNER JOIN characters AS ch ON FIND_IN_SET(ch.id,m.character_id)
            WHERE m.id = :m_id AND
            m.delete_flg = 0';

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      $rst['character_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

    return $rst;

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

function myfavoritmemory($u_id){
  debug('お気に入りの投稿があるか確認します。');
  debug('ユーザーID：'.$u_id);
  //例外処理
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id FROM memories as m
            INNER JOIN memory_favorit as mf
            on m.id = mf.memory_id
            WHERE m.user_id = :u_id AND m.delete_flg = 0';

    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
function isMemoryFavorit($u_id, $m_id){
  debug('お気に入り情報があるか確認します。');
  debug('ユーザーID：'.$u_id);
  debug('思い出ID：'.$m_id);
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM memory_favorit WHERE memory_id = :m_id AND user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':m_id' => $m_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt->rowCount()){
      debug('お気に入りです');
      return true;
    }else{
      debug('特に気に入ってません');
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//GETパラメータ付与
// $del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
  if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
      if (is_array($val)) {
        foreach ($val as $arraykey => $arrayvalue) {
          $str .= $key.'[]='.$arrayvalue.'&';
        }
      }else{
        if(!in_array($key,$arr_del_key,true)) { //取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
          $str .= $key.'='.$val.'&';
        }
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}


//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
  // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
  if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
  }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
  }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  // 現ページが1の場合は左に何も出さない。右に５個出す。
  }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  // それ以外は左に２個出す。
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}


//メール送信
  function sendMail($from, $to, $subject, $comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        //文字化けしないように設定
        mb_language("Japanese"); //現在使っている言語を設定する
        mb_internal_encoding("UTF-8"); //内部の日本語をどうエンコーディング（機械が分かる言葉へ変換）するかを設定

        //メールを送信（送信結果はtrueかfalseで返ってくる）
        $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
        //送信結果を判定
        if ($result) {
          debug('メールを送信しました。');
        } else {
          debug('【エラー発生】メールの送信に失敗しました。');
        }
    }
}

//画像表示用関数
function showImg($path){
  if(empty($path)){
    return 'img/sample-img.png';
  }else{
    return $path;
  }
}

//エラーメッセージ関数
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}
