<?php
require_once(__DIR__ .'/header.php');
$goods = $goodsMod->goods();
?>
  <section class="mv">
    <h2><p>ここちいい<br>つけごこち</p></h2>
  </section>
  <div class="top_text">
    <h3 class="more">better life...</h3>
    <p>
      毎日を少しだけいいものに。<br>
      生活の中に溶け込むVueのアクセサリー。
    </p>
  </div>
  <section class="item__list">
    <?php foreach($goods as $item):
      $id = $item->id; ?>
    <li class="item">
      <a class="item_link" href="<?= SITE_URL; ?>/goods_detail.php?goods_id=<?= $id; ?>">
      <img src="<?= !(empty($item->image)) ? './image/'.h($item->image) : './asset/img/noimage.png'; ?>">
      <h4><?= h($item->name); ?></h4>
      <div class="detail_price">
        <span class="price">￥<?= number_format($item->price * ($rate + 1)); ?></span>
      </div>
      </a>
    </li>
    <?php endforeach ?>
  </section>
<?php
  require_once(__DIR__ .'/footer.php');
?>

