<?php
require_once(__DIR__ .'/header_admin.php');
$GoodsMod = new Ec\Model\Goods();
$OrdersMod = new Ec\Model\Order();
$goods_data = $GoodsMod->goods();
$setting = $GoodsMod->settings();
$OrderCon = new Ec\Controller\Order();
$OrderCon->run();
$orders = $OrderCon->search();

if (isset($orders)) {
  define('MAX','5');
  $orders_num = count($orders);
  $max_page = ceil($orders_num / MAX);
  if (!isset($_GET['page_id'])) {
    $now = 1;
  } else {
    $now = $_GET['page_id'];
  }
  $start_no = ($now - 1) * MAX;
  $orders_data = array_slice($orders, $start_no, MAX, true);
}

for ($i = 0; $i < count($goods_data); $i++) {
  $goods_id[$i] = $goods_data[$i]->id;
  $color[$i] = unserialize($goods_data[$i]->color);
  $size[$i] = unserialize($goods_data[$i]->size);
}
for ($i = 0; $i < count($orders); $i++) {
  $item[$i] = array_values(array_filter(unserialize($orders[$i]->goods), 'myFilter'));
}
function myFilter($val) {
  return !($val === "");
}

$arrayColor = array_combine($goods_id, $color);
$arraySize = array_combine($goods_id, $size);

