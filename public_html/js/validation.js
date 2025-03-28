// 注文者情報入力画面のバリデーション
function validateForm() {
  const key = customerInformation.key.value;
  if(key == 'next') {
    let email = document.forms["customerInformation"]["email"].value;
    let name = document.forms["customerInformation"]["name"].value;
    let kana = document.forms["customerInformation"]["kana"].value;
    let postNum1 = document.forms["customerInformation"]["post-number1"].value;
    let postNum2 = document.forms["customerInformation"]["post-number2"].value;
    let address = document.forms["customerInformation"]["address"].value;
    let tel = document.forms["customerInformation"]["tel"].value;
    let pay = document.forms["customerInformation"]["pay"].value;
    let number = document.forms["customerInformation"]["credit_number"].value;
    let cvv = document.forms["customerInformation"]["cvv"].value;
  
    let errMail = document.getElementById('err-mail');
    let errName = document.getElementById('err-name');
    let errKana = document.getElementById('err-kana');
    let errPostNum = document.getElementById('err-postnum');
    let errAddress = document.getElementById('err-address');
    let errTel = document.getElementById('err-tel');
    let errPay = document.getElementById('err-pay');
    let errNumber = document.getElementById('err-number');
    let errCvv = document.getElementById('err-cvv');
  
    let completeText = document.getElementById('complete');
    let buy = document.getElementById('buy');
  
    const mailRegex = new RegExp(/^\S+@\S+\.\S+$/);
    const katakanaRegex = new RegExp(/^([ァ-ン]|ー)+$/);
  
    // 支払方法・クレジット
    if ((pay == "")) {
      errPay.innerText = "お支払方法が選択されていません。"
      err = false;
      let y = errPay.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
      errPay.innerText = "";
      if (pay == "credit") {
        if (number == "") {
          errNumber.innerText = "カード番号が入力されていません。"
          err = false;
          let y = errNumber.getBoundingClientRect().top;
          scrollTo(0,y);
        } else {
          errNumber.innerText = "";
        }
        if (cvv == "") {
          errCvv.innerText = "セキュリティコードが入力されていません。"
          err = false;
          let y = errCvv.getBoundingClientRect().top;
          scrollTo(0,y);
        } else {
          errCvv.innerText = "";
        }
      } else {
        errPay.innerText = "";
      }
    }
  
    // 電話番号
    if (tel == "") {
      errTel.innerText = "電話番号が未入力です。"
      err = false;
      let y = errTel.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
        errTel.innerText = "";
    }
  
    // 住所
    if (address == "") {
      errAddress.innerText = "住所が未入力です。"
      err = false;
      let y = errAddress.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
        errAddress.innerText = "";
    }
  
    // 郵便番号
    if (postNum1 == "" || postNum2 == "") {
      errPostNum.innerText = "郵便番号が未入力です。"
      err = false;
      let y = errPostNum.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
        errPostNum.innerText = "";
        err = false;
    }
    
    // フリガナ
    if (kana == "") {
      errKana.innerText = "フリガナが未入力です。"
      err = false;
      let y = errKana.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
      if (!kana.match(katakanaRegex)) {
        errKana.innerText = "フリガナは全角カナで入力してください。"
        err = false;
        let y = errKana.getBoundingClientRect().top;
        scrollTo(0,y);
      } else {
        errKana.innerText = "";
      }
    }

    // 名前
    if (name == "") {
      errName.innerText = "名前が未入力です。";
      err = false;
      let y = errName.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
      errName.innerText = "";
    }

    // メールアドレス
    if (email == "") {
      errMail.innerText = "メールアドレスが未入力です。"
      err = false;
      let y = errMail.getBoundingClientRect().top;
      scrollTo(0,y);
    } else {
      if (!email.match(mailRegex)) {
        errMail.innerText = "メールアドレスの形式ではありません。";
        err = false;
        let y = errMail.getBoundingClientRect().top;
        scrollTo(0,y);
      } else {
        errMail.innerText = "";
      }
    }

    // エラーメッセージが表示されなかったら購入ボタン表示
    if (errMail.innerText == "" && errName.innerText == "" && errKana.innerText == "" && errPostNum.innerText == "" && errAddress.innerText == "" && errTel.innerText == "" && errPay.innerText == "" && errNumber.innerText == "" && errCvv.innerText == "") {
      next.style.display = "none";
      completeText.innerText = "この内容で送信します。";
      buy.style.display = "block";
      err = false;
    }
  } else if(key == 'buy') {
    err = true;
  }
  return err;
}

