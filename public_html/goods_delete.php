<?php
require_once(__DIR__ .'/header_admin.php');
$goodsCon->run();
$id = $_SESSION['delete_goods_id'];

foreach ($goodsMod->goods() as $data) {
  if ($data->id == $id) {
    $goods_data = $data;
  }
}

foreach ($goodsMod->goods_sizes() as $s) {
  if ($s->goods_id == $id) {
    $size[] = $s;
  }
}

foreach ($goodsMod->goods_colors() as $c) {
  if ($c->goods_id == $id) {
    $color[] = $c;
  }
}
?>
<div class="main">
    <div class="container">
      <h2>この商品を本当に削除しますか。</h2>
      <form class="delete_form" method="post" action="">
        <div class="goods">
          <div class="goods_image">
            <img src="<?= !(empty($goods_data->image)) ? './image/'.h($goods_data->image) : './asset/img/noimage.png'; ?>">
          </div>
          <div class="goods_detail">
            <p class="note">※一度削除すると元には戻せません。</p>
            <table>
              <tbody>
                <tr>
                  <th>商品名</th>
                  <td>
                    <p><?= h($goods_data->name); ?></p>
                  </td>
                </tr>
                <tr>
                  <th>金額</th>
                  <td>
                    <p class="price"><?= h($goods_data->price) ?>円</p>
                  </td>
                </tr>
                <?php if ($size): ?>
                <tr>
                  <th>サイズ</th>
                  <td>
                    <p class="size">
                    <?php foreach ($size as $key => $s):?>
                        <?= h($s->size); ?>
                        <?php if (!($key == array_key_last($size))): ?>,<?php endif; ?>
                      <?php endforeach; ?>
                    </p>
                  </td>
                </tr>
                <?php endif; ?>
                <?php if ($color): ?>
                <tr>
                  <th>カラー</th>
                  <td>
                    <p class="color">
                      <?php foreach ($color as $key => $c):?>
                        <?= h($c->color); ?>
                        <?php if (!($key == array_key_last($color))): ?>,<?php endif; ?>
                      <?php endforeach; ?>
                    </p>
                  </td>
                </tr>
                <?php endif; ?>
                <tr>
                  <th>
                    商品説明
                  </th>
                  <td>
                    <code><?= h($goods_data->explanation); ?></code>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="back-delete">
          <a class="back btn" href="<?= SITE_URL; ?>/goods_confirm.php">やめる</a>
          <input class="btn" type="submit" name="goods_delete" value="削除">
          <input type="hidden" name="id" value="<?= h($id); ?>">
        </div>
      </form>
    </div>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

