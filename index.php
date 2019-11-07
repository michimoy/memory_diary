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
    <p>当サイトは、日々の生活を写真として残していく思い出日記となります。<br>
       自分の思い出だけでなく、ほかのご利用者様の思い出もご覧になれます。
    </p>
    <section id="sidebar">
      <form name="" method="get">
        <h1 class="title">カテゴリー</h1>
        <div class="selectbox">
          <select name="c_id">
            <option value="0" ></option>

          </select>
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
