<?php
require_once(__DIR__ .'/header_admin.php');
$GoodsMod = new Ec\Model\Goods();
$OrderMod = new Ec\Model\Order();
$goods_data = $GoodsMod->goods();
$OrderCon = new Ec\Controller\Order();
$orders_data = $OrderMod->orders();
$order_goods = $OrderMod->ordersGoods();
$OrderCon->run();

foreach ($orders_data as $key => $orders) {
  if ($orders->number == $_SESSION['number']) {
    $order = $orders;
  }
}
foreach ($order_goods as $goods) {
  if ($order->number == $goods->order_number) {
    $orderG[] = $goods;
  }
}
?>
    <div class="container">
      <h2>選択中の情報</h2>
        <form class="container_form" method="post" action="">
          <div class="order_list">
            <div class="order">
              <h3>注文番号：<?= h($order->number); ?></h3>
              <div class="order_detail">
                <div class="customer">
                  <table>
                    <tbody>
                      <tr>
                        <th>ご注文者情報</th>
                        <td><?= h($order->name); ?></td>
                      </tr>
                      <tr>
                        <th>フリガナ</th>
                        <td><?= h($order->kana); ?></td>
                      </tr>
                      <tr>
                        <th>住所</th>
                        <td><?= h($order->address); ?></td>
                      </tr>
                      <tr>
                        <th>メールアドレス</th>
                        <td><?= h($order->email); ?></td>
                      </tr>
                      <tr>
                        <th>電話番号</th>
                        <td><?= h($order->tel); ?></td>
                      </tr>
                      <tr>
                        <th>支払方法</th>
                        <td>
                          <?php $pay = $order->pay;
                          if ($pay == "credit") { ?>クレジットカード
                          <?php } if ($pay == "transfer") { ?>銀行振込
                          <?php } if ($pay == "cash") { ?>代引 <?php } ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="content">
                  <p class="order_title">注文内容</p>
                  <table class="content_items">
                    <tbody>
                      <tr>
                        <?php
                          foreach ($orderG as $goods):
                            if (!($goods->count == 0)): ?>
                        <th>
                          <?php
                            foreach ($goods_data as $g):
                              if ($g->id == $goods->goods_id): ?>
                            <?= h($g->name); ?>
                          <?php
                              endif;
                            endforeach; ?>
                        </th>
                        <td>
                          <?= h($goods->count); ?>個
                        </td>
                        <td>
                          <?php if (!empty($goods->color)): ?>
                            <?= h($goods->color); ?>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if (isset($goods->size)): ?>
                            <?= h($goods->size); ?>
                          <?php endif; ?>
                        </td>
                        <td>
                          ￥<?= number_format($goods->price) ?>／個
                        </td>
                      </tr>
                      <?php $total[] = $goods->price * $goods->count; ?>
                      <?php endif; endforeach; ?>
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
