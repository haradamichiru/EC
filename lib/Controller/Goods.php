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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setting']) ) {
      $this->setting();
    }

  }

  protected function addGoods() {
    $goods_img = $_FILES['add_image'];
    $ext = substr($goods_img['name'], strrpos($goods_img['name'], '.') + 1);
    $goods_img['name'] = uniqid("img_") .'.'. $ext;
    $GoodsMod = new \Ec\Model\Goods();
    move_uploaded_file($goods_img['tmp_name'],'./gazou/'.$goods_img['name']);
    $_POST['image'] = $goods_img['name'];
    $goods = $GoodsMod->create([
      'goods_name' => $_POST['goods_name'],
      'price' => $_POST['goods_price'],
      'explanation' => $_POST['explanation'],
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
    $_SESSION['post_number'] = $_POST['post-number-1']. '-'. $_POST['post-number-2'];
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
    $GoodsMod = new \Ec\Model\Goods();
    if ($goods_img['size'][$id] > 0) {
      $ext = substr($goods_img['name'][$id], strrpos($goods_img['name'][$id], '.') + 1);
      $goods_img['name'][$id] = uniqid("img_") .'.'. $ext;
      move_uploaded_file($goods_img['tmp_name'][$id],'./gazou/'.$goods_img['name'][$id]);
      $_POST['image'] = $goods_img['name'][$id];
      $GoodsMod->goodsUpdate([
        'id' => $id,
        'goods_name' => $_POST['goods_name'][$id],
        'price' => $_POST['price'][$id],
        'explanation' => $_POST['explanation'][$id],
        'image' => $_POST['image'],
        'size' => $_POST['size'][$id],
        'color' => $_POST['color'][$id],
      ]);
    } else {
      $GoodsMod->goodsUpdate([
        'id' => $id,
        'goods_name' => $_POST['goods_name'][$id],
        'price' => $_POST['price'][$id],
        'explanation' => $_POST['explanation'][$id],
        'image' => $old_img,
        'size' => $_POST['size'][$id],
        'color' => $_POST['color'][$id],
      ]);
    }
    header('Location: '. SITE_URL . '/goods_confirm.php');
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
