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
    $OrderMod = new \Ec\Model\Order();
    $order = $OrderMod->order([
      'number' => $_POST['number'],
      'email' => $_SESSION['mail'],
      'name' => $_SESSION['name'],
      'kana' => $_SESSION['kana'],
      'address' => $_SESSION['address'],
      'tel' => $_SESSION['tel'],
      'pay' => $_SESSION['pay'],
      'items' => $_SESSION['cart'],
      'postage' => $_POST['postage'],
      'tax' => $_POST['tax_rate'],
    ]);
    $_SESSION = array();
    $_SESSION['number'] = $_POST['number'];
    header('Location: ' . SITE_URL . '/shopping_completed.php');
    exit();
  }

  protected function UpdateOrders() {
    $key = array_keys($_POST['update'])['0'];
    $errorMessages = [];
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    }
    if (!empty($errorMessages)) {
      $this->setErrors('name', $errorMessages['name']);
      $this->setErrors('kana', $errorMessages['kana']);
      $this->setErrors('email', $errorMessages['email']);
      $this->setErrors('address', $errorMessages['address']);
      $this->setErrors('tel', $errorMessages['tel']);
      $this->setErrors('pay', $errorMessages['pay']);
      $this->setValues('id', $key);
      $this->setValues('name', $_POST['name'][$key]);
      $this->setValues('kana', $_POST['kana'][$key]);
      $this->setValues('email', $_POST['email'][$key]);
      $this->setValues('address', $_POST['address'][$key]);
      $this->setValues('telN', $_POST['tel'][$key]);
      $this->setValues('pay', $_POST['pay'][$key]);
      return $errorMessages;
    } else {
      $OrderMod = new \Ec\Model\order();
      $GoodsMod = new \Ec\Model\Goods();
      $goods_data = $GoodsMod->goods();
      for ($i = 0; $i <= count(array_keys($_POST['goods'][$key])); $i++){
        foreach($goods_data as $goods):
          if ($_POST['goods'][$key][$i]['id'] == $goods->id) {
            $_POST['goods'][$key][$i]['price'] = $goods->price;
          }
        endforeach;
          if (empty($_POST['goods'][$key][$i]['id'])) {
            unset($_POST['goods'][$key][$i]);
          } elseif (empty($_POST['goods'][$key][$i]['color'])) {
            unset($_POST['goods'][$key][$i]['color']);
          } elseif (is_array($_POST['goods'][$key][$i]['color'])) {
            $_POST['goods'][$key][$i]['color'] = array_shift(array_values(array_filter($_POST['goods'][$key][$i]['color'])));
          }
          if (empty($_POST['goods'][$key][$i]['id'])) {
            unset($_POST['goods'][$key][$i]);
          } elseif (empty($_POST['goods'][$key][$i]['size'])) {
            unset($_POST['goods'][$key][$i]['size']);
          } elseif (is_array($_POST['goods'][$key][$i]['size'])) {
            $_POST['goods'][$key][$i]['size'] = array_shift(array_values(array_filter($_POST['goods'][$key][$i]['size'])));
          }
      }
      $OrderMod->update([
        'number' => $_POST['number'][$key],
        'name' => $_POST['name'][$key],
        'kana' => $_POST['kana'][$key],
        'address' => $_POST['address'][$key],
        'email' => $_POST['email'][$key],
        'tel' => $_POST['tel'][$key],
        'pay' => $_POST['pay'][$key],
        'goods' => $_POST['goods'][$key],
        'status' => $_POST['status'][$key],
      ]);
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