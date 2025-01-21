<?php
namespace Ec\Model;
class Order extends \Ec\Model {

  public function find($id) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id;");
    $stmt->bindValue('id',$id);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    return $user;
  }

  // 新規注文
  public function order($values) {
    $stmt = $this->db->prepare("INSERT INTO orders (number, postage, tax, name, kana, email, post_number, address, tel, pay, created, modified) VALUES (:number, :postage, :tax, :name, :kana, :email, :post_number, :address, :tel, :pay, now(), now())");
    $res = $stmt->execute([
      ':number' => $values['number'],
      ':postage' => $values['postage'],
      ':tax' => $values['tax'],
      ':name' => $values['name'],
      ':kana' => $values['kana'],
      ':email' => $values['email'],
      ':post_number' => $values['postNum'],
      ':address' => $values['address'],
      ':tel' => $values['tel'],
      ':pay' => $values['pay'],
    ]);
  }
  public function orderGoods($values) {
    $stmt = $this->db->prepare("INSERT INTO order_goods (order_number, goods_id, price, count, size, color, created, modified) VALUES (:order_number, :goods_id, :price, :count, :size, :color, now(), now())");
    if (empty($values['size'])) {
      if (empty($values['color'])) {
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => '',
          ':color' => '',
        ]);
      } else {
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => '',
          ':color' => $values['color'],
        ]);
      }
    } else {
      if (empty($values['color'])) {
        $res = $stmt->execute([
          ':order_number' => $values['order_number'],
          ':goods_id' => $values['goods_id'],
          ':price' => $values['price'],
          ':count' => $values['count'],
          ':size' => $values['size'],
          ':color' => '',
        ]);
      } else {
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
  public function orders() {
    $stmt = $this->db->query("SELECT * FROM order_goods");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 注文商品一覧
  // public function ordersGoods() {
  //   $stmt = $this->db->query("SELECT * FROM order_goods");
  //   return $stmt->fetchAll(\PDO::FETCH_OBJ);
  // }

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
      ':post_number' => $values['postNum'],
      ':address' => $values['address'],
      ':tel' => $values['tel'],
      ':pay' => $values['pay'],
    ]);
  }

  // 注文商品更新
  public function orderGoodsUpdate($values) {
    var_dump($values);
    exit();
    // $stmt = $this->db->prepare("UPDATE order_goods SET goods_id = :goods_id, price = :price, count = :count, size = :size, color = :color, modified = now() WHERE order_number = :number");
    // $stmt->execute([
    //   ':number' => $values['number'],
    //   ':goods_id' => $values['goods_id'],
    //   ':price' => $values['price'],
    //   ':count' => $values['count'],
    //   ':size' => $values['size'],
    //   ':color' => $values['color'],
    // ]);
  }

  // 注文商品追加
  public function orderGoodsCreate($values) {
    var_dump($values);
    exit();

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