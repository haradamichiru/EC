$(function () {
  $('input[name=add_image]').change(function () {
    var file = $(this).prop('files')[0];

    // 画像以外は処理を停止
    if (!file.type.match('image.*')) {
      return;
    }

    // 画像表示
    var reader = new FileReader();
    reader.onload = function () {
      var img_src = $('.add_image').attr('src', reader.result);
      $('.imgfile').html(img_src);
    }
    reader.readAsDataURL(file);
  });

  $('.edit_button').on('change', function() {
    var file = $(this).prop('files')[0];
    var reader = new FileReader();
    var image = $(this).closest('.goods_image');

    reader.onload = function() {
      image.find('.edit_image').attr('src', reader.result);
    }
    reader.readAsDataURL(file);
  })

});

