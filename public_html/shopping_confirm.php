<?php
require_once(__DIR__ .'/header.php');
$goodsMod = new Ec\Model\Goods();
$OrderMod = new Ec\Model\Order();
$goods = $goodsMod->goods();
$orders = $OrderMod->orders();
$item = $_SESSION['cart'];
$list = array_column($goods, null, 'id');
$setting = $goodsMod->settings();

$postage = $setting[0]->postage;
$rate = $setting[0]->tax / 100;

$date = date('Ymd');

if ($orders == array()) {
  $num = 0;
} else {
  $num = array_key_last($orders)+2;
}

$app = new Ec\Controller\Order();
$app->run();
?>
    <div class="information">
      <h1 class="page_title">注文内容確認</h1>
      <div class="customerInformation">
        <form method="post" action="">
          <div class="container_confirm">
            <!-- 注文者情報 -->
            <section class="customer">
              <div class="customer-info">
                <h4>ご注文者情報</h4>
                <p class="customer-info_detail"><?= h($_SESSION['name']); ?>　（ <?= h($_SESSION['kana']); ?> ）</p>
              </div>
              <div class="customer-info">
                <h4>お届け先</h4>
                <p class="customer-info_detail">〒<?= h($_SESSION['post_number']); ?></p>
                <p class="customer-info_detail"><?= h($_SESSION['address']); ?></p>
              </div>
              <div class="customer-info">
                <h4>ご連絡先</h4>
                <div class="customer-info_detail">
                  <p><?= h($_SESSION['mail']); ?></p>
                  <p><?= h($_SESSION['tel']); ?></p>
                </div>
              </div>
              <div class="customer-info">
                <h4>お支払方法</h4>
                <div class="customer-info_detail">
                  <p><?php if ($_SESSION['pay'] == 'cash') {
                    ?>代引<?php } elseif ($_SESSION['pay'] == 'transfer') {
                    ?>銀行振込<?php } elseif ($_SESSION['pay'] == 'credit') {
                    ?>クレジットカード</p>
                  <p><?= h($_SESSION['credit_number']); } ?></p>
                </div>
              </div>
            </section>
            <!-- 合計 -->
            <div class="total">
              <section class="payment">
                <h2>お支払金額</h2>
                <div class="total_price">
                  <div class="total_number">
                    <table>
                      <tbody>
                        <tr>
                          <th>商品小計</th>
                          <td><?= h($_SESSION['item_count']); ?>点</td>
                        </tr>
                        <tr>
                          <th>送料</th>
                          <td>￥<?= h($postage); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                    <p class="tax_in">
                      <span class="price">￥<?= number_format($_SESSION['total_price']); ?></span>
                      <span class="tax">(内税￥<?= h($_SESSION['total_tax']); ?>)</span>
                    </p>
                  </div>
              </section>
              <div class="next_confirm">
                <input class="btn" type="submit" name="complete" value="ご注文を確定する">
                <input type="hidden" name="number" value="<?= h($date. $num); ?>">
                <input type="hidden" name="postage" value="<?= h($postage); ?>">
                <input type="hidden" name="tax_rate" value="<?= h($setting[0]->tax); ?>">
                <a class="back" href="<?= SITE_URL; ?>/shopping_information.php">お客様情報入力画面に戻る</a>
              </div>
            </div>
          </div>
          <section class="deliver">
            <h2>お届け内容</h2>
            <?php foreach($item as $all):
              $id = $all['id'];
              $price = $list[$id]->price;
              $count = $all['count'][0]; ?>
            <div class="deliver_item">
              <div class="item_img">
                <img src="<?= !(empty($list[$id]->image)) ? './image/'.h($list[$id]->image) : './asset/img/noimage.png'; ?>">
              </div>
              <div class="deliver_item_detail">
                <h3><?= h($list[$id]->goods_name); ?></h3>
                <p class="item_price">￥<?= number_format($price * ($rate + 1)); ?>/点</p>
                <p class="number">点数：<?= h($number[$id] = $count); ?></p>
                <p class="color">color：<?= h($all['color']); ?></p>
                <?php if (isset($all['size'])) { ?>
                <p class="size">size：<?= h($all['size']); ?></p>
                <?php } ?>
                <div class="subtotal">
                  <p>小計￥<?= number_format($subtotal[$id] = $price * $count * ($rate + 1)); ?></p>
                </div>
              </div>
            </div>
            <?php endforeach ?>
          </section>
        </form>
      </div>
    </div>
<?php
  require_once(__DIR__ .'/footer.php');
?>
