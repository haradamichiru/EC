<?php
require_once(__DIR__ .'/header.php');
$detail = $goodsMod->getGoods($_GET['goods_id']);
$sizeMod = $goodsMod->goods_sizes();
$colorMod = $goodsMod->goods_colors();

foreach ($sizeMod as $size) {
  if ($size->goods_id == $_GET['goods_id']) {
    $goodsSize[] = $size->size;
  }
}
foreach ($colorMod as $color) {
  if ($color->goods_id == $_GET['goods_id']) {
    $goodsColor[] = $color->color;
  }
}

$app = new Ec\Controller\Goods();
$app->run();
?>
  <div class="detail">
    <div div class="item__show">
      <div class="item_img">
        <img src="<?= !(empty($detail->image)) ? './image/'.h($detail->image) : './asset/img/noimage.png'; ?>">
      </div>
      <form method="post" action="">
        <section class="item_detail">
          <h1><?= h($detail->name); ?></h1>
          <div class="detail_price tax_in">
            <span class="price">￥<?= number_format($detail->price * (RATE + 1)); ?></span>
            <input type="hidden" name="price" value="<?= h($detail->price); ?>">
            <span class="tax">（内税￥<?= number_format($detail->price * RATE); ?>）</span>
          </div>
          <div class="specification">
            <table>
              <tbody>
                <tr>
                  <?php if (!empty($goodsSize)) { ?>
                  <th>size</th>
                  <td>
                    <select class="select" name="size">
                      <?php foreach($goodsSize as $s): ?>
                      <option value="<?= h($s); ?>"><?= h($s); ?></option>
                      <?php endforeach ?>
                    </select>
                  </td>
                  <?php } ?>
                </tr>
                <tr>
                  <?php if (!empty($goodsColor)) { ?>
                  <th>color</th>
                  <td>
                    <select class="select" name="color">
                      <?php foreach($goodsColor as $c): ?>
                      <option value="<?= h($c); ?>"><?= h($c); ?></option>
                      <?php endforeach ?>
                    </select>
                  </td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="quantity">
            <button class="quantity__button no-js-hidden" name="minus" type="button">
              <span class="visually-hidden">個数を減らす</span>
              <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="presentation"
                class="icon icon-minus" fill="none" viewBox="0 0 10 2">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M.5 1C.5.7.7.5 1 .5h8a.5.5 0 110 1H1A.5.5 0 01.5 1z" fill="currentColor"> </path>
              </svg>
            </button>
            <input class="quantity__input" type="text" name="count[]" value="1" min="0" aria-label=""
              id="Quantity-1" data-index="1">
            <button class="quantity__button no-js-hidden" name="plus" type="button">
              <span class="visually-hidden">個数を増やす</span>
              <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="presentation"
                class="icon icon-plus" fill="none" viewBox="0 0 10 10">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M1 4.51a.5.5 0 000 1h3.5l.01 3.5a.5.5 0 001-.01V5.5l3.5-.01a.5.5 0 00-.01-1H5.5L5.49.99a.5.5 0 00-1 .01v3.5l-3.5.01H1z"
                  fill="currentColor"> </path>
              </svg></button>
          </div>
          <input class="btn" type="submit" name="add_cart" value="カートに入れる">
          <input type="hidden" name="id" value="<?= h($detail->id); ?>">
        </section>
      </form>
    </div>
    <section class="explanation">
      <h4 class="item_contents">商品説明</h4>
      <div class="contents">
        <p><?= nl2br(htmlspecialchars($detail->explanation, ENT_QUOTES, 'UTF-8')); ?></p>
      </div>
    </section>
  </div>
<?php
  require_once(__DIR__ .'/footer.php');
?>

