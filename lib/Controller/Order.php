<?php
namespace Ec\Controller;
class Order extends \Ec\Controller {
  public function run() {
    $action = [
      'back' => 'backCart',
      'buy' => 'newOrder',
      'update' => 'updateOrders',
      'cancel' => 'cancel',
      'order_delete' => 'delete',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      foreach ($action as $key => $method) {
        if (isset($_POST[$key])) {
          $this->$method();
          return;
        }
      }
      return;
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
        'postNumber' => $_POST['post-number1']. '-' .$_POST['post-number2'],
        'address' => $_POST['address'],
        'tel' => $_POST['tel'],
        'pay' => $_POST['pay'],
      ];
    } elseif (isset($_POST['update'])) { // 注文更新の場合
      $orders = [
        'number' => $_POST['number'],
        'status' => $_POST['status'],
        'email' => $_POST['email'],
        'name' => $_POST['name'],
        'kana' => $_POST['kana'],
        'postNumber' => $_POST['postNumber'],
        'address' => $_POST['address'],
        'tel' => $_POST['tel'],
        'pay' => $_POST['pay'],
      ];
    }
    return $orders;
  }


  // カートへ戻る
  protected function backCart() {
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['kana'] = $_POST['kana'];
    $_SESSION['post-number1'] = $_POST['post-number1'];
    $_SESSION['post-number2'] = $_POST['post-number2'];
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['tel'] = $_POST['tel'];
    header('Location: ' . SITE_URL . '/shopping_all.php'); // カート内一覧画面へ遷移
    exit();
  }

  // 新規注文
  protected function newOrder() {
    // 注文者情報をordersテーブルに新規追加
    $orderMod = new \Ec\Model\Order();
    $orderMod->order($this->orders());
    // カートに入っていた商品をorder_goodsテーブルに新規追加
    foreach (array_filter($_SESSION['cart']) as $goods_id => $goods) {
      foreach($goods as $key => $detail) {
        // 注文番号、商品ID、金額、個数を配列に格納
        $goods_detail = [
          'order_number' => $_POST['number'],
          'goods_id' => $goods_id,
          'price' => $detail['price'],
          'count' => $detail['count'],
        ];
        if (empty($detail['size'])) {
          if (empty($detail['color'])) { // サイズ、カラーどちらも存在しない場合
            $goods_detail['size'] = ''; // サイズを空で連携
            $goods_detail['color'] = ''; // カラーを空で連携
            $orderMod->orderGoods($goods_detail);
          } else { // サイズなし、カラーありの場合
            $goods_detail['size'] = ''; // サイズを空で連携
            $goods_detail['color'] = $detail['color'];
            $orderMod->orderGoods($goods_detail);
          }
        } else {
          if (empty($detail['color'])) { // サイズあり、カラーなしの場合
            $goods_detail['size'] = $detail['size'];
            $goods_detail['color'] = ''; // カラーを空で連携
            $orderMod->orderGoods($goods_detail);
          } else { // サイズ、カラーどちらも存在している場合
            $goods_detail['size'] = $detail['size'];
            $goods_detail['color'] = $detail['color'];
            $orderMod->orderGoods($goods_detail);
          }
        }
      }
    }
    unset($_SESSION['cart']); // セッション削除
    $_SESSION['number'] = $_POST['number']; // 注文番号をセッションに格納
    header('Location: ' . SITE_URL . '/shopping_completed.php'); // 注文完了画面へ遷移
    exit();
  }


  // 注文更新
  protected function UpdateOrders() {
    // 注文商品更新データ格納
    foreach (array_filter($_POST['count']) as $key => $count) {
      $orderGoodsUpdate[$key] = [
        'count' => $count,
        'size' => $_POST['size'][$key],
        'color' => $_POST['color'][$key],
      ];
    }

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
      $this->setErrors('postNumber', $errorMessages['postNumber']);
      $this->setErrors('address', $errorMessages['address']);
      $this->setErrors('tel', $errorMessages['tel']);
      $this->setErrors('pay', $errorMessages['pay']);
      $this->setErrors('goods', $errorMessages['goods']);
      foreach ($this->orders() as $key => $detail) {
        $this->setValues($key, $detail);
      };
      return [$errorMessages, $errors];
    } else {
      $OrderMod = new \Ec\Model\Order();
      $GoodsMod = new \Ec\Model\Goods();
      $goods_data = $GoodsMod->goods(); // DB"goods"を変数格納
      $order_goods = $OrderMod->ordersGoods(); // DB"order_goods"を変数格納

      // 注文者情報更新
      $OrderMod->update($this->orders());

      // DB"goods"内でPOSTされた商品IDと同じ商品IDの金額を配列として格納
      foreach ($goods_data as $goods) {
        foreach ($_POST['goods_id'] as $post_id) {
          if ($post_id == $goods->id) {
            $price[] = $goods->price;
          }
        }
      }

      // DB"order_goods"内でとPOSTされた注文番号が同じIDと商品IDを配列として格納
      foreach ($order_goods as $orders) {
        if ($orders->order_number == $_POST['number']) {
          $ordersGoodsId[] = $orders->id;
          $goodsId[] = $orders->goods_id;
        }
      }
      if (count($ordersGoodsId) >= count(array_filter($_POST['goods_id']))) { // DBとPOSTの注文番号一致数がPOSTされた数以上の場合
        foreach (array_filter($_POST['goods_id']) as $key => $orderId) { // POSTされた商品数までループ
          $orderGoodsUpdate[$key]['id'] = $ordersGoodsId[$key];
          $orderGoodsUpdate[$key]['goods_id'] = $orderId;
          $OrderMod->orderGoodsUpdate($orderGoodsUpdate[$key]);
        }
        foreach ($ordersGoodsId as $key => $orderId) { // DB内とPOSTされた注文番号が一致する数までループ
          if ($key >= count(array_filter($_POST['goods_id']))) { // POSTされた数より多い場合
            $orderGoodsUpdate[$key]['id'] = $ordersGoodsId[$key];
            $orderGoodsUpdate[$key]['goods_id'] = $orderId;
            $OrderMod->orderGoodsUpdate($orderGoodsUpdate[$key]);
          }
        }
      } else { // DBとPOSTの注文番号一致数がPOSTされた数より少ない場合
        foreach ($_POST['goods_id'] as $key => $orderId) { // POSTされた商品数までループ
          foreach ($ordersGoodsId as $id) { // DBとPOSTの注文番号一致数までループ
            if ($id == $_POST['id'][$i]) { // DB内のidとPOSTされたidが同じだった場合
              $orderGoodsUpdate[$key]['id'] = $id;
              $orderGoodsUpdate[$key]['key'] = $key;
              $orderGoodsUpdate[$key]['goods_id'] = $orderId;
              $OrderMod->orderGoodsUpdate($orderGoodsUpdate[$key]);
              break;
            } else { // DB内にPOSTされたidがなかった場合
              $OrderMod->orderGoodsCreate([
                'number' => $_POST['number'],
                'goods_id' => $orderId,
                'price' => $price[0],
                'count' => $_POST['count'][$key],
                'size' => $_POST['size'][$key],
                'color' => $_POST['color'][$key],
              ]);
              break;
            }
          }
        }
      }
      header('Location: '. SITE_URL . '/order_confirm.php?page_id=' .$_POST['page_id']. '#order[' .$_POST['number'] .']');
      exit();
    }
  }

  // 注文削除画面への遷移
  private function cancel() {
    $_SESSION['number'] = $_POST['number']; // 注文番号をセッションに格納
    header('Location: ' . SITE_URL . '/cancel.php'); // 注文削除画面へ遷移
    exit();
  }

  // 注文更新バリデーション
  private function validate() {
    if (isset($_POST['update'])) {
      if ($_POST['name'] === '') {
        $errors['name'] = "ご注文者名が入力されていません。";
      }
      if ($_POST['kana'] === '') {
        $errors['kana'] = "フリガナが入力されていません。";
      } elseif (preg_match('/^[ァ-ヴー]+$/mu', $_POST['kana']) == 0) {
        $errors['kana'] = "フリガナはカタカナで入力してください。";
      }
      if ($_POST['postNumber'] === '') {
        $errors['postNumber'] = "郵便番号が入力されていません。";
      }
      if ($_POST['address'] === '') {
        $errors['address'] = "住所が入力されていません。";
      }
      if ($_POST['email'] === '') {
        $errors['email'] = "メールアドレスが入力されていません。";
      } elseif (!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "メールアドレスが不正です。";
      }
      if ($_POST['tel'] === '') {
        $errors['tel'] = "電話番号が入力されていません。";
      } elseif (!(preg_match("/^[0-9]{2,4}[0-9]{2,4}[0-9]{3,4}$/",$_POST['tel']) || preg_match("/^[0-9]{2,4}[-][0-9]{2,4}[-][0-9]{3,4}$/",$_POST['tel']))) {
        $errors['tel'] = "電話番号が不正です。";
      }
      if ($_POST['credit'] == 1 && $_POST['pay'] == 'credit') {
        $errors['pay'] = "クレジットへの変更はできません。";
      }
      if (count(array_filter($_POST['goods_id'])) == 0) {
        $errors['goods'] = "１つ以上の注文商品登録をしてください。";
      }
      if (!empty($errors)) {
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
      }
    }
  }

  // 注文更新時のバリデーション時のエラーメッセージ格納
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

  // 注文削除
  protected function delete() {
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
    $this->setValues('seach_number', $number);
    $this->setValues('seach_username', $username);
    $this->setValues('seach_tel', $tel);
    $this->setValues('seach_status', $status);

    $OrderMod = new \Ec\Model\order();
    $orderData = $OrderMod->searchOrder([
      'number' => $number,
      'username' => $username,
      'tel' => $tel,
      'status' => $status,
    ]);
    $max_page = ceil(count($orderData) / VIEW_COUNT);
    if (!isset($_GET['page_id'])) {
      $now = 1;
    } else {
      $now = $_GET['page_id'];
    }
    $start_no = ($now - 1) * VIEW_COUNT;
    $orders_data = array_slice($orderData, $start_no, VIEW_COUNT, true);
    return [$orders_data, $now, $max_page];
  }
}