?>

    <!-- 検索 -->
    <section class="container">
      <h2>注文状況検索</h2>
      <form class="container_form" method="get" action="" onsubmit="return validateFormOrderSearch()" name="orderSearch">
        <div class="status_detail">
          <p class="err-txt" id="err-search"></p>
          <table>
            <tbody>
              <tr>
                <th>
                  <label>注文番号</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="number" value="<?= isset($OrderCon->getValues()->number) ? h($OrderCon->getValues()->number): ''; ?>">
                </td>
              </tr>
              <tr>
                <th>
                  <label>お客様名</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="username" value="<?= isset($OrderCon->getValues()->username) ? h($OrderCon->getValues()->username): ''; ?>">
                </td>
              </tr>
              <tr>
                <th>
                  <label>電話番号</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="tel" value="<?= isset($OrderCon->getValues()->tel) ? h($OrderCon->getValues()->tel): ''; ?>">
                </td>
              </tr>
              <tr>
                <th>
                  発送状態
                </th>
                <td class="form-status">
                  <div class="status">
                    <input class="status-radio" id="status0" type="radio" name="status" value="0" <?php if ($OrderCon->getValues()->status == 0) { ?> checked <?php } ?>>
                    <label for="status0">
                      発送準備中
                    </label>
                  </div>
                  <div class="status">
                    <input class="status-radio" id="status1" type="radio" name="status" value="1" <?php if ($OrderCon->getValues()->status == 1) { ?> checked <?php } ?>>
                    <label for="status1">
                      発送完了
                    </label>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="search">
          <div class="goods_addition">
            <input class="btn" name="search" type="submit" value="検索" id="search-btn">
          </div>
        </div>
      </form>
    </section>
    <!-- 注文情報 -->
    <section class="container">
      <h2>発送管理</h2>
      <form class="container_form" method="post" action="">
          <div class="order_list">
          <?php foreach($orders_data as $key => $order):
            $number = $order->number;
            $status = $order->status;
            $pay = $order->customers_pay;
            $postage = $order->postage;
            $rate = $order->tax / 100;
          ?>
            <div class="order" id=<?= h($key); ?>>
              <h3>注文番号：<?= h($number); ?></h3>
              <input type="hidden" name="number[<?= $key ?>]" value="<?= $number ?>">
              <div class="order_detail">
                <div class="order_customer">
                  <table>
                    <tbody>
                      <tr>
                        <th>ご注文者名</th>
                        <td>
                          <input class="form-text" type="text" name="name[<?= $key; ?>]" value="<?= isset($OrderCon->getValues()->name) && ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->name): h($order->customers_name); ?>" ?>
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('name')): ''; ?></p>
                        </td>
                      </tr>
                      <tr>
                        <th>フリガナ</th>
                        <td>
                          <input class="form-text" type="text" name="kana[<?= $key; ?>]" value="<?= isset($OrderCon->getValues()->kana) && ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->kana): h($order->customers_kana); ?>" ?>
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('kana')): ''; ?></p>
                        </td>
                      </tr>
                      <tr>
                        <th>郵便番号</th>
                        <td>
                          <input class="form-text" type="text" name="postNum[<?= $key; ?>]" value="<?= isset($OrderCon->getValues()->postNum) && ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->postNum): h($order->customers_post_number); ?>" ?>
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('postNum')): ''; ?></p>
                        </td>
                      </tr>
                      <tr>
                        <th>住所</th>
                        <td>
                          <textarea class="form-text large" name="address[<?= $key; ?>]" cols="40" rows="4"><?= isset($OrderCon->getValues()->address) && ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->address): h($order->customers_address); ?></textarea>
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('address')): ''; ?></p>
                        </td>
                      </tr>
                      <tr>
                        <th>メールアドレス</th>
                        <td>
                          <input class="form-text" type="text" name="email[<?= $key; ?>]" value="<?= isset($OrderCon->getValues()->email) && ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->email): h($order->customers_email); ?>">
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('email')): ''; ?></p>
                      </tr>
                      <tr>
                        <th>電話番号</th>
                        <td>
                          <input class="form-text" type="text" name="tel[<?= $key; ?>]" value="<?= isset($OrderCon->getValues()->telN) && ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->telN): h($order->customers_tel); ?>">
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('tel')): ''; ?></p>
                        </td>
                      </tr>
                      <tr>
                        <th>支払方法</th>
                        <td>
                          <?php
                            if (isset($OrderCon->getValues()->pay)) {
                              if ($OrderCon->getValues()->pay == "credit") {
                                $valuesPay = 'credit';
                              }
                            }
                          ?>
                          <p class="small">※銀行振込→クレジット、代引→クレジットへの変更はできません。</p>
                          <select class="select" name="pay[<?= $key; ?>]">
                            <option value="credit" <?php if ($valuesPay == "credit" || $pay == "credit") { ?> selected <?php } ?>>クレジットカード</option>
                            <option value="transfer" <?php if (!($valuesPay == 'credit') && $pay == "transfer") { ?> selected <?php } ?>>銀行振込</option>
                            <option value="cash" <?php if (!($valuesPay == 'credit') && $pay == "cash") { ?> selected <?php } ?>>代引</option>
                          </select>
                          <p class="err-txt"><?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getErrors('pay')): ''; ?></p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="order_content">
                  <p class="order_title">注文内容</p>
                  <table class="content_items">
                    <tbody>
                    <?php for ($k = 0; $k < count($item[$key]); $k++):
                      $id = $item[$key][$k]['id'];
                      $price = $item[$key][$k]['price'];
                      $array = $item[$key][$k]; ?>
                      <tr>
                        <th>
                          <?php foreach($goods_data as $goods): ?>
                            <?php if ($id == $goods->id) { ?>
                              <?= h($goods->goods_name); ?>
                              <input type="hidden" name="goods[<?= $key ?>][<?= $k ?>][id]" value="<?= $goods->id; ?>">
                            <?php } ?>
                          <?php endforeach; ?>
                        </th>
                        <td>
                          <input class="form-text number" type="text" name="goods[<?= $key ?>][<?= $k ?>][count][0]" value="<?= $count = $item[$key][$k]['count'][0]; ?>">個
                        </td>
                        <td>color:
                          <select class="select" name="goods[<?= $key ?>][<?= $k ?>][color]">
                          <?php
                            for ($i = 0; $i < count($color); $i++) {
                              if ($goods_id[$i] == $id) {
                                foreach($color[$i] as $c):
                          ?>
                            <option value="<?= $c ?>" <?php if ($c == $item[$key][$k]['color']) { ?> selected <?php } ?>>
                              <?= h($c); ?>
                            </option>
                          <?php endforeach; }} ?>
                          </select>
                        </td>
                        <td>
                          <?php if (!(empty($item[$key][$k]['size']))) { ?>
                          size:
                          <select class="select" name="goods[<?= $key ?>][<?= $k ?>][size]">
                          <?php
                            for ($i = 0; $i < count($size); $i++) {
                              if ($goods_id[$i] == $id) {
                              foreach($size[$i] as $s):
                          ?>
                            <option value="<?= $s ?>" <?php if ($s == $item[$key][$k]['size']) { ?> selected <?php } ?>>
                              <?= h($s); ?>
                            </option>
                          <?php endforeach; }} ?>
                          </select>
                          <?php } ?>
                        </td>
                        <td>
                          ￥<?= number_format($price) ?>／個
                          <input type="hidden" name="goods[<?= $key ?>][<?= $k ?>][price]" value="<?= $price; ?>">
                        </td>
                        <td>
                          <button type="button" class="delete-btn">削除</button>
                        </td>
                      </tr>
                      <?php
                        $total[$key][] = $price * $count;
                        endfor;
                        $j = $k;
                      ?>
                      <tr class="add-order">
                        <th>
                          <select class="goods-name select" name="goods[<?= $key ?>][<?= $j ?>][id]">
                            <option value="" selected>
                              商品を選択してください
                            </option>
                            <?php foreach($goods_data as $goods): ?>
                            <option value="<?= $goods->id ?>">
                              <?= h($goods->goods_name); ?>
                            </option>
                            <?php  endforeach; ?>
                          </select>
                        </th>
                        <td>
                          <input class="form-text number" type="text" name="goods[<?= $key ?>][<?= $j ?>][count][0]" value="1">個
                        </td>
                        <td>
                          color:
                          <?php
                            $colorKeys = array_keys($arrayColor);
                            for ($i = 0; $i < count($arrayColor); $i++) { ?>
                            <select class="color <?= $colorKeys[$i] ?> select" name="goods[<?= $key ?>][<?= $j ?>][color][<?= $i ?>]">
                              <option value="">
                                カラーを選択してください
                              </option>
                              <?php
                                foreach($color[$i] as $c):
                              ?>
                              <option value="<?= h($c); ?>">
                                <?= h($c); ?>
                              </option>
                              <?php endforeach; ?>
                            </select>
                          <?php } ?>
                        </td>
                        <td>
                          size:
                          <?php
                            $sizeKeys = array_keys($arraySize);
                            for ($i = 0; $i < count($size); $i++) {
                              if (!empty($size[$i])) { ?>
                            <select class="size <?= $sizeKeys[$i] ?> select" name="goods[<?= $key ?>][<?= $j ?>][size][<?= $i ?>]">
                              <option value="" selected>
                                サイズを選択してください
                              </option>
                              <?php
                                foreach($size[$i] as $s):
                              ?>
                              <option value="<?= h($s); ?>">
                                <?= h($s); ?>
                              </option>
                              <?php endforeach; ?>
                            </select>
                          <?php }} ?>
                        </td>
                        <td>
                          ￥
                          <?php
                            foreach($goods_data as $i => $goods): ?>
                              <p class="add-price <?= h($goods->id); ?>"><?= h($goods->price); ?></p>
                              <input name="goods[<?= $key ?>][<?= $j ?>][price]" type="hidden" class="add-price <?= h($goods->id); ?>" value="<?= h($goods->price); ?>">
                          <?php endforeach; ?>／個
                        </td>
                        <td>
                          <button type="button" class="delete-btn">削除</button>
                        </td>
                      </tr>
                      <tr>
                        <th colspan="5" class="add">
                        <input class="btn add-btn <?= $key ?>" type="button" value="商品を追加する">
                      </tr>
                    </tbody>
                  </table>
                  <div class="order_state">
                    <table>
                      <tbody>
                        <tr>
                          <th>購入日時：</th>
                          <td><?= h($order->created); ?><br></td>
                        </tr>
                        <tr>
                          <th>更新日時：</th>
                          <td><?= h($order->modified); ?><br></td>
                        </tr>
                        <tr>
                          <th>消費税額：</th>
                          <td>￥<?= number_format($tax = array_sum($total[$key]) * $rate); ?><br></td>
                        </tr>
                        <tr>
                          <th>送料：</th>
                          <td>￥<?= number_format($postage); ?><br></td>
                        </tr>
                        <tr>
                          <th>合計金額：</th>
                          <td class="price">￥<?= number_format(array_sum($total[$key]) + $postage + $tax); ?></td>
                        </tr>
                        <tr>
                          <th>発送状態：</th>
                          <td>
                            <select class="select" name="status[<?= $key ?>]">
                              <option value="0" <?php if ($status == "0") { ?> selected <?php } ?>>発送準備中</option>
                              <option value="1" <?php if ($status == "1") { ?> selected <?php } ?>>発送完了</option>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="cancel">
                <p class="err-txt"><?= h($OrderCon->getErrors('color')); ?></p>
                <p class="err-txt"><?= h($OrderCon->getErrors('size')); ?></p>
                <input class="btn cancel-btn" type="submit" name="cancel[<?= $key ?>]" value="注文をキャンセルする">
                <input class="btn update-btn" type="submit" value="更新" name="update[<?= $key; ?>]">
              </div>
            </div>
          <?php endforeach ?>
            <div class="next">
              <div>
                <?php
                if($now > 1){ ?>
                  <a href="./order_confirm.php?page_id=<?= $now -1 ?>">＜</a>
                <?php } else { ?>＜<?php } ?>
              </div>
              <?php for($i = 1; $i <= $max_page; $i++){
                if ($i == $now) { ?>
                  <?= $now;?>
                  <?php } else { ?>
                  <a href="./order_confirm.php?page_id=<?= $i ?>"><?= $i; ?></a>
                <?php }} ?>
              <div>
                <?php
                if($now < $max_page){ ?>
                  <a href="./order_confirm.php?page_id=<?= $now +1 ?>">＞</a>
                <?php } else { ?>＞<?php } ?>
              </div>
            </div>
          </div>
        </form>
    </section>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

