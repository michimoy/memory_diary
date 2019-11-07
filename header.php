<header>
  <div class="site-width">
    <h1><a href="index.php">Memory diary</a></h1>
    <nav id="top-nav">
      <ul>

        <?php
        if(empty($_SESSION['user_id'])) {
        ?>

        <li><a href="login.php">ログイン</a></li>
        <li><a href="signup.php">新規登録</a></li>

        <?php
        } else {
        ?>
        <li><a href="mypage.php">マイページ</a></li>
        <li><a href="logout.php">ログアウト</a></li>
        <?php
        }
        ?>
      </ul>
    </nav>
  </div>
</header>
