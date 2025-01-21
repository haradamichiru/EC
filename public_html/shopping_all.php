<?php
require_once(__DIR__ .'/header.php');
$goods = $goodsMod->goods();

$item = $_SESSION['cart'];
$list = array_column($goods, null, 'id');

$app = new Ec\Controller\Goods();
$app->run();

?>
    <section class="shopping_cart">
      <h1 class="page_title">カート内一覧</h1>
      <form class="cart" method="post" action="">
        <!-- 商品一覧 -->
        <section div class="items">
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
              <img src="<?= !(empty($list[$id]->image)) ? './image/'.h($list[$id]->image) : './asset/img/noimage.png'; ?>">
            </div>
            <div class="item_number">
              <h2><?= h($list[$id]->name); ?></h2>
              <div class="item_price">
                <p><span class="price">￥<?= number_format($price * (RATE + 1)); ?></span>（内税￥<?= number_format($price * RATE); ?>）/点<p>
              </div>
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
        </section>
        <!-- 合計 -->
        <section class="total">
          <div class="payment">
            <h3>お支払金額</h3>
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
                        ￥<?= POSTAGE; ?>
                        <input type="hidden" name="postage" value="<?= POSTAGE; ?>">
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
          <a class="back" href="<?= SITE_URL; ?>/index.php">買い物を続ける</a>
        </section>
        <?php } ?>
      </form>
    </section>
<?php
  require_once(__DIR__ .'/footer.php');
?>
