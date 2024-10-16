let form = document.getElementsByClassName("container_form");
let searchBtn = document.getElementById("search_submit");


btn.addEventListener("click", function(e) {
  let name = document.getElementsByName("name")[0].value;
  let errName = document.getElementById("err-name");
  let kana = document.getElementsByName("kana")[0].value;
  let errKana = document.getElementById("err-kana");
  const katakanaRegex = new RegExp(/^([ァ-ン]|ー)+$/);
  let tel = document.getElementsByName("tel")[0].value;
  let errTel = document.getElementById("err-tel");
  const telRegex = new RegExp(/^\d{10}$|^\d{11}$/);
  let mail = document.getElementsByName("mail")[0].value;
  let errMail = document.getElementById("err-mail");
  const mailRegex = new RegExp(/^\S+@\S+\.\S+$/);

  if (name == "") {
    errName.innerText = "名前が未入力です";
    e.preventDefault();
  }else {
    errName.innerText = "";
  }
  if (kana == "") {
    errKana.innerText = "フリガナが未入力です";
    e.preventDefault();
  }else {
    if (!kana.match(katakanaRegex)) {
      errKana.innerText = "全角カナで入力してください。";
      e.preventDefault();
    }else {
      errKana.innerText = "";
    }
  }
  if (tel == "") {
    errTel.innerText = "電話番号が未入力です";
    e.preventDefault();
  }else {
    if (!tel.match(telRegex)) {
      errTel.innerText = "電話番号をハイフンなしで入力してください。";
      e.preventDefault();
    }else {
      errTel.innerText = "";
    }
  }
  if (mail == "") {
    errMail.innerText = "メールアドレスが未入力です";
    e.preventDefault();
  }else {
    if (!mail.match(mailRegex)) {
      errMail.innerText = "メールアドレスの形式ではありません。";
      e.preventDefault();
    }else {
      errMail.innerText = "";
    }
  }
  if (errName.innerText == "" && errKana.innerText == "" && errTel.innerText == "" && errMail.innerText == "") {
    location = "thanks.html";
  }
});
