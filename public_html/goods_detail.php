<?php
require_once(__DIR__ .'/header.php');
$goodsMod = new Ec\Model\Goods();
$goods_id = $_GET['goods_id'];
$detail = $goodsMod->getGoods($goods_id);
$setting = $goodsMod->settings();
$ex = htmlspecialchars_decode($detail->explanation);
$color = array_filter(unserialize($detail->color), 'myFilter');
$size = array_filter(unserialize($detail->size), 'myFilter');
function myFilter($val) {
	return !($val === "");
}
$rate = $setting[0]->tax / 100;

$app = new Ec\Controller\Goods();
$app->run();
?>
  <div class="detail">
    <div div class="item__show">
      <div class="item_img">
        <img src="<?= !(empty($detail->image)) ? './gazou/'.h($detail->image) : './asset/img/noimage.png'; ?>">
      </div>
      <form method="post" action="">
        <div class="item_detail">
          <h1><?= h($detail->goods_name); ?></h1>
          <div class="detail_price tax_in">
            <span class="price">￥<?= number_format($detail->price * ($rate + 1)); ?></span>
            <input type="hidden" name="price" value="<?= h($detail->price); ?>">
            <span class="tax">（内税￥<?= number_format($detail->price * $rate); ?>）</span>
          </div>
          <div class="specification">
            <table>
              <tbody>
                <tr>
                  <?php if (!empty($color)) { ?>
                  <th>color</th>
                  <td>
                    <select name="color">
                      <?php foreach($color as $c): ?>
                      <option value="<?= h($c); ?>"><?= !empty($c) ? h($c) : ''; ?></option>
                      <?php endforeach ?>
                    </select>
                  </td>
                  <?php } ?>
                </tr>
                <tr>
                  <?php if (!empty($size)) { ?>
                  <th>size</th>
                  <td>
                    <select name="size">
                      <?php foreach($size as $s): ?>
                      <option value="<?= h($s); ?>"><?= h($s); ?></option>
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
        </div>
      </form>
    </div>
    <div class="explanation">
      <p class="item_contents">商品説明</p>
      <div class="contents">
        <p><?= $ex; ?></p>
      </div>
    </div>
  </div>
<?php
  require_once(__DIR__ .'/footer.php');
?>

