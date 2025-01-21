<?php
namespace Ec\Controller;
class Order extends \Ec\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete'])) {
      $this->newOrder();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
      $this->updateOrders();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
      $this->cancel();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
      $this->delete();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
      $orderData = $this->search();
      return $orderData;
    }

  }

  protected function newOrder() {
    foreach ($_SESSION['cart'] as $goods) {
      $goods_id[] = $goods['id'];
      $price[] = $goods['price'];
      $count[] = $goods['count'][0];
      $size[] = $goods['size'];
      $color[] = $goods['color'];
    }
    $OrderMod = new \Ec\Model\Order();
    $order = $OrderMod->order([
      'number' => $_POST['number'],
      'postage' => $_POST['postage'],
      'tax' => $_POST['tax_rate'],
      'name' => $_SESSION['name'],
      'kana' => $_SESSION['kana'],
      'email' => $_SESSION['mail'],
      'postNum' => $_SESSION['post_number'],
      'address' => $_SESSION['address'],
      'tel' => $_SESSION['tel'],
      'pay' => $_SESSION['pay'],
    ]);

    foreach ($goods_id as $key => $gId) {
      $OrderMod->orderGoods([
        'order_number' => $_POST['number'],
        'goods_id' => $gId,
        'price' => $price[$key],
        'count' => $count[$key],
        'size' => $size[$key],
        'color' => $color[$key],
      ]);
    }
    $_SESSION = array();
    $_SESSION['number'] = $_POST['number'];
    header('Location: ' . SITE_URL . '/shopping_completed.php');
    exit();
  }

  protected function UpdateOrders() {
    $key = array_keys($_POST['update'])['0'];
    $errorMessages = [];
    // var_dump($_POST['postNum'][$key]);
    // exit();
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    }
    if (!empty($errorMessages)) {
      $this->setErrors('name', $errorMessages['name']);
      $this->setErrors('kana', $errorMessages['kana']);
      $this->setErrors('email', $errorMessages['email']);
      $this->setErrors('postNum', $errorMessages['postNum']);
      $this->setErrors('address', $errorMessages['address']);
      $this->setErrors('tel', $errorMessages['tel']);
      $this->setErrors('pay', $errorMessages['pay']);
      $this->setValues('id', $key);
      $this->setValues('name', $_POST['name'][$key]);
      $this->setValues('kana', $_POST['kana'][$key]);
      $this->setValues('email', $_POST['email'][$key]);
      $this->setValues('postNum', $_POST['postNum'][$key]);
      $this->setValues('address', $_POST['address'][$key]);
      $this->setValues('telN', $_POST['tel'][$key]);
      $this->setValues('pay', $_POST['pay'][$key]);
      return $errorMessages;
    } else {
      $OrderMod = new \Ec\Model\Order();
      $GoodsMod = new \Ec\Model\Goods();
      $goods_data = $GoodsMod->goods();
      $order_goods = $OrderMod->orders();
      foreach ($goods_data as $goods) {
        foreach ($_POST['id'][$key] as $post_id) {
          if ($post_id == $goods->id) {
            $price[] = $goods->price;
          }
        }
      }
      // var_dump($order_goods);
      // for ($i = 0; $i <= count(array_keys($_POST['goods'][$key])); $i++){
      //   foreach($goods_data as $goods):
      //     if ($_POST['goods'][$key][$i]['id'] == $goods->id) {
      //       $_POST['goods'][$key][$i]['price'] = $goods->price;
      //     }
      //   endforeach;
      //     if (empty($_POST['goods'][$key][$i]['id'])) {
      //       unset($_POST['goods'][$key][$i]);
      //     } elseif (empty($_POST['goods'][$key][$i]['color'])) {
      //       unset($_POST['goods'][$key][$i]['color']);
      //     } elseif (is_array($_POST['goods'][$key][$i]['color'])) {
      //       $_POST['goods'][$key][$i]['color'] = array_shift(array_values(array_filter($_POST['goods'][$key][$i]['color'])));
      //     }
      //     if (empty($_POST['goods'][$key][$i]['id'])) {
      //       unset($_POST['goods'][$key][$i]);
      //     } elseif (empty($_POST['goods'][$key][$i]['size'])) {
      //       unset($_POST['goods'][$key][$i]['size']);
      //     } elseif (is_array($_POST['goods'][$key][$i]['size'])) {
      //       $_POST['goods'][$key][$i]['size'] = array_shift(array_values(array_filter($_POST['goods'][$key][$i]['size'])));
      //     }
      // }
      $OrderMod->update([
        'number' => $_POST['number'][$key],
        'status' => $_POST['status'][$key],
        'email' => $_POST['email'][$key],
        'name' => $_POST['name'][$key],
        'kana' => $_POST['kana'][$key],
        'postNum' => $_POST['postNum'][$key],
        'address' => $_POST['address'][$key],
        'tel' => $_POST['tel'][$key],
        'pay' => $_POST['pay'][$key],
      ]);

      // var_dump(count($_POST['id'][$key]));
      // exit();
      foreach ($order_goods as $orders) {
        if ($orders->order_number == $_POST['number'][$key]) {
          $same_number[] = $orders;
        }
      }
      $counter = 0;
      // var_dump($same_number);
      foreach ($same_number as $sn) {
        for ($i = 0; $i < count($price); $i++) {
            // var_dump($counter);
          if ($counter == 0) {
            // var_dump('0');
            // $OrderMod->orderGoodsUpdate([
            //   'number' => $_POST['number'][$key],
            //   'goods_id' => $_POST['id'][$key][$counter],
            //   'price' => $price[$counter],
            //   'count' => $_POST['count'][$key][$counter],
            //   'size' => $_POST['size'][$key][$counter],
            //   'color' => $_POST['color'][$key][$counter],
            // ]);
            $counter++;
            break;
          }
          
          // 2回目以上のループ($i)
          if ($i >= 1 && $counter == $i) {
            // $OrderMod->orderGoodsUpdate([
            //   'number' => $_POST['number'][$key],
            //   'goods_id' => $_POST['id'][$key][$counter],
            //   'price' => $price[$counter],
            //   'count' => $_POST['count'][$key][$counter],
            //   'size' => $_POST['size'][$key][$counter],
            //   'color' => $_POST['color'][$key][$counter],
            // ]);
            $counter++;
            // $j = $i +1;
            // continue;
          } elseif (count($same_number) < count($price) && $i == count($same_number)) {
              // $OrderMod->orderGoodsCreate([
              //   'number' => $_POST['number'][$key],
              //   'goods_id' => $_POST['id'][$key][$counter-1],
              //   'price' => $price[$counter-1],
              //   'count' => $_POST['count'][$key][$counter-1],
              //   'size' => $_POST['size'][$key][$counter-1],
              //   'color' => $_POST['color'][$key][$counter-1],
              // ]);
            $counter++;
            // $counter++;
            // continue;
            // if ($i )
          }
        }
      }
      // var_dump($i);
      // var_dump($counter);

      exit();
      // foreach ($_POST['id'][$key] as $i => $id) {
      //   foreach ($order_goods as $orders) {
      //   }
      // }

      header('Location: '. SITE_URL . '/order_confirm.php');
      exit();
    }
  }

  private function cancel() {
    $key = array_keys($_POST['cancel'])['0'];
    $_SESSION['number'] = $_POST['number'][$key];
    header('Location: ' . SITE_URL . '/cancel.php');
    exit();
  }

  private function delete() {
    $OrderMod = new \Ec\Model\order();
    $OrderMod->delete([
      'number' => $_SESSION['number'],
    ]);
    $_SESSION = array();
    header('Location: ' . SITE_URL . '/order_confirm.php');
    exit();
  }

  public function search() {
    if (isset($_GET['number'])) {
      $number = $_GET['number'];
    }
    if (isset($_GET['username'])) {
      $username = $_GET['username'];
    }
    if (isset($_GET['tel'])) {
      $tel = $_GET['tel'];
    }
    if (isset($_GET['status'])) {
      $status = $_GET['status'];
    } else {
      $status = 0;
    }
    $this->setValues('number', $number);
    $this->setValues('username', $username);
    $this->setValues('tel', $tel);
    $this->setValues('status', $status);
    $OrderMod = new \Ec\Model\order();
    $orderData = $OrderMod->searchOrder([
      'number' => $number,
      'username' => $username,
      'tel' => $tel,
      'status' => $status,
    ]);
    return $orderData;
  }

  private function validate() {
    if (isset($_POST['update'])) {
      $key = array_keys($_POST['update'])['0'];
      if ($_POST['name'][$key] === '') {
        $errors['name'] = "ご注文者名が入力されていません。";
      }
      if ($_POST['kana'][$key] === '') {
        $errors['kana'] = "ご注文者名カナが入力されていません。";
      }
      if ($_POST['postNum'][$key] === '') {
        $errors['postNum'] = "郵便番号が入力されていません。";
      }
      if ($_POST['address'][$key] === '') {
        $errors['address'] = "住所が入力されていません。";
      }
      if ($_POST['email'][$key] === '') {
        $errors['email'] = "メールアドレスが入力されていません。";
      } elseif (!filter_var($_POST['email'][$key],FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "メールアドレスが不正です。";
      }
      if ($_POST['tel'][$key] === '') {
        $errors['tel'] = "電話番号が入力されていません。";
      } elseif (!preg_match('/^[0-9]{2,4}[0-9]{2,4}[0-9]{3,4}$/', $_POST['tel'][$key])) {
        $errors['tel'] = "電話番号が不正です。ハイフンなしで入力してください。";
      }
      if ($_POST['pay'][$key] === 'credit') {
        $errors['pay'] = "クレジットへの変更はできません。";
      }

    //   if ($_POST['username'][$id] === '') {
    //     $errors['username'] = "ユーザー名が入力されていません!";
    //   }
    //   if (!($_POST['authority'][$id] == "1" || $_POST['authority'][$id] == "99")) {
    //     $errors['authority'] = "権限は「1=一般ユーザー」か「99=管理者」を入力してください！";
    //   }
    //   if (!($_POST['delflag'][$id] == "1" || $_POST['delflag'][$id] == "0")) {
    //     $errors['delflag'] = "削除フラグは0か1を入力してください！（1は削除されたユーザーになります）";
    //   }
    //   if (!empty($errors)) {
    //     throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
    //   }
    // }
    // if (isset($_POST['password'])) {
    //   if (!isset($_POST['email']) || !isset($_POST['username']) || !isset($_POST['password'])) {
    //     echo "不正なフォームから登録されています!";
    //     exit();
    //   }
    //   if ($_POST['username'] === '') {
    //     $errors['username'] = "ユーザー名が入力されていません!";
    //   }
    //   if (!preg_match('/\A[a-zA-Z0-9]+\z/', $_POST['password'])) {
    //     $errors['password'] = "パスワードが不正です!";
    //   }
    //   if (!($_POST['authority'] == "1" || $_POST['authority'] == "99")) {
    //     $errors['authority'] = "権限は「1=一般ユーザー」か「99=管理者」を入力してください！";
    //   }
    //   if (!($_POST['delflag'] == "1" || $_POST['delflag'] == "0")) {
    //     $errors['delflag'] = "削除フラグは0か1を入力してください！（1は削除されたユーザーになります）";
    //   }
    //   if (!filter_var($_POST['email'][$id],FILTER_VALIDATE_EMAIL)) {
    //     $errors['email'] = "メールアドレスが不正です!";
    //   }
      if (!empty($errors)) {
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
      }
    }
  }

}
            // if ($counter >= 1 && $counter <= $i) {
            //   // var_dump('jump');
            //   // var_dump($i);
            //   // var_dump($counter);

            //   continue;
            // }
            // var_dump($_POST['size'][$key][$counter]);
            // var_dump($counter);

            // if ($i == $counter) {
            //   // var_dump($i);
            //   // var_dump($counter);
            //   var_dump('upd');
            //   // $OrderMod->orderGoodsUpdate([
            //   //   'number' => $_POST['number'][$key],
            //   //   'goods_id' => $_POST['id'][$key][$counter],
            //   //   'price' => $price[$counter],
            //   //   'count' => $_POST['count'][$key][$counter],
            //   //   'size' => $_POST['size'][$key][$counter],
            //   //   'color' => $_POST['color'][$key][$counter],
            //   // ]);
            //   $counter++;
            //   continue;
            // } elseif ($counter < $i) {
            //   var_dump('add');
            //   // var_dump($_POST['size'][$key]);
            //   // var_dump($_POST['size'][$key][$counter-1]);

            //   // exit();
            //   // $OrderMod->orderGoodsCreate([
            //   //   'number' => $_POST['number'][$key],
            //   //   'goods_id' => $_POST['id'][$key][$counter-1],
            //   //   'price' => $price[$counter-1],
            //   //   'count' => $_POST['count'][$key][$counter-1],
            //   //   'size' => $_POST['size'][$key][$counter-1],
            //   //   'color' => $_POST['color'][$key][$counter-1],
            //   // ]);
            // }
