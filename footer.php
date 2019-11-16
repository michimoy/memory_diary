<footer id="footer">
  Copyright Task Manager. All Rights Reserved.
</footer>
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/jquery.multi-select.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
<script>
  $(function(){
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }

    // テキストエリアカウント
    var $countUp = $('#js-count'),
        $countView = $('#js-count-view');
    $countUp.on('keyup', function(e){
      $countView.html($(this).val().length);
    });

    // メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
      $jsShowMsg.slideToggle('slow');
      setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
    };

    //複数選択可能SELECT
    $('#category-multiselect').multiSelect();
    $('#character-multiselect').multiSelect();

    //datepicker
    $("#datepicker").datepicker({
      dateFormat: "yy-mm-dd",
      closeText: "閉じる",
      prevText: '<前',
      nextText: '次>',
      currentText: "今日",
      monthNames: [
        "1月",
        "2月",
        "3月",
        "4月",
        "5月",
        "6月",
        "7月",
        "8月",
        "9月",
        "10月",
        "11月",
        "12月"
      ],
      monthNamesShort: [
        "1月",
        "2月",
        "3月",
        "4月",
        "5月",
        "6月",
        "7月",
        "8月",
        "9月",
        "10月",
        "11月",
        "12月"
      ],
      dayNames: [
        "日曜日",
        "月曜日",
        "火曜日",
        "水曜日",
        "木曜日",
        "金曜日",
        "土曜日"
      ],
      changeMonth: true,
      changeYear: true,
      dayNamesShort: ["日", "月", "火", "水", "木", "金", "土"],
      dayNamesMin: ["日", "月", "火", "水", "木", "金", "土"],
      weekHeader: "週",
      isRTL: false,
      showMonthAfterYear: true,
      yearSuffix: "年",
      firstDay: 1, // 週の初めは月曜
      showButtonPanel: true // "今日"ボタン, "閉じる"ボタンを表示する
    });

    $.datepicker._gotoToday = function(id) {
      $(id).datepicker('setDate', new Date()).datepicker('hide').blur().change();
    };


    // 画像ライブプレビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover',function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border','3px #ccc dashed');
    });
    $dropArea.on('dragleave',function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e){
      $dropArea.css('border', 'none');
      var file = this.files[0],
          $img = $(this).siblings('.prev-img'),
          fileReader = new FileReader();

      fileReader.onload = function(event){
          $img.attr('src', event.target.result).show();
      };

      fileReader.readAsDataURL(file);
    });



  });

</script>
</body>
</html>
