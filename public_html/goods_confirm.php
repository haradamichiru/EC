<?php
require_once(__DIR__ .'/header_admin.php');
$goodsCon = new Ec\Controller\Goods();
$goodsCon->run();
$orderCon = new Ec\Controller\Order();
$orderCon->run();

$goodsMod = new Ec\Model\Goods();
$goods = $goodsMod->goods();
$setting = $goodsMod->settings();

for ($i = 0; $i < count($goods); $i++) {
  $color[$i] = array_filter(unserialize($goods[$i]->color), 'myFilter');
  $size[$i] = unserialize($goods[$i]->size);
  $ex[$i] = htmlspecialchars_decode($goods[$i]->explanation);
}
function myFilter($val) {
  return !($val === "");
}

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
                  <label for="goods_name">商品名</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="goods_name" value="<?= isset($goodsCon->getValues()->goods_name) ? h($goodsCon->getValues()->goods_name): ''; ?>">
                  <p class="err-txt" id="err-goods"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label for="goods_price">金額</label>
                </th>
                <td>
                  <input class="form-text" type="text" name="goods_price" value="<?= isset($goodsCon->getValues()->price) ? h($goodsCon->getValues()->price): ''; ?>">
                  <p class="err-txt" id="err-price"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label for="goods_price">商品説明</label>
                </th>
                <td>
                  <code class="small">※改行する場合は	&lt;br&gt;を入力してください。</code>
                  <textarea class="form-text" name="explanation" value="<?= isset($goodsCon->getValues()->explanation) ? h($goodsCon->getValues()->explanation): ''; ?>"></textarea>
                  <p class="err-txt" id="err-ex"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label>サイズ</label>
                </th>
                <td>
                  <p class="small">※サイズを設定する場合は、単位と共にこちらに入力してください。</p>
                  <p class="size">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                    <input class="form-text size" name="size[]" type="text" value="<?= isset($goodsCon->getValues()->size) ? h($goodsCon->getValues()->size): ''; ?>">
                  </p>
                </td>
              </tr>
              <tr>
                <th>
                  <label>カラー</label>
                </th>
                <td>
                  <p class="small">※カラーを設定する場合は、こちらに入力してください。</p>
                  <p class="color">
                    <input class="form-text color" name="color[]" type="text" value="<?= isset($goodsCon->getValues()->color) ? h($goodsCon->getValues()->color): ''; ?>">
                    <input class="form-text color" name="color[]" type="text" value="<?= isset($goodsCon->getValues()->color) ? h($goodsCon->getValues()->color): ''; ?>">
                    <input class="form-text color" name="color[]" type="text" value="<?= isset($goodsCon->getValues()->color) ? h($goodsCon->getValues()->color): ''; ?>">
                    <input class="form-text color" name="color[]" type="text" value="<?= isset($goodsCon->getValues()->color) ? h($goodsCon->getValues()->color): ''; ?>">
                    <input class="form-text color" name="color[]" type="text" value="<?= isset($goodsCon->getValues()->color) ? h($goodsCon->getValues()->color): ''; ?>">
                  </p>
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
              <img class="add_image" src="<?= isset($goodsCon->getValues()->image) ? './gazou/'.h($goodsCon->getValues()->image) : './asset/img/noimage.png'; ?>" alt="">
            </div>
          </div>
          <div class="goods_addition">
            <input class="btn" type="submit" value="登録" name="add">
            <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
          </div>
        </div>
      </form>
    </div>
    <!-- 送料・消費税設定 -->
    <div class="container">
      <h2>送料・消費税設定</h2>
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
                    <input class="form-text setting" type="text" name="postage" value="<?= isset($setting[0]->postage) ? h($setting[0]->postage): ''; ?>">円
                  </p>
                  <p class="err-txt" id="err-postage"></p>
                </td>
              </tr>
              <tr>
                <th>
                  <label for="tax">消費税</label>
                </th>
                <td>
                  <p class="price">
                    <input class="form-text setting" type="text" name="tax" value="<?= isset($setting[0]->tax) ? h($setting[0]->tax): ''; ?>">％
                  </p>
                  <p class="err-txt" id="err-tax"></p>
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
      <form class="container_form" method="post" action="" onsubmit="return validateFormGoodsUpdate()" name="goodsFormUpdate" enctype="multipart/form-data">
        <div class="goods_list">
        <?php foreach($goods as $key => $item):
          $id = $item->id; ?>
          <div class="goods">
            <div class="goods_image">
              <span class="delete_image-btn">削除</span>
              <label>
                <span class="edit-btn">編集
                  <input type="file" name="edit_image[<?= $id; ?>]" class="edit_button" style="display:none" accept="image/*">
                </span>
              </label>
              <div class="image_file">
                <input type="hidden" class="old_image" name="old_image[<?= $id; ?>]" value="<?= h($item->image); ?>">
                <img class="edit_image" src="<?= !(empty($item->image)) ? './gazou/'.h($item->image) : './asset/img/noimage.png'; ?>">
              </div>
            </div>
            <div class="goods_detail">
              <table>
                <tbody>
                  <tr>
                    <th>商品名</th>
                    <td>
                      <p><input class="form-text" name="goods_name[<?= $id; ?>]" type="text" value="<?= h($item->goods_name); ?>"></p>
                      <input class="id" name="id" type="hidden" value="<?= $id; ?>">
                    </td>
                  </tr>
                  <tr>
                    <th>金額</th>
                    <td>
                      <p class="price"><input class="form-text" name="price[<?= $id; ?>]" type="text" value="<?= h($item->price); ?>">円</p>
                    </td>
                  </tr>
                  <tr>
                    <th>カラー</th>
                    <td>
                      <p class="color">
                        <?php foreach($color[$key] as $c):?>
                          <input class="form-text color" name="color[<?= $id; ?>][]" type="text" value="<?= h($c); ?>">
                        <?php endforeach ?>
                        <input class="add-color btn" name="color_add" type="button" value="追加">
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <th>サイズ</th>
                    <td>
                      <p class="size">
                        <?php foreach($size[$key] as $s):?>
                          <input class="form-text size" name="size[<?= $id; ?>][]" type="text" value="<?= h($s); ?>">
                        <?php endforeach ?>
                        <input class="add-size btn" name="size_add" type="button" value="追加">
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      商品説明
                    </th>
                    <td>
                      <code class="small">※改行する場合は	&lt;br&gt;を入力してください。</code>
                      <textarea class="form-text" name="explanation[<?= $id; ?>]"><?= h($item->explanation); ?></textarea>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="buttons">
              <p class="delete">
                <input class="delete_goods" type="submit" name="delete_confirm[<?= $id ?>]" value="削除">
              </p>
              <div class="goods_edit">
                <input class="btn" type="submit" name="goods_update[<?= $id ?>]" value="更新">
                <p class="err-txt" id="err-goods_update"></p>
                <p class="err-txt" id="err-price_update"></p>
              </div>
            </div>
          </div>
          <?php endforeach ?>
      </form>
    </div>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

