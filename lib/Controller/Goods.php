<?php
namespace Ec\Controller;
class Goods extends \Ec\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
      $this->addGoods();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['add_cart'])) {
      $this->cart();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
      $this->customer();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
      $this->delete();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change'])) {
      $this->change();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_confirm']) ) {
      $this->deleteConfirm();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goods_delete']) ) {
      $this->goodsDelete();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goods_update']) ) {
      $this->goodsUpdate();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['size_update']) ) {
      $this->sizeUpdate();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['color_update']) ) {
      $this->colorUpdate();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setting']) ) {
      $this->setting();
    }

  }

  // 商品POSTデータ格納
  private function goods() {
    $goods = [
      'goods_name' => $_POST['goods_name'],
      'price' => $_POST['goods_price'],
      'explanation' => htmlspecialchars($_POST['explanation']),
      'image' => $_POST['image'],
    ];
    return $goods;
  }

  // 商品追加
  protected function addGoods() {
    // 画像データをフォルダに格納して$_POST['image']として保存
    $goods_img = $_FILES['add_image'];
    $ext = substr($goods_img['name'], strrpos($goods_img['name'], '.') + 1);
    $goods_img['name'] = uniqid("img_") .'.'. $ext;
    $GoodsMod = new \Ec\Model\Goods();
    move_uploaded_file($goods_img['tmp_name'],'./image/'.$goods_img['name']);
    $_POST['image'] = $goods_img['name'];

    $GoodsMod->create($this->goods());
    $id = end($GoodsMod->goods())->id; // ↑で追加した商品のIDを取得
    if ($_POST['size']) { //サイズが送信された場合
      foreach ($_POST['size'] as $size) {
        // 商品サイズ追加
        $GoodsMod->goodsSizeCreate([
          'goods_id' => $id,
          'color' => $size,
        ]);
      }
    }
    if ($_POST['color']) { //カラーが送信された場合
      foreach ($_POST['color'] as $color) {
        // 商品カラー追加
        $GoodsMod->goodsColorCreate([
          'goods_id' => $id,
          'color' => $color,
        ]);
      }
    }
  }

  // ショッピングカートへ商品を追加
  protected function cart() {
    if ($_SESSION['cart'] == array()) { // SESSION[cart]内が空だった場合
      $i = 0;
      $_SESSION['cart'][$i] = $_GET; // SESSION[cart]へGETされたデータを格納
    } else {
      $i = array_key_last($_SESSION['cart'])+1;
      $j = array_keys($_SESSION['cart']);
      $k = 0;
      while ($k <= count($j)) {
        // SESSION[cart]内のid、サイズ、カラーとGETデータが一致した場合
        if ($_SESSION['cart'][$k]['id'] == $_GET['id'] && $_SESSION['cart'][$k]['size'] == $_GET['size'] && $_SESSION['cart'][$k]['color'] == $_GET['color']) {
          $_SESSION['cart'][$k]['count'][0] = $_GET['count'][0] + $_SESSION['cart'][$k]['count'][0]; // SESSION[cart]の該当商品数を更新
          break;
        // GETされたデータにサイズが含まれていないかつ、SESSION[cart]内のid、カラーがGETデート一致した場合
        } elseif (empty($_GET['size']) && $_SESSION['cart'][$k]['id'] == $_POST['id'] && $_SESSION['cart'][$k]['color'] == $_POST['color']) {
          $_SESSION['cart'][$k]['count'][0] = $_POST['count'][0] + $_SESSION['cart'][$k]['count'][0]; // SESSION[cart]の該当商品数を更新
          break;
        // GETされたデータにカラーが含まれていないかつ、SESSION[cart]内のid、サイズがGETデート一致した場合
        } elseif (empty($_GET['color']) && $_SESSION['cart'][$k]['id'] == $_POST['id'] && $_SESSION['cart'][$k]['size'] == $_POST['size']) {
          $_SESSION['cart'][$k]['count'][0] = $_POST['count'][0] + $_SESSION['cart'][$k]['count'][0]; // SESSION[cart]の該当商品数を更新
          break;
        } else {
          $_SESSION['cart'][$i] = $_GET; // GETされたデータをSESSION[cart]に格納
          break;
        }
      }
    }
    header('Location: ' . SITE_URL . '/shopping_all.php'); // カート内一覧画面へ遷移
    exit();
  }

  // 注文者情報入力画面へ遷移
  private function customer() {
    header('Location: ' . SITE_URL . '/shopping_information.php'); // 注文者情報入力画面へ遷移
    exit();
  }

  // ショッピングカートから商品を削除
  private function delete() {
    // SESSION[cart]内すべてを繰り返し
    for ($k = 0; $k <= array_key_last($_SESSION['cart']); $k++) {
      // SESSION[cart]のidとポストされたidが同じだった場合
      if ($_SESSION['cart'][$k]['id'] == $_POST['delete']) {
        unset($_SESSION['cart'][$k]); // 該当idのSESSION[cart]を空にする
        header('Location: ' . SITE_URL . '/shopping_all.php'); // 画面更新
        exit();
        break;
      }
    }
  }

  // ショッピングカートの商品数量変更
  private function change() {
    $id = $_POST['change'];
    $count = $_POST['count'][$id];
    $j = array_key_last($_SESSION['cart']);
    for ($k = 0; $k <= $j; $k++) {
      if ($_SESSION['cart'][$k]['id'] == $id) {
        $_SESSION['cart'][$k]['count'][0] = $count;
        header('Location: ' . SITE_URL . '/shopping_all.php'); // 画面更新
        exit();
        break;
      }
    }
    $_SESSION['cart'][$id]['count'][0] = $count;
    header('Location: ' . SITE_URL . '/shopping_all.php'); // 画面更新
    exit();
  }

  // 商品削除
  protected function deleteConfirm() {
    $_SESSION['delete_id'] = array_keys($_POST['delete_confirm'])[0]; // 押下した削除ボタンのキーを取得（IDを取得）
    header('Location: ' . SITE_URL . '/goods_delete.php'); // 商品削除画面へ遷移
    exit();
  }

  // 商品削除
  private function goodsDelete() {
    $GoodsMod = new \Ec\Model\goods();
    $GoodsMod->delete([
      'id' => $_POST['id'],
    ]);
    header('Location: '. SITE_URL . '/goods_confirm.php');
    exit();
  }

  // 商品情報更新
  protected function goodsUpdate() {
    $id = array_keys($_POST['goods_update'])[0]; // POSTされたID情報を$idとして変数格納
    $goods_img = $_FILES['edit_image'];
    $old_img = $_POST['old_image'][$id];
    $goodsMod = new \Ec\Model\Goods();
    $goodsSizes = $goodsMod->goods_sizes();
    $goodsColors = $goodsMod->goods_colors();
    $postSize = $_POST['size'][$id]; // POSTされたサイズを配列として格納
    $postColor = $_POST['color'][$id]; // POSTされたカラーを配列として格納

    // サイズ情報更新
    if (!empty($postSize)) { // サイズがPOSTされていた場合
      $counter = 0;
      foreach ($goodsSizes as $size) {
        if ($size->goods_id == $id) { // DB"goods_size"の中でPOSTされたidと同じものがあった場合
          $counter++; // DB内のgoods_idとidが同じ個数をカウント
          $sizeMod[] = $size->id; // DBのidを配列に格納
        }
      }
      if (count($postSize) > $counter) { // POSTされた数がDB内の同じgoods_id数より多かった場合
        // DB内のgoods_idが同じものはPOSTされた値に更新
        foreach ($sizeMod as $key => $size) {
          $goodsMod->goodsSizeUpdate([
            'id' => $size,
            'size' => $postSize[$key],
            'delflag' => '0',
          ]);
        }
        // DB内の同じgoods_id数からPOSTされた数までサイズ追加
        for ($i = $counter; $i < count($postSize); $i++) {
          $goodsMod->goodsSizeCreate([
            'goods_id' => $id,
            'size' => $postSize[$i],
          ]);
        }
      } elseif (count($postSize) == $counter) { // POSTされた数とDB内の同じgoods_id数が同じだった場合
        // DB内のgoods_idが同じものはPOSTされた値に更新
        foreach ($sizeMod as $key => $size) {
          $goodsMod->goodsSizeUpdate([
            'id' => $size,
            'size' => $postSize[$key],
            'delflag' => '0',
          ]);
        }
      } else { // 上記に当てはまらない場合（POSTされた数がDB内の同じgoods_id数を下回った場合）
        // POSTされた値を更新
        for ($i = 0; $i < count($postSize); $i++) {
          $goodsMod->goodsSizeUpdate([
            'id' => $sizeMod[$i],
            'size' => $postSize[$i],
            'delflag' => '0',
          ]);
        }
        // POSTされた値以上のDB内の同じgoods_idのdelflagを更新
        for ($j = $i; $j <= count($sizeMod); $j++) {
          $goodsMod->goodsSizeUpdate([
            'id' => $sizeMod[$j],
            'size' => '',
            'delflag' => '1',
          ]);
        }
      }
    }

    // カラー情報更新
    if (!empty($postColor)) {
      $counter = 0;
      foreach ($goodsColors as $color) { // カラーがPOSTされていた場合
        if ($color->goods_id == $id) { // DB"goods_color"の中でPOSTされたidと同じものがあった場合
          $counter++; // DB内のgoods_idとidが同じ個数をカウント
          $colorMod[] = $color->id; // DBのidを配列に格納
        }
      }
      if (count($postColor) > $counter) { // POSTされた数がDB内の同じgoods_id数より多かった場合
        // DB内のgoods_idが同じものはPOSTされた値に更新
        foreach ($colorMod as $key => $color) {
          $goodsMod->goodsColorUpdate([
            'id' => $color,
            'color' => $postColor[$key],
            'delflag' => '0',
          ]);
        }
        // DB内の同じgoods_id数からPOSTされた数までサイズ追加
        for ($i = $counter; $i < count($postColor); $i++) {
          $goodsMod->goodsColorCreate([
            'goods_id' => $id,
            'color' => $postColor[$i],
          ]);
        }
      } elseif (count($postColor) == $counter) { // POSTされた数とDB内の同じgoods_id数が同じだった場合
        // DB内のgoods_idが同じものはPOSTされた値に更新
        foreach ($colorMod as $key => $color) {
          $goodsMod->goodsColorUpdate([
            'id' => $color,
            'color' => $postColor[$key],
            'delflag' => '0',
          ]);
        }
      } else { // 上記に当てはまらない場合（POSTされた数がDB内の同じgoods_id数を下回った場合）
        // POSTされた値を更新
        for ($i = 0; $i < count($postColor); $i++) {
          $goodsMod->goodsColorUpdate([
            'id' => $colorMod[$i],
            'color' => $postColor[$i],
            'delflag' => '0',
          ]);
        }
        // POSTされた値以上のDB内の同じgoods_idのdelflagを更新
        for ($j = $i; $j <= count($colorMod); $j++) {
          $goodsMod->goodsColorUpdate([
            'id' => $colorMod[$j],
            'color' => '',
            'delflag' => '1',
          ]);
        }
      }
    }

    // 商品画像更新
    if ($goods_img['size'][$id] > 0) {
      $ext = substr($goods_img['name'][$id], strrpos($goods_img['name'][$id], '.') + 1);
      $goods_img['name'][$id] = uniqid("img_") .'.'. $ext;
      move_uploaded_file($goods_img['tmp_name'][$id],'./image/'.$goods_img['name'][$id]);
      $_POST['image'] = $goods_img['name'][$id];
      $goodsMod->goodsUpdate([
        'id' => $id,
        'goods_name' => $_POST['goods_name'][$id],
        'price' => $_POST['price'][$id],
        'explanation' => $_POST['explanation'][$id],
        'image' => $_POST['image'],
      ]);
    } else {
      $goodsMod->goodsUpdate([
        'id' => $id,
        'goods_name' => $_POST['goods_name'][$id],
        'price' => $_POST['price'][$id],
        'explanation' => $_POST['explanation'][$id],
        'image' => $old_img,
      ]);
    }
    header('Location: '. SITE_URL . '/goods_confirm.php#goods[' .$id .']'); // 更新した商品の位置へ遷移
    exit();
  }

  // サイズ一覧更新
  protected function sizeUpdate() {
    $id = array_values(array_filter($_POST['id'], 'strlen'));
    $size = array_values(array_filter($_POST['size'], function ($k) {
      return $k !== '';
    }));
    $GoodsMod = new \Ec\Model\Goods();
    $sizeMod = $GoodsMod->sizes();
    $counter = 1;
    $sizeModCount = 0;
    foreach ($sizeMod as $key => $sizeDb) {
      if (!empty($sizeDb->size)) {
        $sizeModCount++;
      }
    }

    if (!empty($sizeMod)) {
      foreach ($sizeMod as $sizeId) {
        foreach ($size as $key => $s) {
          if ($sizeId->id == $id[$key]) {
            $GoodsMod->sizeUpdate([
              'id' => $id[$key],
              'size' => $s,
            ]);
            $counter++;
            continue;
          } elseif (count($sizeMod) == 1 && $counter >= 2) {
            if (isset($sizeId->size)) {
              $GoodsMod->sizeUpdate([
                'id' => $id[$key],
                'size' => $s,
              ]);
              $counter++;
            } else {
              $GoodsMod->sizeCreate([
                'id' => $id[$key],
                'size' => $s,
              ]);
              $counter++;
            }
          } elseif ($counter < count($sizeMod) * count($size) - (count($size) - count($sizeMod))) {
            if ($sizeModCount <= count($size)) {
              $counter++;
              continue;
            } elseif ($counter > count($size) * count($size)) {
              $GoodsMod->sizeUpdate([
                'id' => $sizeId->id,
                'size' => '',
              ]);
            } else {
              $counter++;
              continue;
            }
          } else {
            $GoodsMod->sizeCreate([
              'id' => $id[$key],
              'size' => $s,
            ]);
          }
        }
      }
     } else {
      foreach ($size as $key => $s) {
        $GoodsMod->sizeCreate([
          'id' => $id[$key],
          'size' => $s,
        ]);
      }
    }
    header('Location: '. SITE_URL . '/size.php');
    exit();
  }

  // カラー一覧更新
  protected function colorUpdate() {
    $id = array_values(array_filter($_POST['id'], 'strlen'));
    $color = array_values(array_filter($_POST['color'], function ($k) {
      return $k !== '';
    }));
    $GoodsMod = new \Ec\Model\Goods();
    $colorMod = $GoodsMod->colors();
    $counter = 1;
    $colorModCount = 0;
    foreach ($colorMod as $key => $colorDb) {
      if (!empty($colorDb->color)) {
        $colorModCount++;
      }
    }

    if (!empty($colorMod)) {
      foreach ($colorMod as $colorId) {
        foreach ($color as $key => $c) {
          if ($colorId->id == $id[$key]) {
            $GoodsMod->colorUpdate([
              'id' => $id[$key],
              'color' => $c,
            ]);
            $counter++;
            continue;
          } elseif (count($colorMod) == 1 && $counter >= 2) {
            if (isset($colorId->color)) {
              $GoodsMod->colorUpdate([
                'id' => $id[$key],
                'color' => $c,
              ]);
              $counter++;
            } else {
              $GoodsMod->colorCreate([
                'id' => $id[$key],
                'color' => $c,
              ]);
              $counter++;
            }
          } elseif ($counter < count($colorMod) * count($color) - (count($color) - count($colorMod))) {
            if ($colorModCount <= count($color)) {
              $counter++;
              continue;
            } else {
              foreach ($id as $id) {
                if ((count($color) + 1) <= $id) {
                  $GoodsMod->colorUpdate([
                    'id' => $id,
                    'color' => '',
                  ]);
                }
              }
            }
          } else {
            $GoodsMod->colorCreate([
              'id' => $id[$key],
              'color' => $c,
            ]);
          }
        }
      }
     } else {
      foreach ($color as $key => $c) {
        $GoodsMod->colorCreate([
          'id' => $id[$key],
          'color' => $c,
        ]);
      }
    }
    header('Location: '. SITE_URL . '/color.php');
    exit();
  }

  // 送料更新
  protected function setting() {
    $GoodsMod = new \Ec\Model\Goods();
    $GoodsMod->settingUpdate([
      'id' => $_POST['user_id'],
      'postage' => $_POST['postage'],
    ]);
    header('Location: '. SITE_URL . '/goods_confirm.php'); // 画面更新
    exit();
  }

}
