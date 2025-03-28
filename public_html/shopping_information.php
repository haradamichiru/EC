<?php
require_once(__DIR__ .'/header.php');
$orderCon->run();
$orders = $orderMod->orders();
$postage = $goodsMod->settings()[0]->postage;

if ($orders == array()) {
  $num = 0;
} else {
  $num = array_key_last($orders)+2;
}

?>
<section class="information">
  <h1 class="page_title">お客様情報入力</h1>
    <div class="customerInformation">
      <form method="post" class="h-adr" action="" id="form" onsubmit="return validateForm()" name="customerInformation">
        <div class="form-contents">
          <label class="form">メールアドレス（必須）</label>
          <input class="form-text" type="email" name="email" value="<?= h($_SESSION['email'] ?? ''); ?>" placeholder="abc@vue.jp">
          <p class="err-txt" id="err-mail"></p>
          <p>※入力誤りにご注意ください。メールアドレスに誤りがある場合、注文完了メールが受け取れなくなります。</p>
        </div>
        <div class="form-contents">
          <label class="form">氏名（必須）</label>
          <input class="form-text" type="text" name="name" value="<?= h($_SESSION['name'] ?? ''); ?>" placeholder="佐藤太郎">
          <p class="err-txt" id="err-name"></p>
        </div>
        <div class="form-contents">
          <label class="form">氏名フリガナ（必須）</label>
          <input class="form-text" type="text" name="kana" value="<?= h($_SESSION['kana'] ?? ''); ?>" placeholder="サトウタロウ" id="kana">
          <p class="err-txt" id="err-kana"></p>
        </div>
        <div class="form-contents">
          <label class="form">郵便番号（必須）</label>
          <span class="p-country-name" style="display:none;">Japan</span>
          <p class="postnum">
            〒
            <input class="form-text p-postal-code" type="text" name="post-number1" value="<?= h($_SESSION['post-number1'] ?? ''); ?>" placeholder="160">
            -
            <input class="form-text p-postal-code" type="text" name="post-number2" value="<?= h($_SESSION['post-number2'] ?? ''); ?>" placeholder="0022">
          </p>
          <p class="err-txt" id="err-postnum"></p>
        </div>
        <div class="form-contents"><label class="form">住所（必須）</label>
          <input class="form-text p-region p-locality p-street-address p-extended-address" type="text" name="address" value="<?= h($_SESSION['address'] ?? ''); ?>" placeholder="東京都新宿区新宿１丁目１９−１０ サンモールクレスト 5F">
          <p>※建物名、部屋番号を必ずご入力ください。</p>
          <p class="err-txt" id="err-address"></p>
        </div>
        <div class="form-contents">
          <label class="form">お届け時に連絡可能な電話番号（必須）</label>
          <input class="form-text" type="tel" name="tel" value="<?= h($_SESSION['tel'] ?? ''); ?>" placeholder="0312345678" id="tel">
          <p class="err-txt" id="err-tel"></p>
        </div>
        <div class="bottom">
          <div class="pay">
            <div class="form"><p>お支払方法の選択（必須）</p>
              <p class="err-txt" id="err-pay"></p>
            </div>
            <div>
              <div class="credit">
                <label><input type="radio" name="pay" value="credit">クレジットカード</label>
              </div>
              <div id="creditInfo">
                <table>
                  <tbody>
                    <tr>
                      <th><label for="credit_number">カード番号</label></th>
                      <td>
                        <input id="credit_number" class="form-text" type="text" name="credit_number" value="">
                        <p class="err-txt" id="err-number"></p>
                      </td>
                    </tr>
                    <tr>
                      <th><label for="cvv">セキュリティコード</label></th>
                      <td>
                        <input id="cvv" class="form-text" type="text" name="cvv" value="">
                        <p class="err-txt" id="err-cvv"></p>
                      </td>
                    </tr>
                    <tr>
                      <th><label>有効期限</label></th>
                      <td>
                        <select name="period">
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4</option>
                          <option value="5">5</option>
                          <option value="6">6</option>
                          <option value="7">7</option>
                          <option value="8">8</option>
                          <option value="9">9</option>
                          <option value="10">10</option>
                          <option value="11">11</option>
                          <option value="12">12</option>
                        </select>
                        /
                        <select name="expirationDate">
                          <option value="24">24</option>
                          <option value="25">25</option>
                          <option value="26">26</option>
                          <option value="27">27</option>
                          <option value="28">28</option>
                          <option value="29">29</option>
                          <option value="30">30</option>
                          <option value="31">31</option>
                          <option value="32">32</option>
                          <option value="33">33</option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="transfer">
                <label><input type="radio" name="pay" value="transfer">銀行振込</label>
              </div>
              <div class="cash">
                <label><input type="radio" name="pay" value="cash">代引</label>
              </div>
            </div>
          </div>
          <div class="next">
            <input class="btn" type="submit" name="next" id="next" value="次に進む" onclick="customerInformation.key.value = 'next'">
            <p class="buy" id="complete"></p>
            <input class="btn complete" type="submit" name="buy" id="buy" value="購入する" onclick="customerInformation.key.value = 'buy'">
            <input name="key" type="hidden" value="">
            <input type="hidden" name="number" value="<?= h(date('Ymd'). $num); ?>">
            <input type="hidden" name="postage" value="<?= h($postage); ?>">
            <input type="hidden" name="tax_rate" value="<?= h(TAX_RATE); ?>">
            <input class="back" type="submit" name="back" value="ショッピングカートに戻る">
          </div>
        </div>
      </form>
    </div>
</section>
<?php
  require_once(__DIR__ .'/footer.php');
?>
