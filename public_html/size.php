<?php
require_once(__DIR__ .'/header_admin.php');
$goodsCon = new Ec\Controller\Goods();
$goodsCon->run();
$goodsMod = new Ec\Model\Goods();
$sizes = $goodsMod->sizes();
?>
    <div class="container">
      <h2>サイズ編集</h2>
      <form class="size_form" method="post" action="">
        <div class="size">
          <div class="size_detail">
            <p>テキストボックスのドラックで並び替えが可能です。</p>
            <?php
              $i = 1;
              $j = 1;
              foreach ($sizes as $size):
              if (!empty($size->size)) { ?>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?= $i++; ?>">
              <input class="form-text size sort" id="<?= $j++; ?>" name="size[]" type="text" value="<?= h($size->size); ?>" draggable="true">
            </li>
            <?php } endforeach; ?>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?=  $i++; ?>">
              <input class="form-text size sort" id="<?= $j++; ?>" name="size[]" type="text" value="" draggable="true">
            </li>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?= $i++; ?>">
              <input class="form-text size sort" id="<?= $j++; ?>" name="size[]" type="text" value="" draggable="true">
            </li>
            <li>
              <input class="form-text" name="id[]" type="hidden" value="<?= $i++; ?>">
              <input class="form-text size sort" id="<?= $j++; ?>" name="size[]" type="text" value="" draggable="true">
            </li>
            <p class="note">※空欄で更新すると削除されます。</p>
          </div>
        </div>
        <div class="back-delete">
          <button class="btn"><a class="back" href="<?= SITE_URL; ?>/goods_confirm.php">もどる</a></button>
          <input class="btn" type="submit" name="size_update" value="更新">
        </div>
      </form>
    </div>

<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

