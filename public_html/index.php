<?php
require_once(__DIR__ .'/header.php');
$goodsMod = new Ec\Model\Goods();
$goods = $goodsMod->goods();
$setting = $goodsMod->settings();

$rate = $setting[0]->tax / 100;
?>
  <div class="mv">
    <h1><p>ここちいい<br>つけごこち</p></h1>
  </div>
  <div class="top_text">
    <p class="more">better life...</p>
    <p>
      毎日を少しだけいいものに。<br>
      生活の中に溶け込むVueのアクセサリー。
    </p>
  </div>
  <div class="item__list">
    <?php foreach($goods as $item):
      $id = $item->id; ?>
    <li class="item">
      <a class="item_link" href="<?= SITE_URL; ?>/goods_detail.php?goods_id=<?= $id; ?>">
        <img src="<?= !(empty($item->image)) ? './gazou/'.h($item->image) : './asset/img/noimage.png'; ?>"><br>
        <h2><?= h($item->goods_name); ?></h2>
        <div class="detail_price">
          <span class="price">￥<?= number_format($item->price * ($rate + 1)); ?></span>
        </div>
      </a>
    </li>
    <?php endforeach ?>
  </div>
<?php
  require_once(__DIR__ .'/footer.php');
?>

