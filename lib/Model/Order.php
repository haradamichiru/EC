<?php
namespace Ec\Model;
class Order extends \Ec\Model {
  public function find($id) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindValue('id',$id);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    return $user;
  }

  // 新規注文
  public function order($values) {
    $stmt = $this->db->prepare("INSERT INTO orders (number, postage, tax_rate, name, kana, email, post_number, address, tel, pay, created, modified) VALUES (:number, :postage, :tax_rate, :name, :kana, :email, :post_number, :address, :tel, :pay, now(), now())");
    $res = $stmt->execute([
      ':number' => $values['number'],
      ':postage' => $values['postage'],
      ':tax_rate' => $values['tax_rate'],
      ':name' => $values['name'],
      ':kana' => $values['kana'],
      ':email' => $values['email'],
      ':post_number' => $values['post-number'],
      ':address' => $values['address'],
      ':tel' => $values['tel'],
      ':pay' => $values['pay'],
    ]);
  }
  // 新規注文時に注文された商品のサイズとカラーをそれぞれのDBに追加
  public function orderGoods($values) {
    $stmt = $this->db->prepare("INSERT INTO order_goods (order_number, goods_id, price, count, size, color, created, modified) VALUES (:order_number, :goods_id, :price, :count, :size, :color, now(), now())");
    if (empty($values['size'])) {
      if (empty($values['color'])) { // サイズ、カラーどちらも連携されていない場合
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => '', // サイズを空でDBに追加
          ':color' => '', // カラーを空でDBに追加
        ]);
      } else { // サイズが連携なし、カラーが連携されている場合
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => '', // サイズを空でDBに追加
          ':color' => $values['color'],
        ]);
      }
    } else {
      if (empty($values['color'])) { // サイズが連携あり、カラーが連携されていない場合
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => $values['size'],
          ':color' => '', // カラーを空でDBに追加
        ]);
      } else { // サイズ、カラーどちらも連携されている場合
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => $values['size'],
          ':color' => $values['color'],
        ]);
      }
    }
  }

  // 注文商品一覧
  public function ordersGoods() {
    $stmt = $this->db->query("SELECT * FROM order_goods");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 注文一覧
  public function orders() {
    $stmt = $this->db->query("SELECT * FROM orders");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 注文追加
  public function addGoods() {
    $stmt = $this->db->query("SELECT * FROM users");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 注文更新
  public function update($values) {
    $stmt = $this->db->prepare("UPDATE orders SET status = :status, email = :email, name = :name, kana = :kana, post_number = :post_number, address = :address, tel = :tel, pay = :pay, modified = now() WHERE number = :number");
    $stmt->execute([
      ':number' => $values['number'],
      ':status' => $values['status'],
      ':email' => $values['email'],
      ':name' => $values['name'],
      ':kana' => $values['kana'],
      ':post_number' => $values['post-number'],
      ':address' => $values['address'],
      ':tel' => $values['tel'],
      ':pay' => $values['pay'],
    ]);
  }

  // 注文商品更新
  public function orderGoodsUpdate($values) {
    $key = $values['key'];
    // 商品数がNULLだった場合に0としてDBに保存
    if (isset($values[0]['count'][$key])) {
      $count = $values[0]['count'][$key];
    } else {
      $count = '0';
    }
    // 商品サイズがNULLだった場合に空白としてDBに保存
    if (isset($values[0]['size'][$key])) {
      $size = $values[0]['size'][$key];
    } else {
      $size = '';
    }
    // 商品カラーがNULLだった場合に空白としてDBに保存
    if (isset($values[0]['color'][$key])) {
      $color = $values[0]['color'][$key];
    } else {
      $color = '';
    }

    $stmt = $this->db->prepare("UPDATE order_goods SET goods_id = :goods_id, count = :count, size = :size, color = :color, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $values['id'],
      ':goods_id' => $values['goods_id'],
      ':count' => $count,
      ':size' => $size,
      ':color' => $color,
    ]);
  }

  // 注文商品追加
  public function orderGoodsCreate($values) {
    $stmt = $this->db->prepare("INSERT INTO order_goods (order_number, goods_id, price, count, size, color, created, modified) VALUES (:number, :goods_id, :price, :count, :size, :color, now(), now())");
    $res = $stmt->execute([
      ':number' => $values['number'],
      ':goods_id' => $values['goods_id'],
      ':price' => $values['price'],
      ':count' => $values['count'],
      ':size' => $values['size'],
      ':color' => $values['color'],
    ]);
  }


  // 注文削除
  public function delete($values) {
    $stmt = $this->db->prepare("UPDATE orders SET delflag = :delflag, modified = now() where number = :number");
    $stmt->execute([
      ':delflag' => 1,
      ':number' => $values['number'],
    ]);
  }

  // 注文検索
  public function searchOrder($values) {
    $stmt = $this->db->prepare("SELECT * FROM orders WHERE number LIKE :number AND name LIKE :name AND tel LIKE :tel AND status = :status AND delflag = 0");
    $stmt->execute([
      ':number' => '%'.$values['number'].'%',
      ':name' => '%'.$values['username'].'%',
      ':tel' => '%'.$values['tel'].'%',
      ':status' => $values['status'],
    ]);
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

}