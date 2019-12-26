<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['memoryId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('memory:'.$_POST['memoryId']);
  debug('POST送信があります。');
  $m_id = $_POST['memoryId'];
  debug('思い出ID：'.$m_id);
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // レコードがあるか検索
    $sql = 'SELECT * FROM memory_favorit WHERE memory_id = :m_id AND user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':m_id' => $m_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    $resultCount = $stmt->rowCount();

    // レコードが１件でもある場合
    if(!empty($resultCount)){
      $sql = 'DELETE FROM memory_favorit WHERE memory_id = :m_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':m_id' => $m_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // //お気に入りカウントをデクリメント
      // $sql = 'UPDATE memories SET favorit_count = favorit_count - 1 WHERE id = :m_id AND delete_flg = 0';
      // $data = array(':m_id' => $m_id);
      // $stmt = queryPost($dbh, $sql, $data);

      echo getMemoryFavoritCount($m_id);

    }else{
      $sql = 'INSERT INTO memory_favorit (memory_id,create_date,user_id) VALUES(:m_id,:date,:u_id)';
      $data = array(':m_id' => $m_id,':date' => date('Y-m-d H:i:s'),':u_id' => $_SESSION['user_id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // //お気に入りカウントをインクリメント
      // $sql = 'UPDATE memories SET favorit_count = favorit_count + 1 WHERE id = :m_id AND delete_flg = 0';
      // $data = array(':m_id' => $m_id);
      // $stmt = queryPost($dbh, $sql, $data);
      echo getMemoryFavoritCount($m_id);
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
