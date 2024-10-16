<?php
require_once(__DIR__ .'/header.php');
$goodsMod = new Ec\Model\Goods();
$goods = $goodsMod->goods();
$setting = $goodsMod->settings();

$item = $_SESSION['cart'];
$list = array_column($goods, null, 'id');

$postage = $setting[0]->postage;
$rate = $setting[0]->tax / 100;

$app = new Ec\Controller\Goods();
$app->run();

?>
    <div class="shopping_cart">
      <h1 class="page_title">カート内一覧</h1>
      <form class="cart" method="post" action="">
        <!-- 商品一覧 -->
        <div div class="items">
          <?php if (empty($item)) { ?>
            <p>ショッピングカートの中に商品がございません。</p>
          <?php } else { ?>
          <?php foreach($item as $all):
          $id = $all['id'];
          $price = $list[$id]->price;
          $count = $all['count'][0];
          $size = $all['size'];
          $color = $all['color']; ?>
          <div class="cart_item">
            <div class="item_img">
              <img src="<?= !(empty($list[$id]->image)) ? './gazou/'.h($list[$id]->image) : './asset/img/noimage.png'; ?>">
            </div>
            <div class="item_number">
              <h1><p><?= h($list[$id]->goods_name); ?></p></h1>
              <div class="item_price"><span class="price">￥<?= number_format($price); ?></span>/点</div>
              <div class="specification">
                <table>
                  <tbody>
                    <tr>
                      <th>color</th>
                      <td><?= h($all['color']); ?></td>
                    </tr>
                    <tr>
                      <?php if(!empty($all['size'])) { ?>
                      <th>size</th>
                      <td><?= h($all['size']); ?></td>
                      <?php } ?>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="count">
                <input class="count_number" name="count[<?= h($id); ?>]" type="text" value="<?= h($number[$id] = $count); ?>">
                <button class="btn_change" name="change" type="submit" value="<?= h($id); ?>">再計算</button>
              </div>
            </div>
          <div class="subtotal">
            <p>小計<span class="price">￥<?= number_format(($subtotal[$id. $size. $color] = $price * $count) * ($rate + 1)); ?></span></p>
            <button class="delete" name="delete" type="submit" value="<?= h($id); ?>">削除</button>
          </div>
          </div>
          <?php endforeach ?>
        </div>
        <!-- 合計 -->
        <div class="total">
          <div class="payment">
            <h2>お支払金額</h2>
            <div class="total_price">
              <div class="total_number">
                <table>
                  <tbody>
                    <tr>
                      <th>商品小計</th>
                      <td>
                        <?= $item_count = array_sum($number); ?>点
                        <input type="hidden" name="sum_count" value="<?= $item_count; ?>">
                      </td>
                    </tr>
                    <tr>
                      <th>送料</th>
                      <td>
                        ￥<?= $postage; ?>
                        <input type="hidden" name="postage" value="<?= $postage; ?>">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
                <p class="tax_in">
                  <span class="price">
                    ￥<?= number_format($total_price = array_sum($subtotal) * ($rate + 1) + $postage); ?>
                    <input type="hidden" name="total_price" value="<?= $total_price; ?>">
                  </span>
                  <span class="tax">
                    (内税￥<?= $total_tax = number_format(array_sum($subtotal) * $rate); ?>)
                    <input type="hidden" name="total_tax" value="<?= $total_tax; ?>">
                  </span>
                </p>
              </div>
            </div>
          <input class="btn" type="submit" name="order" value="次に進む">
        </div>
        <?php } ?>
      </form>
    </div>
<?php
  require_once(__DIR__ .'/footer.php');
?>
