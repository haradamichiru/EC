<?php
require_once(__DIR__ .'/header_admin.php');
$goods = $goodsMod->goods();
$sizes = $goodsMod->sizes();
$goodsSizes = $goodsMod->goods_sizes();
$colors = $goodsMod->colors();
$goodsColors = $goodsMod->goods_colors();
$postage = $goodsMod->settings()[0]->postage;

?>
    <!-- 商品追加 -->
    <div class="container">
      <h2>商品の追加</h2>
      <form class="container_form" method="post" action="" onsubmit="return validateFormGoodsAdd()" name="goodsFormAdd" enctype="multipart/form-data">
        <div class="addition_detail">
          <table>
            <tbody>
              <tr>
                <th>
                  <label for="new_goods_name">商品名</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="new_goods_name" value="<?=h($goodsCon->getValues()->goods_name ?? ''); ?>">
                  <p class="err-txt" id="err-goods"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label for="new_goods_price">金額</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="new_goods_price" value="<?= h($goodsCon->getValues()->price ?? ''); ?>">
                  <p class="err-txt" id="err-price"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label for="new_goods_price">商品説明</label>
                </th>
                <td>
                  <textarea class="form-text" name="new_explanation" value="<?= h($goodsCon->getValues()->explanation ?? ''); ?>"></textarea>
                  <p class="err-txt" id="err-ex"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label>サイズ</label>
                </th>
                <td class="size-list">
                  <div class="size">
                    <?php foreach($sizes as $size):
                      if (!(empty($size->size))) { ?>
                      <div class="list">
                        <input class="form-text" name="size[<?= $size->id; ?>]" id="new_size[<?= $size->id; ?>]" type="checkbox" value="<?= h($size->size); ?>">
                        <label class="label" for="new_size[<?= $size->id; ?>]"><?= h($size->size); ?></label>
                      </div>
                    <?php } endforeach ?>
                  </div>
                  <buttton type="button" name="size-button" class="btn" onclick="location.href='size.php'">サイズ編集</button>
                </td>
              </tr>
              <tr>
                <th>
                  <label>カラー</label>
                </th>
                <td class="color-list">
                  <div class="color">
                    <?php foreach($colors as $color):
                      if (!(empty($color->color))) { ?>
                      <div class="list">
                        <input class="form-text color" name="color[<?= $color->id; ?>]" id="new_color[<?= $color->id; ?>]" type="checkbox" value="<?= h($color->color); ?>">
                        <label class="label" for="new_color[<?= $color->id; ?>]"><?= h($color->color); ?></label>
                      </div>
                    <?php } endforeach ?>
                  </div>
                  <buttton type="button" name="color-button" class="btn" onclick="location.href='color.php'">カラー編集</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="entry">
          <div class="goods_image">
            <label>
              <span class="edit-btn">編集
                <input type="file" name="add_image" class="form" style="display:none" accept="image/*">
              </span>
            </label>
            <div class="imgfile">
              <img class="add_image" src="<?= isset($goodsCon->getValues()->image) ? './image/'.h($goodsCon->getValues()->image) : './asset/img/noimage.png'; ?>" alt="">
            </div>
          </div>
          <div class="goods_addition">
            <input class="btn" type="submit" value="登録" name="add">
            <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
          </div>
        </div>
      </form>
    </div>
    <!-- 送料設定 -->
    <div class="container">
      <h2>送料設定</h2>
      <form class="setting_form" method="post" action="" onsubmit="return validateFormSetting()" name="settingForm">
        <div class="setting">
        <table>
            <tbody>
              <tr>
                <th>
                  <label for="postage">送料</label>
                </th>
                <td>
                  <p class="price">
                    <input class="form-text setting" type="text" name="postage" value="<?= h($postage ?? ''); ?>">円
                  </p>
                  <p class="err-txt" id="err-postage"></p>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="setting_button">
          <input class="btn" type="submit" value="更新" name="setting">
          <input type="hidden" value="<?= h($_SESSION['me']->loginid); ?>" name="user_id">
          </div>
        </div>
      </form>
    </div>
    <!-- 商品編集 -->
    <div class="container">
      <h2>商品の編集・削除</h2>
      <?php foreach($goods as $key => $item):
        $id = $item->id; ?>
      <form class="container_form" method="post" action="" onsubmit="return validateFormGoodsUpdate()" name="goodsFormUpdate_<?= h($id); ?>" enctype="multipart/form-data">
        <div class="goods_list">
          <div class="goods" id="goods[<?= $id ?>]">
            <div class="goods_image">
              <span class="delete_image-btn">削除</span>
              <label>
                <span class="edit-btn">編集
                  <input type="file" name="edit_image" class="edit_button" style="display:none" accept="image/*">
                </span>
              </label>
              <div class="image_file">
                <input type="hidden" class="old_image" name="image" value="<?= h($item->image); ?>">
                <img class="edit_image" src="<?= !(empty($item->image)) ? './image/'.h($item->image) : './asset/img/noimage.png'; ?>">
              </div>
            </div>
            <div class="goods_detail">
              <table>
                <tbody>
                  <tr>
                    <th>商品名</th>
                    <td>
                      <p><input class="form-text" name="goods_name" type="text" value="<?= ($id == $goodsCon->getValues()->id) ? h($goodsCon->getvalues()->goods_name): h($item->name); ?>"></p>
                      <input class="id" name="id" type="hidden" value="<?= $id; ?>">
                      <p class="err-txt"><?= ($id == $goodsCon->getValues()->id && $goodsCon->getErrors('goods_name')) ? h($goodsCon->getErrors('goods_name')): ''; ?></p>
                    </td>
                  </tr>
                  <tr>
                    <th>金額</th>
                    <td>
                      <p class="price"><input class="form-text" name="price" type="text" value="<?= ($id == $goodsCon->getValues()->id) ? h($goodsCon->getvalues()->price): h($item->price); ?>">円</p>
                      <p class="err-txt"><?= ($id == $goodsCon->getValues()->id && $goodsCon->getErrors('price')) ? h($goodsCon->getErrors('price')): ''; ?></p>
                    </td>
                  </tr>
                  <tr>
                    <th>サイズ</th>
                    <td>
                      <div class="size">
                        <?php foreach($sizes as $size):
                          if (!(empty($size->size))) { ?>
                          <div class="size-list">
                            <input class="form-text size" name="size[]" id="size[<?= $id; ?>][<?= $size->id; ?>]" type="checkbox" value="<?= h($size->size); ?>"
                            <?php foreach($goodsSizes as $gSize):
                              if ($id == $gSize->goods_id && $size->size == $gSize->size && $gSize->delflag == '0') { ?> checked
                            <?php } endforeach ?> >
                            <label class="label" for="size[<?= $id; ?>][<?= $size->id; ?>]"><?= h($size->size); ?></label>
                          </div>
                        <?php } endforeach ?>
                      </div>
                    </td>
                  </tr>
                  <tr>
                  <tr>
                    <th>カラー</th>
                    <td>
                      <div class="color">
                        <?php foreach($colors as $color):
                          if (!(empty($color->color))) { ?>
                          <div class="color-list">
                            <input class="form-text color" name="color[]" id="color[<?= $id; ?>][<?= $color->id; ?>]" type="checkbox" value="<?= h($color->color); ?>"
                            <?php foreach($goodsColors as $gColor):
                            if ($id == $gColor->goods_id && $color->color == $gColor->color && $gColor->delflag == '0') { ?> checked
                            <?php } endforeach ?> >
                            <label class="label" for="color[<?= $id; ?>][<?= $color->id; ?>]"><?= h($color->color); ?></label>
                          </div>
                        <?php } endforeach ?>
                      </div>
                    </td>
                  </tr>
                    <th>
                      商品説明
                    </th>
                    <td>
                      <textarea class="form-text large" name="explanation"><?= h($goodsCon->getvalues()->explanation ?? $item->explanation); ?></textarea>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="buttons">
              <p class="delete">
                <input class="delete_goods" type="submit" name="delete_confirm" value="削除">
              </p>
              <div class="goods_edit">
                <input class="btn update-goods" type="submit" name="goods_update" value="更新">
                <p class="err-txt" id="err-goods_update"></p>
                <p class="err-txt" id="err-price_update"></p>
              </div>
            </div>
          </div>
        </div>
      </form>
      <?php endforeach ?>
    </div>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

