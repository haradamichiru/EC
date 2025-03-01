document.addEventListener('DOMContentLoaded', function () {
  // 発送管理画面での商品削除
  if (document.querySelector('.delete-btn')) {
    let btn = document.querySelectorAll('.delete-btn');
    for (var i = 0; i < btn.length; i++){
      btn[i].addEventListener('click',function(){ // どの削除ボタンが押下されたか判断
        let tbody = this.parentNode.parentNode.parentNode;
        let tr = this.parentNode.parentNode;
        tbody.removeChild(tr);
      });
    }
  }

  // 発送管理画面での商品追加
  if (document.querySelector('.add-btn')) {
    let btn = document.querySelectorAll('.add-btn');
    let add = document.getElementsByClassName('add-order');
    let main = document.getElementsByClassName('goods-name');
    let color = document.getElementsByClassName('color');
    let size = document.getElementsByClassName('size');
    let price = document.getElementsByClassName('add-price');

    // 追加分を隠しておく
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
    // 「商品を追加する」ボタン押下で追加分を表示させる
    for (var j = 0; j < btn.length; j++){
      btn[j].addEventListener('click',function(){
        let ad = this.parentNode.parentNode.previousElementSibling;
        ad.style.display = 'revert';
        this.style.display = 'none';
      });
      for (var k = 0; k < color.length; k++) {
        color[k].style.display = 'none';
      }

      // 商品を選択すると対応したサイズやカラー、金額に変化させる
      for (var i = 0; i < main.length; i++) {
        main[j].onchange = function() { // 商品を選択
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

    // 更新ボタン押下時にバリデーションチェック
    let updateBtn = document.querySelectorAll('.update-btn');
    for (var i = 0; i < updateBtn.length; i++) {
      updateBtn[i].addEventListener('click',function() { // どの更新ボタンが押下されたか判断
        if (this.closest('.order').querySelector('.add-order').style.display == 'revert') { // 「商品を追加する」ボタンを押下していた場合
          if (this.closest('.order').querySelector('.goods-name').value == "") {
            this.parentNode.querySelector('.err-goods').innerText = "商品を選択してください。";
            err = false;
          } else {
            this.parentNode.querySelector('.err-goods').innerText = "";
          }
          if (this.closest('.order').querySelector('.select-number').value == "") {
            this.parentNode.querySelector('.err-count').innerText = "数量を入力してください。";
            err = false;
          } else {
            this.parentNode.querySelector('.err-count').innerText = "";
          }
          if (this.closest('.order').querySelector('.select-color').value == "" && this.closest('.order').querySelector('.select-color').style.display == 'revert') {
            this.parentNode.querySelector('.err-color').innerText = "カラーを選択してください。";
            err = false;
          } else {
            this.parentNode.querySelector('.err-color').innerText = "";
          }
          if (this.closest('.order').querySelector('.select-size').value == "" && this.closest('.order').querySelector('.select-size').style.display == 'revert') {
            this.parentNode.querySelector('.err-size').innerText = "サイズを選択してください。";
            err = false;
          } else {
            this.parentNode.querySelector('.err-size').innerText = "";
          }
        } else {
          err = true;
        }

        if (this.parentNode.querySelector('.err-goods').innerText == "" && this.parentNode.querySelector('.err-count').innerText == "" && this.parentNode.querySelector('.err-color').innerText == "" && this.parentNode.querySelector('.err-size').innerText =="") {
          err = true;
        }
        return err;
      });
    }
  }
});

// 注文商品情報更新のバリデーション
function validateFormOrderGoods() {
  return err;
}

