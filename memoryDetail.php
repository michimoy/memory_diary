<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　思い出詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 思い出IDのGETパラメータを取得
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
// DBから思い出データを取得
$viewMemoryData = getMemoryOne($m_id);
debug('取得したDBデータ：'.print_r($viewMemoryData,true));
// パラメータに不正な値が入っているかチェック
if(empty($viewMemoryData)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}


// // post送信されていた場合
// if(!empty($_POST['submit'])){
//   debug('POST送信があります。');
//
//   //ログイン認証
//   require('auth.php');
//
//   //例外処理
//   try {
//     // DBへ接続
//     $dbh = dbConnect();
//     // SQL文作成
//     $sql = 'INSERT INTO bord (sale_user, buy_user, memory_id, create_date) VALUES (:s_uid, :b_uid, :p_id, :date)';
//     $data = array(':s_uid' => $viewMemoryData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
//     // クエリ実行
//     $stmt = queryPost($dbh, $sql, $data);
//
//     // クエリ成功の場合
//     if($stmt){
//       $_SESSION['msg_success'] = SUC05;
//       debug('連絡掲示板へ遷移します。');
//       header("Location:msg.php?m_id=".$dbh->lastInsertID()); //連絡掲示板へ
//     }
//
//   } catch (Exception $e) {
//     error_log('エラー発生:' . $e->getMessage());
//     $err_msg['common'] = MSG07;
//   }
// }

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '思い出詳細';
require('head.php');
?>

  <body class="page-memoryDetail page-1colum">
    <style>
      .badge{
        padding: 5px 10px;
        color: white;
        background: #7acee6;
        margin-right: 10px;
        font-size: 16px;
        vertical-align: middle;
        position: relative;
        top: -4px;
      }
      #main .memory_infomation{
        font-size: 15px;
        padding: 10px 0;
      }
      .memory-img-container{
        overflow: hidden;
      }
      .memory-img-container img{
        width: 100%;
      }
      .memory-img-container .img-main{
        width: 750px;
        float: left;
        padding-right: 15px;
        box-sizing: border-box;
      }
      .memory-img-container .img-sub{
        width: 230px;
        float: left;
        background: #f6f5f4;
        padding: 15px;
        box-sizing: border-box;
      }
      .memory-img-container .img-sub:hover{
        cursor: pointer;
      }
      .memory-img-container .img-sub img{
        margin-bottom: 15px;
      }
      .memory-img-container .img-sub img:last-child{
        margin-bottom: 0;
      }
      .memory-detail{
        background: #f6f5f4;
        padding: 15px;
        margin-top: 15px;
        min-height: 150px;
      }
      .memory-buy{
        overflow: hidden;
        margin-top: 15px;
        margin-bottom: 50px;
        height: 50px;
        line-height: 50px;
      }
      .memory-buy .item-left{
        float: left;
      }
      .memory-buy .item-right{
        float: right;
      }
      .memory-buy .price{
        font-size: 32px;
        margin-right: 30px;
      }
      .memory-buy .btn{
        border: none;
        font-size: 18px;
        padding: 10px 30px;
      }
      .memory-buy .btn:hover{
        cursor: pointer;
      }
      /*お気に入りアイコン*/
      .icn-like{
        float:right;
        color: #ddd;
      }
      .icn-like:hover{
        cursor: pointer;
      }
      .icn-like.active{
        float:right;
        color: #fe8a8b;
      }
    </style>

    <!-- ヘッダー -->
    <?php
      require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

      <!-- Main -->
      <section id="main" >
        <div class="memory_infomation">
          <h3>カテゴリー</h3>
          <?php foreach ($viewMemoryData['category_data'] as $key => $value): ?>
              <span class="badge"><?php echo sanitize($value['name']); ?></span>
          <?php endforeach; ?>
          <h3>登場人物</h3>
          <?php foreach ($viewMemoryData['character_data'] as $key => $value): ?>
              <span class="badge"><?php echo sanitize($value['name']); ?></span>
          <?php endforeach; ?>
          <div class="memory_title">
            <h2 style="text-align:center;"><?php echo sanitize($viewMemoryData['memory_data']['memory_title']);  ?></h2>
          </div>
        </div>

        <div class="memory-img-container">
          <div class="img-main">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic1'])); ?>" alt="aa" id="js-switch-img-main">
          </div>
          <div class="img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic1'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic2'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic3'])); ?>" alt="" class="js-switch-img-sub">
            <img src="<?php echo showImg(sanitize($viewMemoryData['memory_data']['pic4'])); ?>" alt="" class="js-switch-img-sub">
          </div>
        </div>
        <div class="memory-detail">
          <p><?php echo sanitize($viewMemoryData['memory_data']['memory_explanation']); ?></p>
        </div>
        <div class="memory-buy">
          <div class="item-left">
            <a href="index.php<?php echo appendGetParam(array('p_id')); ?>">&lt; 商品一覧に戻る</a>
          </div>
          <form action="" method="post"> <!-- formタグを追加し、ボタンをinputに変更し、style追加 -->
            <div class="item-right">
              <input type="submit" value="買う!" name="submit" class="btn btn-primary" style="margin-top:0;">
            </div>
          </form>
          <div class="item-right">
            <p class="price">¥<?php echo sanitize(number_format($viewMemoryData['price'])); ?>-</p>
          </div>
        </div>
      </section>

    </div>

    <!-- footer -->
    <?php
    require('footer.php');
    ?>
