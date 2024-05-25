document.addEventListener('DOMContentLoaded', function () {
  const quantityInput = document.querySelector('.quantity');
  const inputElement = quantityInput.querySelector('.quantity__input');
  const minusButton = quantityInput.querySelector('[name="minus"]');
  const plusButton = quantityInput.querySelector('[name="plus"]');
  const remove = document.querySelector('[name="delete"]');

  minusButton.addEventListener('click', function () {
    // 減算処理
    const currentValue = parseInt(inputElement.value, 10);
    if (currentValue > 1) {
      inputElement.value = currentValue - 1;
    }
  });

  plusButton.addEventListener('click', function () {
    // 加算処理
    const currentValue = parseInt(inputElement.value, 10);
    inputElement.value = currentValue + 1;
  });

  // // 削除
  // remove.addEventListener('click', function () {
  //   let cartItem = document.querySelector('.cart_item');
  //   console.log (remove);
  //   cartItem.remove();
  // });

});