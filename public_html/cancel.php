<?php
require_once(__DIR__ .'/header_admin.php');
$app = new Ec\Controller\Order();
$app->run();
$goodsMod = new Ec\Model\Goods();
$OrdersMod = new Ec\Model\Order();
$orders = $OrdersMod->orders();
$goods = $goodsMod->goods();

for ($i = 0; $i < count($orders); $i++) {
  if ($orders[$i]->number == $_SESSION['number']) {
    $order = $orders[$i];
  }
}

$item = array_filter(unserialize($order->goods), 'myFilter');

for ($i = 0 ; $i < count($goods); $i++) {
  $color[$i] = array_filter(unserialize($goods[$i]->color), 'myFilter');
  $size[$i] = array_filter(unserialize($goods[$i]->size), 'myFilter');
}
function myFilter($val) {
  return !($val === "");
}
?>
    <div class="container">
      <h2>選択中の情報</h2>
        <form class="container_form" method="post" action="">
          <div class="order_list">
            <div class="order">
              <h3>注文番号：<?= h($number); ?></h3>
              <div class="order_detail">
                <div class="order_customer">
                  <table>
                    <tbody>
                      <tr>
                        <th>ご注文者情報</th>
                        <td><?= h($order->customers_name); ?></td>
                      </tr>
                      <tr>
                        <th>フリガナ</th>
                        <td><?= h($order->customers_kana); ?></td>
                      </tr>
                      <tr>
                        <th>住所</th>
                        <td><?= h($order->customers_address); ?></td>
                      </tr>
                      <tr>
                        <th>メールアドレス</th>
                        <td><?= h($order->customers_email); ?></td>
                      </tr>
                      <tr>
                        <th>電話番号</th>
                        <td><?= h($order->customers_tel); ?></td>
                      </tr>
                      <tr>
                        <th>支払方法</th>
                        <td>
                          <?php $pay = $order->customers_pay;
                          if ($pay == "credit") { ?>クレジットカード
                          <?php } if ($pay == "transfer") { ?>銀行振込
                          <?php } if ($pay == "cash") { ?>代引 <?php } ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="order_content">
                  <p class="order_title">注文内容</p>
                  <table class="content_items">
                    <tbody>
                      <?php for ($i = 0; $i < count($item); $i++): ?>
                      <tr>
                        <th>
                          <?php foreach($goods as $g): ?>
                            <?php if ($order->id == $g->id) { ?>
                              <?= h($g->goods_name); ?>
                            <?php } ?>
                          <?php endforeach; ?>
                        </th>
                        <td>
                          <?= $count = h($item[$i]['count'][0]); ?>個
                        </td>
                        <td>color:
                          <?= h($item[$i]['color']); ?>
                        </td>
                        <td>
                          <?php if (!empty($item[$i]['size'])) { ?>
                          size:
                            <?= h($item[$i]['size']); ?> <?php } ?>
                        </td>
                        <td>
                          ￥<?= number_format($price = $item[$i]['price']) ?>／個
                        </td>
                      </tr>
                    <?php
                      $total[] = $price * $count;
                      endfor; ?>
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
                          <th>合計金額：</th>
                          <td class="price">￥<?= number_format(array_sum($total) + $order->postage); ?></td>
                        </tr>
                        <tr>
                          <th>発送状態：</th>
                          <td>
                              <?php
                              $status = $order->status;
                              if ($status == "0") { ?>発送準備中
                              <?php } if ($status == "1") { ?>発送完了<?php } ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="cancel">
                <button type="button" class="btn" onclick="history.back()">戻る</button>
                <input class="btn" type="submit" value="注文を削除" name="delete">
              </div>
            </div>
          </div>
        </form>
    </div>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>
