<?php
namespace Ec\Controller;
class Goods extends \Ec\Controller {
  public function run() {
    $action = [
      'add' => 'addGoods',
      'add_cart' => 'cart',
      'order' => 'customer',
      'delete' => 'cartDelete',
      'change' => 'cartChange',
      'delete_confirm' => 'deleteConfirm',
      'goods_delete' => 'goodsDelete',
      'goods_update' => 'goodsUpdate',
      'size_update' => 'sizeUpdate',
      'color_update' => 'colorUpdate',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      foreach ($action as $key => $method) {
        if (isset($_POST[$key])) {
          // var_dump($method);
          // exit();
          $this->$method();
          return;
        }
      }
      return;
    }
  }

  // 商品追加
  protected function addGoods() {
    // 画像データをフォルダに格納して$_POST['image']として保存
    $goods_img = $_FILES['add_image'];
    if ($goods_img['size'] > 0) {
      $ext = substr($goods_img['name'], strrpos($goods_img['name'], '.') + 1);
      $goods_img['name'] = uniqid("img_") .'.'. $ext;
      move_uploaded_file($goods_img['tmp_name'],'./image/'.$goods_img['name']);
      $_POST['image'] = $goods_img['name'];
    } else {
      $_POST['image'] = "";
    }
    
    $GoodsMod = new \Ec\Model\Goods();
    $GoodsMod->create([
      'goods_name' => $_POST['new_goods_name'],
      'price' => $_POST['new_goods_price'],
      'explanation' => htmlspecialchars($_POST['new_explanation']),
      'image' => $_POST['image'],
    ]);
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
    $session_goods_id = $_SESSION['cart'][$_POST['id']];
    $post_goods_id = $_POST['id'];
    if (isset($session_goods_id)) { // $_SESSION['cart']の中POSTされた商品と同じIDがあった場合
      foreach ($session_goods_id as $key => $detail) {
        if ($session_goods_id[$key]['size'] == $_POST[$post_goods_id]['size']) { // $_SESSION['cart']のサイズとPOSTされたサイズが同じ場合
          if ($session_goods_id[$key]['color'] == $_POST[$post_goods_id]['color']) { // $_SESSION['cart']のカラーとPOSTされたカラーが同じ場合（同一商品の場合）
            $_SESSION['cart'][$_POST['id']][$key]['count'] = $_POST[$post_goods_id]['count'] + $session_goods_id[$key]['count']; // 商品数を増やす
          } else { // $_SESSION['cart']のサイズとPOSTされたサイズが同じでカラーが異なる場合
            $_SESSION['cart'][$_POST['id']][] = $_POST[$_POST['id']]; //　別の商品として$_SESSION['cart']へ格納
          }
        } else {
          $_SESSION['cart'][$_POST['id']][] = $_POST[$_POST['id']];
        }
      }
    } else {
      $_SESSION['cart'][$_POST['id']][] = $_POST[$_POST['id']];
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
  private function cartDelete() {
    if (count($_SESSION['cart'][$_POST['delete']]) == 1) {
      unset($_SESSION['cart'][$_POST['delete']]);
    } else {
      unset($_SESSION['cart'][$_POST['delete']][$_POST['key']]);
    }
  }

  // ショッピングカートの商品数量変更
  private function cartChange() {
    $goods_id = $_POST['change'];
    $_SESSION['cart'][$goods_id][$_POST['key']]['count'] = $_POST['count'][$_POST['key']];
    header('Location: ' . SITE_URL . '/shopping_all.php'); // 画面更新
    exit();
  }

  // 商品削除画面へ遷移
  protected function deleteConfirm() {
    $_SESSION['delete_goods_id'] = $_POST['id']; // 押下した削除ボタンのキーを取得（IDを取得）
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
    $id = $_POST['id']; // POSTされたID情報を$idとして変数格納
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    }
    if (!empty($errorMessages)) { // $errorMessagesがあった場合
      $this->setErrors('goods_name', $errorMessages['goods_name']);
      $this->setErrors('price', $errorMessages['price']);
      $this->setValues('id', $_POST['id']);
      $this->setValues('goods_name', $_POST['goods_name']);
      $this->setValues('price', $_POST['price']);
      $this->setValues('explanation', $_POST['explanation']);
      return $errorMessages;
    } else {
      $goods_img = $_FILES['edit_image'];
      $goodsMod = new \Ec\Model\Goods();
      $goodsSizes = $goodsMod->goods_sizes();
      $goodsColors = $goodsMod->goods_colors();
      $postSize = $_POST['size']; // POSTされたサイズを配列として格納
      $postColor = $_POST['color']; // POSTされたカラーを配列として格納

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

      // 更新するデータを配列に格納
      $goods_update_data = [
        'id' => $id,
        'goods_name' => $_POST['goods_name'],
        'price' => $_POST['price'],
        'explanation' => $_POST['explanation'],
      ];
      // 商品画像更新
      if ($goods_img['size'] > 0) {
        $ext = substr($goods_img['name'], strrpos($goods_img['name'], '.') + 1);
        $goods_img['name'] = uniqid("img_") .'.'. $ext;
        move_uploaded_file($goods_img['tmp_name'],'./image/'.$goods_img['name']);
        $goods_update_data['image'] = $goods_img['name'];
        $goodsMod->goodsUpdate($goods_update_data);
      } else {
        $goods_update_data['image'] = $_POST['image'];
        $goodsMod->goodsUpdate($goods_update_data);
      }
    }
    header('Location: '. SITE_URL . '/goods_confirm.php#goods[' .$id .']'); // 更新した商品の位置へ遷移
    exit();
  }

  // 商品更新バリデーション
  private function validate() {
    if (isset($_POST['goods_update'])) {
      if ($_POST['goods_name'] === '') {
        $errors['goods_name'] = "商品名が入力されていません。";
      }
      if ($_POST['price'] === '') {
        $errors['price'] = "商品金額が入力されていません。";
      }
      if (!empty($errors)) {
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
      }
    }
  }

  // 商品更新時のバリデーション時のエラーメッセージ
  public function errorText() {
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    }
    if (!empty($errorMessages)) { // $errorMessagesがあった場合
      return $errorMessages;
    }
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
    // DBに保存されているサイズの個数をカウント
    foreach ($sizeMod as $key => $sizeDb) {
      if (!empty($sizeDb->size)) { // 空白は除く
        $sizeModCount++;
      }
    }

    if (!empty($sizeMod)) { // DBにサイズの保存がある場合
      foreach ($sizeMod as $sizeId) {
        foreach ($size as $key => $s) {
          if ($sizeId->id == $id[$key]) { // DBに保存してあるID中にPOSTされたIDがあった場合
            // サイズを更新
            $GoodsMod->sizeUpdate([
              'id' => $id[$key],
              'size' => $s,
            ]);
            $counter++;
            continue;
          } elseif (count($sizeMod) == 1 && $counter >= 2) { // DB内の空白を含むサイズ数が1つのみかつカウンターが2以上の場合
            if (isset($sizeId->size)) { // DB内にサイズがある場合
              // サイズを更新
              $GoodsMod->sizeUpdate([
                'id' => $id[$key],
                'size' => $s,
              ]);
              $counter++;
            } else { // DB内にサイズがない場合
              // サイズを新規作成
              $GoodsMod->sizeCreate([
                'id' => $id[$key],
                'size' => $s,
              ]);
              $counter++;
            }
          } elseif ($counter < count($sizeMod) * count($size) - (count($size) - count($sizeMod))) { // カウンター数がすべての組み合わせ数-POSTされたサイズとDB内の空白含むサイズ数の差よりも小さかった場合
            if ($sizeModCount <= count($size)) { // POSTされたサイズ数がDBのサイズ数以上の場合
              $counter++;
              continue;
            } elseif ($counter > count($size) * count($size)) { // カウンターがPOSTされたサイズ数での組み合わせ数より大きかった場合
              // サイズを更新
              $GoodsMod->sizeUpdate([
                'id' => $sizeId->id,
                'size' => '',
              ]);
            } else {
              $counter++;
              continue;
            }
          } else {
            // サイズを新規作成
            $GoodsMod->sizeCreate([
              'id' => $id[$key],
              'size' => $s,
            ]);
          }
        }
      }
     } else { // DBに情報がない場合
      foreach ($size as $key => $s) {
        // サイズを新規作成
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
  protected function colorUpdate() { // DBにカラーの保存がある場合
    $id = array_values(array_filter($_POST['id'], 'strlen'));
    $color = array_values(array_filter($_POST['color'], function ($k) {
      return $k !== '';
    }));
    $GoodsMod = new \Ec\Model\Goods();
    $colorMod = $GoodsMod->colors();
    $counter = 1;
    $colorModCount = 0;
    // DBに保存されているカラーの個数をカウント
    foreach ($colorMod as $key => $colorDb) { // DBに保存してあるID中にPOSTされたIDがあった場合
      if (!empty($colorDb->color)) { // 空白は除く
        $colorModCount++;
      }
    }

    if (!empty($colorMod)) { // DBにカラーの保存がある場合
      foreach ($colorMod as $colorId) {
        foreach ($color as $key => $c) {
          if ($colorId->id == $id[$key]) { // DBに保存してあるID中にPOSTされたIDがあった場合
            // カラーを更新
            $GoodsMod->colorUpdate([
              'id' => $id[$key],
              'color' => $c,
            ]);
            $counter++;
            continue;
          } elseif (count($colorMod) == 1 && $counter >= 2) { // DB内の空白を含むカラー数が1つのみかつカウンターが2以上の場合
            if (isset($colorId->color)) { // DB内にカラーがある場合
              // カラーを更新
              $GoodsMod->colorUpdate([
                'id' => $id[$key],
                'color' => $c,
              ]);
              $counter++;
            } else { // DB内にカラーがない場合
              // カラーを新規作成
              $GoodsMod->colorCreate([
                'id' => $id[$key],
                'color' => $c,
              ]);
              $counter++;
            }
          } elseif ($counter < count($colorMod) * count($color) - (count($color) - count($colorMod))) { // カウンター数がすべての組み合わせ数-POSTされたカラーとDB内の空白含むカラー数の差よりも小さかった場合
            if ($colorModCount <= count($color)) { // POSTされたカラー数がDBのサイズ数以上の場合
              $counter++;
              continue;
            } else {
              foreach ($id as $id) {
                if ((count($color) + 1) <= $id) {
                  // カラーを更新
                  $GoodsMod->colorUpdate([
                    'id' => $id,
                    'color' => '',
                  ]);
                }
              }
            }
          } else {
            // カラーを新規作成
            $GoodsMod->colorCreate([
              'id' => $id[$key],
              'color' => $c,
            ]);
          }
        }
      }
     } else { // DBに情報がない場合
      foreach ($color as $key => $c) {
        // カラーを新規作成
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
