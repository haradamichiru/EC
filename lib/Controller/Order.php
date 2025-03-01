<?php
namespace Ec\Controller;
class Order extends \Ec\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy'])) {
      $this->newOrder(); // 新規注文
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
      $this->updateOrders(); // 注文更新
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
      $this->cancel(); // 注文削除画面への遷移
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
      $this->delete(); // 注文削除
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
      $orderData = $this->search();
      return $orderData; // 注文検索
    }
  }

  // 注文者情報POSTデータ格納
  private function orders() {
    if ($_POST['post-number1']) { // 新規注文の場合
      $orders = [
        'number' => $_POST['number'],
        'postage' => $_POST['postage'],
        'tax_rate' => $_POST['tax_rate'] * 100,
        'status' => $_POST['status'],
        'email' => $_POST['email'],
        'name' => $_POST['name'],
        'kana' => $_POST['kana'],
        'post-number' => $_POST['post-number1']. '-' .$_POST['post-number2'],
        'address' => $_POST['address'],
        'tel' => $_POST['tel'],
        'pay' => $_POST['pay'],
      ];
    } elseif (isset($_POST['update'])) { // 注文更新の場合
      $key = array_keys($_POST['update'])['0']; // POSTされた商品を特定するためのキーを変数格納
      $orders = [
        'number' => $_POST['number'][$key],
        'status' => $_POST['status'][$key],
        'email' => $_POST['email'][$key],
        'name' => $_POST['name'][$key],
        'kana' => $_POST['kana'][$key],
        'post-number' => $_POST['post-number'][$key],
        'address' => $_POST['address'][$key],
        'tel' => $_POST['tel'][$key],
        'pay' => $_POST['pay'][$key],
      ];
    }
    return $orders;
  }

  // 注文商品更新データ格納
  private function orderGoodsUpdate() {
    $key = array_keys($_POST['update'])['0']; // POSTされた商品を特定するためのキーを変数格納
    $orderGoodsUpdate = [
      'count' => $_POST['count'][$key],
      'size' => $_POST['size'][$key],
      'color' => $_POST['color'][$key],
    ];
    return $orderGoodsUpdate;
  }

  // 新規注文
  protected function newOrder() {
    // セッション「カート」を項目ごとに格納
    foreach ($_SESSION['cart'] as $goods) {
      $goods_id[] = $goods['id'];
      $price[] = $goods['price'];
      $count[] = $goods['count'][0];
      $size[] = $goods['size'];
      $color[] = $goods['color'];
    }
    // 注文者情報をordersテーブルに新規追加
    $orderMod = new \Ec\Model\Order();
    $orderMod->order($this->orders());
    // カートに入っていた商品をorder_goodsテーブルに新規追加
    foreach ($goods_id as $key => $gId) {
      $orderMod->orderGoods([
        'order_number' => $_POST['number'],
        'goods_id' => $gId,
        'price' => $price[$key],
        'count' => $count[$key],
        'size' => $size[$key],
        'color' => $color[$key],
      ]);
    }
    $_SESSION = array(); // セッション削除
    $_SESSION['number'] = $_POST['number']; // 注文番号をセッションに格納
    header('Location: ' . SITE_URL . '/shopping_completed.php'); // 注文完了画面へ遷移
    exit();
  }

  // 注文更新
  protected function UpdateOrders() {
    $key = array_keys($_POST['update'])['0']; // POSTされた商品を特定するためのキーを変数格納
    $errorMessages = []; //　エラーメッセージをリセット
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    }
    if (!empty($errorMessages)) { // $errorMessagesがあった場合
      $this->setErrors('name', $errorMessages['name']);
      $this->setErrors('kana', $errorMessages['kana']);
      $this->setErrors('email', $errorMessages['email']);
      $this->setErrors('postNum', $errorMessages['postNum']);
      $this->setErrors('address', $errorMessages['address']);
      $this->setErrors('tel', $errorMessages['tel']);
      $this->setErrors('pay', $errorMessages['pay']);
      $this->setErrors('goods', $errorMessages['goods']);
      $this->setValues('id', $key);
      $this->setValues('name', $_POST['name'][$key]);
      $this->setValues('kana', $_POST['kana'][$key]);
      $this->setValues('email', $_POST['email'][$key]);
      $this->setValues('postNum', $_POST['postNum'][$key]);
      $this->setValues('address', $_POST['address'][$key]);
      $this->setValues('telN', $_POST['tel'][$key]);
      $this->setValues('pay', $_POST['pay'][$key]);
      $this->setValues('goods', $_POST['goods_id'][$key]);
      return $errorMessages;
    } else {
      $OrderMod = new \Ec\Model\Order();
      $GoodsMod = new \Ec\Model\Goods();
      $goods_data = $GoodsMod->goods(); // DB"goods"を変数格納
      $order_goods = $OrderMod->ordersGoods(); // DB"order_goods"を変数格納

      // 注文者情報更新
      $OrderMod->update($this->orders());

      // DB"goods"内でPOSTされた商品IDと同じ商品IDの金額を配列として格納
      foreach ($goods_data as $goods) {
        foreach ($_POST['goods_id'][$key] as $post_id) {
          if ($post_id == $goods->id) {
            $price[] = $goods->price;
          }
        }
      }

      // DB"order_goods"内でとPOSTされた注文番号が同じIDと商品IDを配列として格納
      foreach ($order_goods as $orders) {
        if ($orders->order_number == $_POST['number'][$key]) {
          $ordersGoodsId[] = $orders->id;
          $goodsId[] = $orders->goods_id;
        }
      }

      if (count($ordersGoodsId) >= count(array_filter($_POST['goods_id'][$key]))) { // DBとPOSTの注文番号一致数がPOSTされた数以上の場合
        foreach (array_filter($_POST['goods_id'][$key]) as $i => $orderId) { // POSTされた商品数までループ
          $OrderMod->orderGoodsUpdate([
            'id' => $ordersGoodsId[$i],
            'key' => $i,
            'goods_id' => $orderId,
            $this->orderGoodsUpdate(),
          ]);
        }
        foreach ($ordersGoodsId as $i => $orderId) { // DB内とPOSTされた注文番号が一致する数までループ
          if ($i >= count(array_filter($_POST['goods_id'][$key]))) { // POSTされた数より多い場合
            $OrderMod->orderGoodsUpdate([
              'id' => $ordersGoodsId[$i],
              'key' => $i,
              'goods_id' => $orderId,
              $this->orderGoodsUpdate(),
            ]);
          }
        }
      } else { // DBとPOSTの注文番号一致数がPOSTされた数より少ない場合
        foreach ($_POST['goods_id'][$key] as $i => $orderId) { // POSTされた商品数までループ
          foreach ($ordersGoodsId as $id) { // DBとPOSTの注文番号一致数までループ
            if ($id == $_POST['id'][$key][$i]) { // DB内のidとPOSTされたidが同じだった場合
              var_dump('bb');
              $OrderMod->orderGoodsUpdate([
                'id' => $id,
                'key' => $i,
                'goods_id' => $orderId,
                $this->orderGoodsUpdate(),
              ]);
              break;
            } else { // DB内にPOSTされたidがなかった場合
              $OrderMod->orderGoodsCreate([
                'number' => $_POST['number'][$key],
                'goods_id' => $orderId,
                'price' => $price[0],
                'count' => $_POST['count'][$key][$i],
                'size' => $_POST['size'][$key][$i],
                'color' => $_POST['color'][$key][$i],
              ]);
              break;
            }
          }
        }
      }
      header('Location: '. SITE_URL . '/order_confirm.php?page_id=' .$_GET['page_id']. '#order[' .$key .']');
      exit();
    }
  }

  // 注文削除画面への遷移
  private function cancel() {
    $key = array_keys($_POST['cancel'])['0']; // 注文番号の特定
    $_SESSION['number'] = $_POST['number'][$key]; // 注文番号をセッションに格納
    header('Location: ' . SITE_URL . '/cancel.php'); // 注文削除画面へ遷移
    exit();
  }

  // 注文更新バリデーション
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
      }
      if ($_POST['credit'][$key] == 1 && $_POST['pay'][$key] == 'credit') {
        $errors['pay'] = "クレジットへの変更はできません。";
      }
      if (count(array_filter($_POST['goods_id'][$key])) == 0) {
        $errors['goods'] = "１つ以上の注文商品登録をしてください。";
      }
      if (!empty($errors)) {
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
      }
    }
  }

  // 注文削除
  private function delete() {
    $OrderMod = new \Ec\Model\order();
    $OrderMod->delete([
      'number' => $_SESSION['number'],
    ]);
    $_SESSION['number'] = array(); // セッション削除
    header('Location: ' . SITE_URL . '/order_confirm.php'); // 発送管理画面へ遷移
    exit();
  }

  // 注文検索
  public function search() {
    // 注文番号が入力されていた場合
    if (isset($_GET['number'])) {
      $number = $_GET['number'];
    }
    // お客様名が入力されていた場合
    if (isset($_GET['username'])) {
      $username = $_GET['username'];
    }
    // 電話番号が入力されていた場合
    if (isset($_GET['tel'])) {
      $tel = $_GET['tel'];
    }
    // 発送状態が選択されていた場合
    if (isset($_GET['status'])) {
      $status = $_GET['status'];
    } else {
      $status = 0; // 選択されていなかった場合、発送準備中を選択
    }
    // 入力した値を保存
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

}
