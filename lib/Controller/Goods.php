<?php
namespace Ec\Controller;
class Goods extends \Ec\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
      $this->addGoods();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
      $this->cart();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
      $this->delete();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change'])) {
      $this->change();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
      $this->order();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer'])) {
      $this->customer();
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

  protected function addGoods() {
    $goods_img = $_FILES['add_image'];
    $ext = substr($goods_img['name'], strrpos($goods_img['name'], '.') + 1);
    $goods_img['name'] = uniqid("img_") .'.'. $ext;
    $GoodsMod = new \Ec\Model\Goods();
    move_uploaded_file($goods_img['tmp_name'],'./image/'.$goods_img['name']);
    $_POST['image'] = $goods_img['name'];
    $goods = $GoodsMod->create([
      'goods_name' => $_POST['goods_name'],
      'price' => $_POST['goods_price'],
      'explanation' => htmlspecialchars($_POST['explanation']),
      'image' => $_POST['image'],
      'size' => $_POST['size'],
      'color' => $_POST['color'],
    ]);
  }

  protected function cart() {
    $id = $_POST['id'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    if ($_SESSION['cart'] == array()) {
      $i = 0;
      $_SESSION['cart'][$i] = $_POST;
    } else {
      $i = array_key_last($_SESSION['cart'])+1;
      $j = array_keys($_SESSION['cart']);
      $k = 0;
      while ($k <= count($j)) {
        if ($_SESSION['cart'][$k]['id'] == $id && $_SESSION['cart'][$k]['size'] == $size && $_SESSION['cart'][$k]['color'] == $color) {
          $count = $_POST['count'][0] + $_SESSION['cart'][$k]['count'][0];
          $_SESSION['cart'][$k]['count'][0] = $count;
          break;
        } elseif (empty($size) && $_SESSION['cart'][$k]['id'] == $id && $_SESSION['cart'][$k]['color'] == $color) {
          $count = $_POST['count'][0] + $_SESSION['cart'][$k]['count'][0];
          $_SESSION['cart'][$k]['count'][0] = $count;
          break;
        } else {
          $_SESSION['cart'][$i] = $_POST;
          break;
        }
      }
    }
    header('Location: ' . SITE_URL . '/shopping_all.php');
    exit();
  }

  private function delete() {
    $id = $_POST['delete'];
    $j = array_key_last($_SESSION['cart']);
    for ($k = 0; $k <= $j; $k++) {
      if ($_SESSION['cart'][$k]['id'] == $id) {
        unset($_SESSION['cart'][$k]);
        header('Location: ' . SITE_URL . '/shopping_all.php');
        exit();
        break;
      }
    }
  }

  private function change() {
    $id = $_POST['change'];
    $count = $_POST['count'][$id];
    $j = array_key_last($_SESSION['cart']);
    for ($k = 0; $k <= $j; $k++) {
      if ($_SESSION['cart'][$k]['id'] == $id) {
        $_SESSION['cart'][$k]['count'][0] = $count;
        header('Location: ' . SITE_URL . '/shopping_all.php');
        exit();
        break;
      }
    }
    $_SESSION['cart'][$id]['count'][0] = $count;
    header('Location: ' . SITE_URL . '/shopping_all.php');
    exit();
  }

  private function order() {
    $_SESSION['item_count'] = $_POST['sum_count'];
    $_SESSION['total_price'] = $_POST['total_price'];
    $_SESSION['total_tax'] = $_POST['total_tax'];
    $_SESSION['postage'] = $_POST['postage'];
    header('Location: ' . SITE_URL . '/shopping_information.php');
    exit();
  }

  protected function customer() {
    $_SESSION['mail'] = $_POST['mail'];
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['kana'] = $_POST['kana'];
    $_SESSION['post_number1'] = $_POST['post-number1'];
    $_SESSION['post_number2'] = $_POST['post-number2'];
    $_SESSION['post_number'] = $_SESSION['post_number1']. '-'. $_SESSION['post_number2'];
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['tel'] = $_POST['tel'];
    if ($_POST['credit_number']) {
      $_SESSION['credit_number'] = $_POST['credit_number'];
      $_SESSION['cvv'] = $_POST['cvv'];
      $_SESSION['period'] = $_POST['period'];
      $_SESSION['expirationDate'] = $_POST['expirationDate'];
    }
    $_SESSION['pay'] = $_POST['pay'];
    header('Location: ' . SITE_URL . '/shopping_confirm.php');
    exit();
  }

  protected function deleteConfirm() {
    $_SESSION['delete_id'] = array_keys($_POST['delete_confirm'])[0];
    header('Location: ' . SITE_URL . '/goods_delete.php');
    exit();
  }

  private function goodsDelete() {
    $GoodsMod = new \Ec\Model\goods();
    $GoodsMod->delete([
      'id' => $_POST['id'],
    ]);
    header('Location: '. SITE_URL . '/goods_confirm.php');
    exit();
  }

  protected function goodsUpdate() {
    $id = array_keys($_POST['goods_update'])[0];
    $goods_img = $_FILES['edit_image'];
    $old_img = $_POST['old_image'][$id];
    $goodsMod = new \Ec\Model\Goods();
    $goodsSizes = $goodsMod->goods_sizes();
    $goodsColors = $goodsMod->goods_colors();
    $postSize = $_POST['size'][$id];
    $postColor = $_POST['color'][$id];

    // グッズサイズ
    if (!empty($postSize)) {
      $counter = 0;
      foreach ($goodsSizes as $size) {
        if ($size->goods_id == $id) {
          $counter++;
          $sizeMod[] = $size->id;
        }
      }
      if (count($postSize) > $counter) {
        for ($i = 0; $i <= count($postSize) - $counter; $i++) {
          $goodsMod->goodsSizeCreate([
            'goods_id' => $id,
            'size' => $postSize[$i],
          ]);
        }
      } elseif (count($postSize) == $counter) {
        foreach ($sizeMod as $key => $size) {
          $goodsMod->goodsSizeUpdate([
            'id' => $size,
            'size' => $postSize[$key],
            'delflag' => '0',
          ]);
        }
      } else {
        for ($i = 0; $i < count($postSize); $i++) {
          $goodsMod->goodsSizeUpdate([
            'id' => $sizeMod[$i],
            'size' => $postSize[$i],
            'delflag' => '0',
          ]);
        }
        for ($j = $i; $j <= count($sizeMod); $j++) {
          $goodsMod->goodsSizeUpdate([
            'id' => $sizeMod[$j],
            'size' => '',
            'delflag' => '1',
          ]);
        }
      }
    }

    // グッズカラー
    if (!empty($postColor)) {
      $counter = 0;
      foreach ($goodsColors as $color) {
        if ($color->goods_id == $id) {
          $counter++;
          $colorMod[] = $color->id;
        }
      }
      if (count($postColor) > $counter) {
        for ($i = 0; $i <= count($postColor) - $counter; $i++) {
          $goodsMod->goodsColorCreate([
            'goods_id' => $id,
            'color' => $postColor[$i],
          ]);
        }
      } elseif (count($postColor) == $counter) {
        foreach ($colorMod as $key => $color) {
          $goodsMod->goodsColorUpdate([
            'id' => $color,
            'color' => $postColor[$key],
            'delflag' => '0',
          ]);
        }
      } else {
        for ($i = 0; $i < count($postColor); $i++) {
          $goodsMod->goodsColorUpdate([
            'id' => $colorMod[$i],
            'color' => $postColor[$i],
            'delflag' => '0',
          ]);
        }
        for ($j = $i; $j <= count($colorMod); $j++) {
          $goodsMod->goodsColorUpdate([
            'id' => $colorMod[$j],
            'color' => '',
            'delflag' => '1',
          ]);
        }
      }
    }

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

    header('Location: '. SITE_URL . '/goods_confirm.php#goods[' .$id .']');
    exit();
  }

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

  protected function setting() {
    $GoodsMod = new \Ec\Model\Goods();
    $GoodsMod->settingUpdate([
      'id' => $_POST['user_id'],
      'postage' => $_POST['postage'],
      'tax' => $_POST['tax'],
    ]);
    header('Location: '. SITE_URL . '/goods_confirm.php');
    exit();
  }

}
