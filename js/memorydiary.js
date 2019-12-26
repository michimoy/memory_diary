
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
          fileReader = new FileReader() ;

      fileReader.onload = function(event){
          $img.attr('src', event.target.result).show();
      };
      // 6. 画像読み込み
      fileReader.readAsDataURL(file);
    });

    // 画像切替
    var $switchImgSubs = $('.js-switch-img-sub'),
        $switchImgMain = $('#js-switch-img-main');
    $switchImgSubs.on('click',function(e){
      $switchImgMain.attr('src',$(this).attr('src'));
    });

    // お気に入り登録・削除
    var $favoritMemory,
        favoritMemoryId;
    $favoritMemory = $('.js-click-like') || null; //nullというのはnull値という値で、「変数の中身は空ですよ」と明示するためにつかう値

    $favoritMemory.on('click',function(){
      var $this = $(this);
      favoritMemoryId = $this.data('memoryid') || null;
      $.ajax({
        type: "POST",
        url: "ajaxMemoryFavorit.php",
        data: { memoryId : favoritMemoryId}
      }).done(function( data ){
        console.log('Ajax Success');
        // いいねの総数を表示
        $this.children('span').html(data);
        // クラス属性をtoggleでつけ外しする
        $this.toggleClass('active');
      }).fail(function( msg ) {
        console.log('Ajax Error');
      });
    });

    // 削除ボタン制御
    var $deleteButton = $('.btn-flat-border');
      $deleteButton.on('click',function(){
        var $this = $(this);
        memoryId= $this.data('memoryid') || null;
        var responce = confirm("思い出を削除しますか？");
          if (responce) {
            $.ajax({
                type: "POST",
                url: "ajaxMemoryDelete.php",
                data: { memoryId : memoryId}
            }).done(function( $result ){
              alert($result);
              location.href = 'index.php';
            }).fail(function( msg ){
              alert(msg);
            });

          }else{
            alert("削除を中断しました");
          }
      });

    //クリアボタン制御
    $(".clear-button").on("click", function () {
      clearForm(this.form);
      $.ajax({
             type: 'POST',
             url: 'index.php',
             data: {action: 'sessionreset'}
      }).done(function(){
        alert('検索結果をリセットしました');
        location.href = 'index.php';
      }).fail(function( msg ){
        alert(msg);
      });

      function clearForm (form) {
          $(form)
              .find("input, select, textarea, search")
              .not(":button, :submit, :reset, :hidden")
              .val("")
              .prop("checked", false)
              .prop("selected", false)
          ;
          $(form).find("select").prop("selectedIndex", 0);
      }
    });

    //form監視 項目が押されたらsessionを保存。
    $('form').on('change',function(e){
      var target = $( e.target );
      var fd=new FormData();

      switch (target.attr('name')) {

        case 'ca_id[]':
          fd.append("category",target.attr("value"));
          fd.append("docategory",target.prop('checked')?"add":"del");
          break;
        case 'ch_id[]':
          fd.append("character",target.attr("value"));
          fd.append("docharacter",target.prop('checked')?"add":"del");
          break;
        case 'kerword':
          fd.append("kerword",target.val());
          fd.append("dokerword",target.val()?"add":"del");
          break;
        case 'sort':
          fd.append("sort",target.val());
          fd.append("dosort",target.prop("selectedIndex")===0?"del":"add");
          break;
        default:
      }

      $.ajax({
        "url":"ajaxregist.php",
        "type":"post",
        "dataType":"json",
        "data":fd,
        "processData": false,
        "contentType": false,
        "error" : function(XMLHttpRequest, textStatus, errorThrown) {
            console.log("ajax通信に失敗しました");
        },
        "xhrFields": {"withCredentials": true},
      }).done(function( response ){
        console.log(response);
      }).fail(function(jqXHR, textStatus, errorThrown){
        console.log("jqXHR : " + jqXHR.status);
    　　 console.log("textStatus     : " + textStatus);
    　　 console.log("errorThrown    : " + errorThrown.message);
      });
    });
  });

//BookBlock
var Page = (function() {

  var $grid = $( '#bb-custom-grid' );

  return {
    init : function() {
      $grid.find( 'div.bb-bookblock' ).each( function( i ) {

        var $bookBlock = $( this ),
          $nav = $bookBlock.next().children( 'span' ),
          bb = $bookBlock.bookblock( {
            speed : 600,
            shadows : false
          } );

        // add navigation events
        $nav.each( function( i ) {
          $( this ).on( 'click touchstart', function( event ) {
            var $dot = $( this );
            $nav.removeClass( 'bb-current' );
            $dot.addClass( 'bb-current' );
            $bookBlock.bookblock( 'jump', i + 1 );
            return false;
          } );
        } );

        // add swipe events
        $bookBlock.children().on( {
          'swipeleft' : function( event ) {
            $bookBlock.bookblock( 'next' );
            return false;
          },
          'swiperight' : function( event ) {
            $bookBlock.bookblock( 'prev' );
            return false;
          }

        } );

      } );
    }
  };

})();
Page.init();
