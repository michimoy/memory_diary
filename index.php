<?php

require('function.php');

debug('「「「「「「「「「「「');
debug('トップページ');
debug('「「「「「「「「「「「');
debugLogStart();

$siteTitle = 'HOME';
require('head.php');
?>
<body>
<?php
require('header.php')
?>
<div id="contents" class="site-width">
    <p style = "text-align:center;">当サイトは、日々の生活を写真として残していく思い出日記となります。<br>
       自分の思い出だけでなく、ほかのご利用者様の思い出もご覧になれます。
    </p>
    <section id="sidebar">
      <form name="" method="get">
        <h1 class="title">絞り込み</h1>
        <label for="">
          <?php foreach ($variable as $key => $value) {
            // code...
          } ?>
          <input type="checkbox" name="category" value="">
        </label>
        </div>
      </form>
    </section>
  <div class="sidebar">

  </div>
</div>
<!-- footer -->
<?php
require('footer.php');
?>
