document.addEventListener('DOMContentLoaded', function () {
  if (document.querySelector('.delete-btn')) {
    let btn = document.querySelectorAll('.delete-btn');
    for (var i = 0; i < btn.length; i++){
      btn[i].addEventListener('click',function(){
        let tbody = this.parentNode.parentNode.parentNode;
        let tr = this.parentNode.parentNode;
        tbody.removeChild(tr);
      });
    }
  }

  if (document.querySelector('.add-btn')) {
    let btn = document.querySelectorAll('.add-btn');
    let add = document.getElementsByClassName('add-order');
    let main = document.getElementsByClassName('goods-name');
    let color = document.getElementsByClassName('color');
    let size = document.getElementsByClassName('size');
    let price = document.getElementsByClassName('add-price');

    for (var i = 0; i < add.length; i++) {
      add[i].style.display = 'none';
    }
    for (var i = 0; i < color.length; i++) {
      color[i].style.display = 'none';
    }
    for (var i = 0; i < size.length; i++) {
      size[i].style.display = 'none';
    }
    for (var i = 0; i < price.length; i++) {
      price[i].style.display = 'none';
    }
    for (var j = 0; j < btn.length; j++){
      btn[j].addEventListener('click',function(){
        let ad = this.parentNode.parentNode.previousElementSibling;
        ad.style.display = 'revert';
        this.style.display = 'none';
      });
      for (var k = 0; k < color.length; k++) {
        color[k].style.display = 'none';
      }

      for (var i = 0; i < main.length; i++) {
        main[j].onchange = function() {
          let sizeCount = 0;
          let colorCount = 0;

          this.parentNode.parentNode.querySelector('.select-color').style.display = 'revert';
          this.parentNode.parentNode.querySelector('.select-size').style.display = 'revert';
          for (var k = 0; k < size.length; k++) {
            size[k].style.display = 'none';
          }
          for (var k = 0; k < color.length; k++) {
            color[k].style.display = 'none';
          }
          for (var k = 0; k < price.length; k++) {
            price[k].style.display = 'none';
          }
          for (var k = 0; k < color.length; k++) {
            if (color[k].className == 'color' + ' ' + this.value) {
              color[k].style.display = 'revert';
              colorCount++;
            }
          }
          if (colorCount == 0) {
            this.parentNode.parentNode.querySelector('.select-color').style.display = 'none';
          }

          for (var k = 0; k < size.length; k++) {
            if (size[k].className == 'size' + ' ' + this.value) {
              size[k].style.display = 'revert';
              sizeCount++;
            }
          }
          if (sizeCount == 0) {
            this.parentNode.parentNode.querySelector('.select-size').style.display = 'none';
          }

          for (var k = 0; k < price.length; k++) {
            if (price[k].className == 'add-price' + ' ' + this.value) {
              price[k].style.display = 'revert';
            }
          }
        }
      }
    }
  }

  if (document.querySelector('.update-btn')) {
    let btn = document.querySelectorAll('.update-btn');
    var position = localStorage.getItem('position');
    var key = localStorage.getItem('number');
    let order = document.querySelectorAll('.order');
    page = JSON.parse(position);
    key = JSON.parse(key);
    let err = order[key].querySelectorAll('.err-txt');
    if (!page) {
      scrollTo(0,0);
    } else {
      for (var j = 0; j < err.length; j++) {
        if (err[j].innerHTML) {
          scrollTo(0, page);
          return false;
        } else {
          scrollTo(0,0);
        }
      }
    }
    for (var i = 0; i < btn.length; i++){
      btn[i].addEventListener('click',function(){
        var orders = this.closest('.order');
        let err = orders.id;
        var result = window.scrollY;
        position = JSON.stringify(result);
        localStorage.setItem('position',position);
        key= JSON.stringify(err);
        localStorage.setItem('number',key);
      });
    }
  }
});