// 注文者情報入力画面での電話番号バリデーション
document.addEventListener('DOMContentLoaded', function () {
  // 電話番号入力時のバリデーションチェック
  if (document.getElementById('tel')) {
    const inputTel = document.getElementById('tel');
    let errTel = document.getElementById('err-tel');

    inputTel.addEventListener('blur', ()=> {
      let validateTelNeo = function (value) {
        return /^[0０]/.test(value) && libphonenumber.isValidNumber(value, 'JP');
      }

      let formatTel = function (value) {
        return new libphonenumber.AsYouType('JP').input(value);
      }

      const postdata = inputTel.value;
      if (!validateTelNeo(postdata)) {
        errTel.innerText = "電話番号が不正です。"
      } else {
        let formattedTel = formatTel(postdata);
        inputTel.value = formattedTel;
        errTel.innerText = "";
      }
    });
  }

  // フリガナ入力時、半角カナを全額カナに変換
  if (document.getElementById('kana')) {
    function kanaHalfToFull(str) {
      var kanaMap = {
          'ｶﾞ': 'ガ', 'ｷﾞ': 'ギ', 'ｸﾞ': 'グ', 'ｹﾞ': 'ゲ', 'ｺﾞ': 'ゴ',
          'ｻﾞ': 'ザ', 'ｼﾞ': 'ジ', 'ｽﾞ': 'ズ', 'ｾﾞ': 'ゼ', 'ｿﾞ': 'ゾ',
          'ﾀﾞ': 'ダ', 'ﾁﾞ': 'ヂ', 'ﾂﾞ': 'ヅ', 'ﾃﾞ': 'デ', 'ﾄﾞ': 'ド',
          'ﾊﾞ': 'バ', 'ﾋﾞ': 'ビ', 'ﾌﾞ': 'ブ', 'ﾍﾞ': 'ベ', 'ﾎﾞ': 'ボ',
          'ﾊﾟ': 'パ', 'ﾋﾟ': 'ピ', 'ﾌﾟ': 'プ', 'ﾍﾟ': 'ペ', 'ﾎﾟ': 'ポ',
          'ｳﾞ': 'ヴ', 'ﾜﾞ': 'ヷ', 'ｦﾞ': 'ヺ',
          'ｱ': 'ア', 'ｲ': 'イ', 'ｳ': 'ウ', 'ｴ': 'エ', 'ｵ': 'オ',
          'ｶ': 'カ', 'ｷ': 'キ', 'ｸ': 'ク', 'ｹ': 'ケ', 'ｺ': 'コ',
          'ｻ': 'サ', 'ｼ': 'シ', 'ｽ': 'ス', 'ｾ': 'セ', 'ｿ': 'ソ',
          'ﾀ': 'タ', 'ﾁ': 'チ', 'ﾂ': 'ツ', 'ﾃ': 'テ', 'ﾄ': 'ト',
          'ﾅ': 'ナ', 'ﾆ': 'ニ', 'ﾇ': 'ヌ', 'ﾈ': 'ネ', 'ﾉ': 'ノ',
          'ﾊ': 'ハ', 'ﾋ': 'ヒ', 'ﾌ': 'フ', 'ﾍ': 'ヘ', 'ﾎ': 'ホ',
          'ﾏ': 'マ', 'ﾐ': 'ミ', 'ﾑ': 'ム', 'ﾒ': 'メ', 'ﾓ': 'モ',
          'ﾔ': 'ヤ', 'ﾕ': 'ユ', 'ﾖ': 'ヨ',
          'ﾗ': 'ラ', 'ﾘ': 'リ', 'ﾙ': 'ル', 'ﾚ': 'レ', 'ﾛ': 'ロ',
          'ﾜ': 'ワ', 'ｦ': 'ヲ', 'ﾝ': 'ン',
          'ｧ': 'ァ', 'ｨ': 'ィ', 'ｩ': 'ゥ', 'ｪ': 'ェ', 'ｫ': 'ォ',
          'ｯ': 'ッ', 'ｬ': 'ャ', 'ｭ': 'ュ', 'ｮ': 'ョ',
          '｡': '。', '､': '、', 'ｰ': 'ー', '｢': '「', '｣': '」', '･': '・'
      };

      var reg = new RegExp('(' + Object.keys(kanaMap).join('|') + ')', 'g');
      return str.replace(reg, function (match) {
          return kanaMap[match];
      }).replace(/ﾞ/g, '゛').replace(/ﾟ/g, '゜');
    };

    const inputKana = document.getElementById('kana');
    inputKana.addEventListener('blur', ()=> {
      const postdata = inputKana.value;
      let kana =  kanaHalfToFull(postdata);
      inputKana.value = kana;
    });
  }
});

// 管理者ログイン画面でのバリデーション
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

// 商品追加のバリデーション
function validateFormGoodsAdd() {
  let goodsName = document.forms["goodsFormAdd"]["new_goods_name"].value;
  let goodsPrice = document.forms["goodsFormAdd"]["new_goods_price"].value;

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

// 送料設定のバリデーション
function validateFormSetting() {
  let postage = document.forms["settingForm"]["postage"].value;
  errPostage = document.getElementById('err-postage');

  if (postage == "") { // 送料欄が空欄だった場合
    errPostage.innerText = "送料が未入力です。"
    err = false;
  } else {
    errPostage.innerText = "";
  }

  if (errPostage.innerText == "") {
    err = true;
  }

  return err;

}

// 注文情報検索のバリデーション
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



