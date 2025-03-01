<?php
require_once(__DIR__ .'/header.php');

// DB”goods”を呼び出し変数格納
$goods = array_column($goodsMod->goods(), null, 'id');
// 送料を呼び出し変数格納
$postage = $goodsMod->settings()[0]->postage;

$goodsCon->run();
?>
    <section class="shopping_cart">
      <h1 class="page_title">カート内一覧</h1>
      <form class="cart" method="post" action="">
        <!-- 商品一覧 -->
        <section div class="items">
          <?php if (empty($_SESSION['cart'])) { // SESSION[cart]に商品がない場合 ?>
            <p>ショッピングカートの中に商品がございません。</p>
          <?php } else { ?>
          <?php foreach($_SESSION['cart'] as $cart):
            $id = $cart['id'];
            $price = $goods[$id]->price;
            $count = $cart['count'][0];
            $size = $cart['size'];
            $color = $cart['color'];
          ?>
          <div class="cart_item">
            <div class="item_img">
              <img src="<?= !(empty($goods[$id]->image)) ? './image/'.h($goods[$id]->image) : './asset/img/noimage.png'; ?>">
            </div>
            <div class="item_number">
              <h2><?= h($goods[$id]->name); ?></h2>
              <div class="item_price">
                <p><span class="price">￥<?= number_format($price * (TAX_RATE + 1)); ?></span>（内税￥<?= number_format($price * TAX_RATE); ?>）/点<p>
              </div>
              <div class="specification">
                <table>
                  <tbody>
                    <tr>
                      <th>color</th>
                      <td><?= h($cart['color']); ?></td>
                    </tr>
                    <tr>
                      <?php if(!empty($cart['size'])) { ?>
                      <th>size</th>
                      <td><?= h($cart['size']); ?></td>
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
            <p>小計<span class="price">￥<?= number_format(($subtotal[$id. $size. $color] = $price * $count) * (TAX_RATE + 1)); ?></span></p>
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
                        <?= array_sum($number); ?>点
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
                    ￥<?= number_format($total_price = array_sum($subtotal) * (TAX_RATE + 1) + $postage); ?>
                  </span>
                  <span class="tax">
                    (内税￥<?= number_format(array_sum($subtotal) * TAX_RATE); ?>)
                    <input type="hidden" name="tax_rate" value="<?= TAX_RATE; ?>">
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
