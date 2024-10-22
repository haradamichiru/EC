document.addEventListener('DOMContentLoaded', function () {
  if (document.querySelector('.add-color')) {
    let colorBtn = document.querySelectorAll('.add-color');
    let sizeBtn = document.querySelectorAll('.add-size');
    let deleteBtn = document.querySelectorAll('.delete_image-btn');

    for (var i = 0; i < colorBtn.length; i++){
      colorBtn[i].addEventListener('click',function(){
        let parent = this.closest('.color');
        let color = document.createElement('input');
        let goods = this.closest('.goods_detail');
        let name = goods.getElementsByClassName('id');
        let id = name.id.value;
        color.className = 'form-text color';
        color.name = 'color[' + id + '][]';
        color.type = "text";
        parent.insertBefore(color, this);
      });
    }

    for (var i = 0; i < sizeBtn.length; i++){
      sizeBtn[i].addEventListener('click',function(){
        let parent = this.closest('.size');
        let size = document.createElement('input');
        let goods = this.closest('.goods_detail');
        let name = goods.getElementsByClassName('id');
        let id = name.id.value;
        size.className = 'form-text size';
        size.name = 'size[' + id + '][]';
        size.type = 'text';
        parent.insertBefore(size, this);
      });
    }

    for (var i = 0; i < deleteBtn.length; i++){
      deleteBtn[i].addEventListener('click',function(){
        let parent = this.closest('.goods_image');
        let image = parent.querySelector('.edit_image');
        image.src = './asset/img/noimage.png';
        parent.querySelector('.old_image').value = "";
      });
    }
  }

});
