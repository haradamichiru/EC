<?php
require_once(__DIR__ .'/header_admin.php');
$goods_data = $GoodsMod->goods();
list($orders, $now, $max_page) = $OrderCon->search();
$order_goods = $OrderMod->ordersGoods();
$sizes = $goodsMod->sizes();
$goodsSizes = $goodsMod->goods_sizes();
$colors = $goodsMod->colors();
$goodsColors = $goodsMod->goods_colors();

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
                  <input class="form-text" type="text" name="number" value="<?= h($OrderCon->getValues()->seach_number ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <th>
                  <label>お客様名</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="username" value="<?= h($OrderCon->getValues()->seach_username ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <th>
                  <label>電話番号</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="tel" value="<?= h($OrderCon->getValues()->seach_tel ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <th>
                  発送状態
                </th>
                <td class="form-status">
                  <div class="status">
                    <input class="status-radio" id="status0" type="radio" name="status" value="0" <?php if ($OrderCon->getValues()->seach_status == 0) { ?> checked <?php } ?>>
                    <label for="status0">
                      発送準備中
                    </label>
                  </div>
                  <div class="status">
                    <input class="status-radio" id="status1" type="radio" name="status" value="1" <?php if ($OrderCon->getValues()->seach_status == 1) { ?> checked <?php } ?>>
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
        <?php if (empty($orders)) { ?>
        <p class="result">検索結果は0件です。</p>
        <?php } else { ?>
        <!-- オーダー繰り返しここから -->
        <?php foreach($orders as $key => $order):
          $number = $order->number;
          $pay = $order->pay;
        ?>
        <form class="container_form order_list" method="post" action="" id="order[<?= h($number); ?>]" name="orders_<?= h($number); ?>">
          <div class="order">
            <h3>注文番号：<?= h($number); ?></h3>
            <input type="hidden" name="number" value="<?= h($number); ?>">
            <div class="order_detail">
              <div class="order_customer">
                <table>
                  <tbody>
                    <tr>
                      <th>ご注文者名</th>
                      <td>
                        <input class="form-text" type="text" name="name" value="<?= isset($OrderCon->getValues()->name) && ($number == $OrderCon->getValues()->number) ? h($OrderCon->getValues()->name): h($order->name); ?>" ?>
                        <p class="err-txt" name="err"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('name')): ''; ?></p>
                      </td>
                    </tr>
                    <tr>
                      <th>フリガナ</th>
                      <td>
                        <input class="form-text" type="text" name="kana" value="<?= isset($OrderCon->getValues()->kana) && ($number == $OrderCon->getValues()->number) ? h($OrderCon->getValues()->kana): h($order->kana); ?>" ?>
                        <p class="err-txt"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('kana')): ''; ?></p>
                      </td>
                    </tr>
                    <tr>
                      <th>郵便番号</th>
                      <td>
                        <input class="form-text" type="text" name="postNumber" value="<?= isset($OrderCon->getValues()->postNumber) && ($number == $OrderCon->getValues()->number) ? h($OrderCon->getValues()->postNumber): h($order->post_number); ?>" ?>
                        <p class="err-txt"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('postNumber')): ''; ?></p>
                      </td>
                    </tr>
                    <tr>
                      <th>住所</th>
                      <td>
                        <textarea class="form-text large" name="address" cols="40" rows="4"><?= isset($OrderCon->getValues()->address) && ($number == $OrderCon->getValues()->number) ? h($OrderCon->getValues()->address): h($order->address); ?></textarea>
                        <p class="err-txt"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('address')): ''; ?></p>
                      </td>
                    </tr>
                    <tr>
                      <th>メールアドレス</th>
                      <td>
                        <input class="form-text" type="text" name="email" value="<?= isset($OrderCon->getValues()->email) && ($number == $OrderCon->getValues()->number) ? h($OrderCon->getValues()->email): h($order->email); ?>">
                        <p class="err-txt"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('email')): ''; ?></p>
                    </tr>
                    <tr>
                      <th>電話番号</th>
                      <td>
                        <input class="form-text" type="text" name="tel" value="<?= isset($OrderCon->getValues()->tel) && ($number == $OrderCon->getValues()->number) ? h($OrderCon->getValues()->tel): h($order->tel); ?>">
                        <p class="err-txt"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('tel')): ''; ?></p>
                      </td>
                    </tr>
                    <tr>
                      <th>支払方法</th>
                      <td>
                        <p class="small">※銀行振込→クレジット、代引→クレジットへの変更はできません。</p>
                        <select class="select" name="pay">
                          <option value="credit"
                            <?php if (isset($OrderCon->getValues()->pay)) { if($OrderCon->getValues()->pay == 'credit' && $number == $OrderCon->getValues()->number) { ?> selected
                            <?php }} elseif ($pay == 'credit') { ?> selected
                            <?php } ?>>クレジットカード
                          </option>
                          <option value="transfer"
                            <?php if (isset($OrderCon->getValues()->pay)) { if ($OrderCon->getValues()->pay == 'transfer' && $number == $OrderCon->getValues()->number) { ?> selected
                            <?php }} elseif ($pay == 'transfer') { ?> selected
                            <?php } ?>>銀行振込
                          </option>
                          <option value="cash"
                            <?php if (isset($OrderCon->getValues()->pay)) { if ($OrderCon->getValues()->pay == 'cash' && $number == $OrderCon->getValues()->number) { ?> selected
                            <?php }} elseif ($pay == 'cash') { ?> selected
                            <?php } ?>>代引
                          </option>
                        </select>
                        <?php if (!($pay == 'credit')) { ?>
                        <input type="hidden" name="credit" value="1">
                        <?php } ?>
                        <p class="err-txt"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('pay')): ''; ?></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="order_content">
                  <p class="order_title">注文内容</p>
                  <table class="content_items">
                    <tbody>
                    <!-- オーダーされたグッズ繰り返しここから -->
                    <?php unset($total);
                    foreach($order_goods as $goods):
                      if (!($order->count >= '0')) :
                        if ($number == $goods->order_number && $goods->count >= 1) :
                          foreach($goods_data as $gd):
                            if ($goods->goods_id == $gd->id) :
                    ?>
                      <tr>
                        <th>
                            <?= h($gd->name); ?>
                            <input type="hidden" name="id[]" value="<?= $goods->id; ?>">
                            <input type="hidden" name="goods_id[]" value="<?= $goods->goods_id; ?>">
                        </th>
                        <td>
                          <input class="form-text number" type="text" name="count[]" value="<?= $goods->count; ?>">個
                        </td>
                        <td>
                          <?php if (!(empty($goods->color))) { ?>
                          color:
                          <select class="select" name="color[]">
                          <?php
                            foreach($goodsColors as $gc):
                              if ($gc->goods_id == $goods->goods_id) {
                          ?>
                            <option value="<?= $gc->color ?>" <?php if ($gc->color == $goods->color) { ?> selected <?php } ?>>
                              <?= h($gc->color); ?>
                            </option>
                          <?php } endforeach; ?>
                          </select>
                          <?php } else { ?>
                            <input type="hidden" name="color[]" value="">
                          <?php } ?>
                        </td>
                        <td>
                          <?php if (!(empty($goods->size))) { ?>
                          size:
                          <select class="select" name="size[]">
                          <?php
                            foreach($goodsSizes as $gs):
                              if ($gs->goods_id == $goods->goods_id) {
                          ?>
                            <option value="<?= $gs->size ?>" <?php if ($gs->size == $goods->size) { ?> selected <?php } ?>>
                              <?= h($gs->size); ?>
                            </option>
                          <?php } endforeach; ?>
                          </select>
                          <?php } else { ?>
                            <input type="hidden" name="size[]" value="">
                          <?php } ?>
                        </td>
                        <td>
                          ￥<?= number_format($goods->price); ?>／個
                        </td>
                        <td>
                          <button type="button" class="delete-btn">削除</button>
                        </td>
                      </tr>
                      <?php
                              $total[] = $goods->price * $goods->count;
                              endif;
                            endforeach;
                          endif;
                        endif;
                      endforeach;
                      ?>
                      <!-- オーダーされたグッズここまで -->
                      <tr class="add-order">
                        <th>
                          <select class="goods-name select" name="goods_id[]">
                            <option value="" selected>
                              商品を選択してください
                            </option>
                            <?php foreach($goods_data as $gd): ?>
                            <option value="<?= h($gd->id); ?>"
                              <?php if ($key == $OrderCon->getValues()->id && $gd->id == $OrderCon->getValues()->goods) { ?> selected <?php }?>
                            >
                              <?= h($gd->name); ?>
                            </option>
                            <?php  endforeach; ?>
                          </select>
                        </th>
                        <td>
                          <input class="form-text select-number" type="text" name="count[]" value="<?= ($key == $OrderCon->getValues()->id) ? h($OrderCon->getValues()->count): ''; ?>" placeholder="1">個
                        </td>
                        <td>
                          color:
                            <select class="select select-color" name="color[]">
                              <option value="">
                                カラーを選択してください
                              </option>
                                <?php
                                  foreach($goodsColors as $gc):
                                    if (!empty($gc->color)) {
                                ?>
                              <option class="color <?= h($gc->goods_id); ?>" value="<?= h($gc->color); ?>"
                                <?php if ($key == $OrderCon->getValues()->id && $gc->color == $OrderCon->getValues()->color) { ?> selected <?php }?>
                              >
                                <?= h($gc->color); ?>
                              </option>
                              <?php } endforeach; ?>
                            </select>
                        </td>
                        <td>
                          size:
                            <select class="select select-size" name="size[]">
                              <option value="" selected>
                                サイズを選択してください
                              </option>
                              <?php
                                foreach($goodsSizes as $gs):
                                  if (!empty($gs->size)) {
                              ?>
                              <option class="size <?= h($gs->goods_id); ?>" value="<?= h($gs->size); ?>">
                                <?= h($gs->size); ?>
                              </option>
                              <?php } endforeach; ?>
                            </select>
                        </td>
                        <td>
                          ￥
                          <?php
                            foreach($goods_data as $i => $goods): ?>
                              <p class="add-price <?= h($goods->id); ?>"><?= h($goods->price); ?></p>
                              <input name="price[<?= $key; ?>][]" type="hidden" class="add-price <?= h($goods->id); ?>" value="<?= h($goods->price); ?>">
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
                          <td><?= h($order->created); ?></td>
                        </tr>
                        <tr>
                          <th>更新日時：</th>
                          <td><?= h($order->modified); ?></td>
                        </tr>
                        <tr>
                          <th>商品合計金額：</th>
                          <td>
                            ￥<?= number_format(array_sum($total)); ?>
                          </td>
                        </tr>
                        <tr>
                          <th>消費税額：</th>
                          <td>
                            ￥<?= number_format(array_sum($total) * $order->tax_rate/100); ?>
                          </td>
                        </tr>
                        <tr>
                          <th>送料：</th>
                          <td>
                            ￥<?= number_format($order->postage); ?>
                          </td>
                        </tr>
                        <tr>
                          <th>合計金額：</th>
                          <td class="price">￥<?= number_format(array_sum($total) * (1 + $order->tax_rate/100) + $order->postage); ?></td>
                        </tr>
                        <tr>
                          <th>発送状態：</th>
                          <td>
                            <select class="select" name="status">
                              <option value="0" <?php if ($order->status == "0") { ?> selected <?php } ?>>発送準備中</option>
                              <option value="1" <?php if ($order->status == "1") { ?> selected <?php } ?>>発送完了</option>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="cancel">
                <p class="err-txt err-goods"><?= ($number == $OrderCon->getValues()->number) ? h($OrderCon->getErrors('goods')): ''; ?></p>
                <p class="err-txt err-count"></p>
                <p class="err-txt err-color"></p>
                <p class="err-txt err-size"></p>
                <input class="btn cancel-btn" type="submit" name="cancel" value="注文をキャンセルする">
                <input class="btn update-btn" type="submit" name="update" value="更新" >
                <input type="hidden" name="page_id" value="<?= h($now); ?>">
              </div>
            </div>
          </form>
          <?php endforeach; ?>
          <!-- オーダー繰り返しここまで -->
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
            <?php } ?>
    </section>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

