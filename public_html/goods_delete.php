<?php
require_once(__DIR__ .'/header_admin.php');
$goodsCon = new Ec\Controller\Goods();
$goodsCon->run();
$goodsMod = new Ec\Model\Goods();
$goods = $goodsMod->goods();
$id = $_SESSION['delete_id'];

for ($i = 0 ; $i < count($goods); $i++) {
  if ($goods[$i]->id == $id) {
    $name = $goods[$i]->goods_name;
    $price = $goods[$i]->price;
    $image = $goods[$i]->image;
    $ex = htmlspecialchars_decode($goods[$i]->explanation);
    $color = array_filter(unserialize($goods[$i]->color), 'myFilter');
    $size = array_filter(unserialize($goods[$i]->size), 'myFilter');
  }
}
function myFilter($val) {
  return !($val === "");
}
?>
<div class="main">
    <div class="container">
      <h2>この商品を本当に削除しますか。</h2>
      <form class="delete_form" method="post" action="">
        <div class="goods">
          <div class="goods_image">
            <img src="<?= !(empty($image)) ? './gazou/'.h($image) : './asset/img/noimage.png'; ?>">
          </div>
          <div class="goods_detail">
            <p class="note">※一度削除すると元には戻せません。</p>
            <table>
              <tbody>
                <tr>
                  <th>商品名</th>
                  <td>
                    <p><?= h($name); ?></p>
                  </td>
                </tr>
                <tr>
                  <th>金額</th>
                  <td>
                    <p class="price"><?= h($price) ?>円</p>
                  </td>
                </tr>
                <?php if (!(empty($size))): ?>
                <tr>
                  <th>サイズ</th>
                  <td>
                    <p class="size">
                    <?php foreach ($size as $key => $s):?>
                        <?= h($s); ?>
                        <?php if (!($key == array_key_last($size))): ?>,<?php endif; ?>
                      <?php endforeach; ?>
                    </p>
                  </td>
                </tr>
                <?php endif; ?>
                <tr>
                  <th>カラー</th>
                  <td>
                    <p class="color">
                      <?php foreach ($color as $key => $c):?>
                        <?= h($c); ?>
                        <?php if (!($key == array_key_last($color))): ?>,<?php endif; ?>
                      <?php endforeach; ?>
                    </p>
                  </td>
                </tr>
                <tr>
                  <th>
                    商品説明
                  </th>
                  <td>
                    <code><?= h($ex); ?></code>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="back-delete">
          <button class="btn"><a class="back" href="javascript:history.back();">やめる</a></button>
          <input class="btn" type="submit" name="goods_delete" value="削除">
          <input type="hidden" name="id" value="<?= h($id); ?>">
        </div>
      </form>
    </div>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

