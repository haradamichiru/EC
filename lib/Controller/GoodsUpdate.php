<?php
namespace Ec\Controller;
class GoodsUpdate extends \Ec\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
      $this->addGoods();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
      $this->updateGoods();
    }
    // 削除
    // if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) == '商品画像削除') {
    //     $this->deleteImage();
    //     header('Location: '. SITE_URL . '/mypage.php');
    //     exit();
    //   }
  }

  // protected function show() {
  //   $user = new \Ec\Model\Goods();
  //   $userData = $user->find($_SESSION['me']->id);
  //   $this->setValues('username', $userData->username);
  //   $this->setValues('email', $userData->email);
  //   $this->setValues('image', $userData->image);
  // }

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

  protected function UpdateGoods() {
    // try {
    //   $this->validate();
    // } catch (\Ec\Exception\invalidEmail $e) {
    //   $this->setErrors('email', $e->getMessage());
    // } catch (\Ec\Exception\InvalidName $e) {
    //   $this->setErrors('username', $e->getMessage());
    // }
    // $this->setValues('username', $_POST['username']);
    // $this->setValues('email', $_POST['email']);
    // if ($this->hasError()) {
    //   return;
    // } else {
      // $goods_img = $_FILES['image'];
      // $old_img = $_POST['old_image'];
      // $ext = substr($user_img['name'], strrpos($user_img['name'], '.') + 1);
      // $user_img['name'] = uniqid("img_") .'.'. $ext;
      // if ($old_img == "") {
      //   $old_img = NULL;
      //   // $_SESSION['me']->image = NULL;
      // }
      // try {
      //   $goodsModel = new \Ec\Model\Goods();
      //   if ($goods_img['size'] > 0) {
      //     unlink('./gazou/'.$old_img);
      //     move_uploaded_file($goods_img['tmp_name'],'./gazou/'.$goods_img['name']);
      //     $goodsModel->update([
      //       'username' => $_POST['username'],
      //       'email' => $_POST['email'],
      //       'userimg' => $user_img['name']
      //     ]);
      //     $_SESSION['me']->image = $goods_img['name'];
      //   } else {
      //     $goodsModel->update([
      //       'username' => $_POST['username'],
      //       'email' => $_POST['email'],
      //       'userimg' => $old_img
      //     ]);
      //   }
      // }
      // catch (\Bbs\Exception\DuplicateEmail $e) {
      //   $this->setErrors('email', $e->getMessage());
      //   return;
      // }
    // }
  //   $_SESSION['me']->username = $_POST['username'];
  //   header('Location: '. SITE_URL . '/mypage.php');
  //   exit();
  }

  // private function validate() {
  //   if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
  //     echo "不正なトークンです!";
  //     exit();
  //   }
  //   if (!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
  //     throw new \Bbs\Exception\InvalidEmail("メールアドレスが不正です!");
  //   }
  //   if ($_POST['username'] === '') {
  //     throw new \Bbs\Exception\InvalidName("ユーザー名が入力されていません!");
  //   }
  // }

  // public function deleteImage() {
  //     $delete_img = $_SESSION['me']->image;
  //     unlink('./gazou/'.$delete_img);
  //     // var_dump($delete_img);
  //     // exit();
  //     $adminMod = new \Bbs\Model\AdminUser();
  //     $adminMod->deleteImage();
  // }

}