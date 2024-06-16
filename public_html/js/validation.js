function validateForm() {
  let mail = document.forms["customerInformation"]["mail"].value;
  let name = document.forms["customerInformation"]["name"].value;
  let kana = document.forms["customerInformation"]["kana"].value;
  let address = document.forms["customerInformation"]["address"].value;
  let tel = document.forms["customerInformation"]["tel"].value;
  let pay = document.forms["customerInformation"]["pay"].value;
  let credit = document.getElementsByName('credit');
  let transfer = document.getElementsByName('transfer');
  let cash = document.getElementsByName('cash');
  let number = document.forms["customerInformation"]["number"].value;
  let cvv = document.forms["customerInformation"]["cvv"].value;

  let errMail = document.getElementById('err-mail');
  let errName = document.getElementById('err-name');
  let errKana = document.getElementById('err-kana');
  let errAddress = document.getElementById('err-address');
  let errTel = document.getElementById('err-tel');
  let errPay = document.getElementById('err-pay');
  let errNumber = document.getElementById('err-number');
  let errCvv = document.getElementById('err-cvv');

  const mailRegex = new RegExp(/^\S+@\S+\.\S+$/);
  const katakanaRegex = new RegExp(/^([ァ-ン]|ー)+$/);
  const telRegex = new RegExp(/^\d{10}$|^\d{11}$/);

  if (mail == "") {
    errMail.innerText = "メールアドレスが未入力です。"
    err = false;

  } else {
    if (!mail.match(mailRegex)) {
      errMail.innerText = "メールアドレスの形式ではありません。";
      err = false;
    } else {
      errMail.innerText ="";
    }
  }

  if (name == "") {
    errName.innerText = "名前が未入力です。";
    err = false;
  } else {
    errName.innerText ="";
  }

  if (kana == "") {
    errKana.innerText = "フリガナが未入力です。"
    err = false;
  } else {
    if (!kana.match(katakanaRegex)) {
      errKana.innerText = "フリガナは全角カナで入力してください。"
      err = false;
    } else {
      errKana.innerText = "";
    }
  }

  if (address == "") {
    errAddress.innerText = "メールアドレスが未入力です。"
    err = false;
  } else {
    if (!mail.match(mailRegex)) {
      errAddress.innerText = "メールアドレスの形式ではありません。"
      err = false;
    } else {
      errAddress.innerText = "";
    }
  }

  if (tel == "") {
    errTel.innerText = "電話番号が未入力です。"
    err = false;
  } else {
    if (!tel.match(telRegex)) {
      errTel.innerText = "電話番号をハイフンなしで入力してください。"
      err = false;
    } else {
      errTel.innerText = "";
    }
  }

  if ((pay == "")) {
    errPay.innerText = "お支払方法が選択されていません。"
    err = false;
  } else {
    errPay.innerText = "";
    if (pay == "credit") {
      if (number == "") {
        errNumber.innerText = "カード番号が入力されていません。"
        err = false;
      } else {
        errNumber.innerText = "";
      }
      if (cvv == "") {
        errCvv.innerText = "セキュリティコードが入力されていません。"
        err = false;
      } else {
        errCvv.innerText = "";
      }
    } else {
      errPay.innerText = "";
    }
  }

  if (errMail.innerText == "" && errName.innerText == "" && errKana.innerText == "" && errAddress.innerText == "" && errTel.innerText == "" && errPay.innerText == "" && errNumber.innerText == "" && errCvv.innerText == "") {
    err = true;
  }

  return err;

}

function validateFormAdmin() {
  let id = document.forms["adminForm"]["id"].value;
  let password = document.forms["adminForm"]["password"].value;

  errId = document.getElementById('err-id');
  errPassword = document.getElementById('err-password');

  if (id == "") {
    errId.innerText = "ログインIDが未入力です。"
    err = false;
  } else {
    errId.innerText = "";
  }

  if (password == "") {
    errPassword.innerText = "パスワードが未入力です。";
    err = false;
  } else {
    errPassword.innerText ="";
  }

  if (errId.innerText == "" && errPassword.innerText == "") {
    err = true;
  }

  return err;

}

function validateFormGoodsAdd() {
  let goodsName = document.forms["goodsFormAdd"]["goods_name"].value;
  let goodsPrice = document.forms["goodsFormAdd"]["goods_price"].value;

  errGoods = document.getElementById('err-goods');
  errPrice = document.getElementById('err-price');

  const priceRegex = new RegExp(/^[0-9]+$/);

  if (goodsName == "") {
    errGoods.innerText = "商品名が未入力です。"
    err = false;
  } else {
    errGoods.innerText = "";
  }

  if (goodsPrice == "") {
    errPrice.innerText = "金額が未入力です。";
    err = false;
  } else {
    if (!goodsPrice.match(priceRegex)) {
      errPrice.innerText = "金額は半角数字で入力してください。"
      err = false;
    } else {
      errPrice.innerText = "";
    }
  }

  if (errGoods.innerText == "" && errPrice.innerText == "") {
    err = true;
  }

  return err;

}

// function validateFormGoodsUpdate() {
//   let goodsName = document.forms["goodsFormUpdate"]["goods_name"].value;
//   let goodsPrice = document.forms["goodsFormUpdate"]["goods_price"].value;

//   errGoods = document.getElementById('err-goods_update');
//   errPrice = document.getElementById('err-price_update');

//   const priceRegex = new RegExp(/^[0-9]+$/);

//   console.log(document.getElementsByName('goods_name').value);

//   if (goodsName == "") {
//     errGoods.innerText = "商品名が未入力です。"
//     err = false;
//   } else {
//     errGoods.innerText = "";
//   }

//   if (goodsPrice == "") {
//     errPrice.innerText = "金額が未入力です。";
//     err = false;
//   } else {
//     if (!goodsPrice.match(priceRegex)) {
//       errPrice.innerText = "金額は半角数字で入力してください。"
//       err = false;
//     } else {
//       errPrice.innerText = "";
//     }
//   }

//   if (errGoods.innerText == "" && errPrice.innerText == "") {
//     err = true;
//   }

//   return err;

// }

function validateFormOrderSearch() {
  let number = document.forms["orderSearch"]["number"].value;
  let username = document.forms["orderSearch"]["username"].value;
  let tel = document.forms["orderSearch"]["tel_number"].value;

  errSearch = document.getElementById('err-search');

  const telRegex = new RegExp(/^\d{10}$|^\d{11}$/);

  if (!(number == "")) {
    err = true;
  } else {
    if (!(username == "")) {
      err = true;
    } else {
      if (!(tel == "")) {
        if (!tel.match(telRegex)) {
          errSearch.innerText = "電話番号をハイフンなしで入力してください。"
          err = false;
        } else {
          errSearch.innerText = "";
          err = true;
        }
      } else {
        errSearch.innerText = "注文番号、お客様名、電話番号のいずれかを入力してください。"
        err = false;
      }
    }
  }

  return err;

}
