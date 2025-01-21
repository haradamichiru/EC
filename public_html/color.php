<?php
require_once(__DIR__ .'/header_admin.php');
$goodsCon = new Ec\Controller\Goods();
$goodsCon->run();
$goodsMod = new Ec\Model\Goods();
$colors = $goodsMod->colors();
?>
    <div class="container">
      <h2>カラー編集</h2>
      <form class="color_form" method="post" action="">
        <div class="color">
          <div class="color_detail">
            <p>テキストボックスのドラックで並び替えが可能です。</p>
            <?php
              $i = 1;
              $j = 1;
              foreach ($colors as $color):
              if (!empty($color->color)) { ?>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?= $i++; ?>">
              <input class="form-text color sort" id="<?= $j++; ?>" name="color[]" type="text" value="<?= h($color->color); ?>" draggable="true">
            </li>
            <?php } endforeach; ?>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?=  $i++; ?>">
              <input class="form-text color sort" id="<?= $j++; ?>" name="color[]" type="text" value="" draggable="true">
            </li>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?= $i++; ?>">
              <input class="form-text color sort" id="<?= $j++; ?>" name="color[]" type="text" value="" draggable="true">
            </li>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?= $i++; ?>">
              <input class="form-text color sort" id="<?= $j++; ?>" name="color[]" type="text" value="" draggable="true">
            </li>
            <p class="note">※空欄で更新すると削除されます。</p>
          </div>
        </div>
        <div class="back-delete">
          <button class="btn"><a class="back" href="<?= SITE_URL; ?>/goods_confirm.php">もどる</a></button>
          <input class="btn" type="submit" name="color_update" value="更新">
        </div>
      </form>
    </div>

<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

