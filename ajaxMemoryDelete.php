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
    $memory = getMemory($_SESSION['user_id'],$m_id);
    $dbh = dbConnect();
    $sql = 'DELETE m,mf FROM memories as m
            LEFT OUTER JOIN memory_favorit as mf
            on m.id = mf.memory_id AND m.user_id = mf.user_id
            WHERE m.id = :m_id
            AND m.user_id = :u_id';
    $data = array(':m_id' => $m_id,':u_id' => $_SESSION['user_id']);
    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      echo $memory['memory_title'].'の削除に成功しました';
    }else{
      echo '削除に失敗しました';
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }


}
