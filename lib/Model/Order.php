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
    $items_data = serialize($values['items']);
    $stmt = $this->db->prepare("INSERT INTO orders (number, goods,postage,tax,customers_email,customers_name,customers_kana,customers_address,customers_tel,customers_pay,created,modified) VALUES (:number, :goods,:postage,:tax,:customers_email,:customers_name,:customers_kana,:customers_address,:customers_tel,:customers_pay,now(),now())");
    $res = $stmt->execute([
      ':number' => $values['number'],
      ':goods' => $items_data,
      ':postage' => $values['postage'],
      ':tax' => $values['tax'],
      ':customers_email' => $values['email'],
      ':customers_name' => $values['name'],
      ':customers_kana' => $values['kana'],
      ':customers_address' => $values['address'],
      ':customers_tel' => $values['tel'],
      ':customers_pay' => $values['pay'],
    ]);
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
    $goods_data = serialize($values['goods']);
    $stmt = $this->db->prepare("UPDATE orders SET goods = :goods, status = :status, customers_email = :customers_email, customers_name = :customers_name,customers_kana = :customers_kana, customers_address = :customers_address, customers_tel = :customers_tel, customers_pay = :customers_pay, modified = now() WHERE number = :number");
    $stmt->execute([
      ':number' => $values['number'],
      ':goods' => $goods_data,
      ':status' => $values['status'],
      ':customers_email' => $values['email'],
      ':customers_name' => $values['name'],
      ':customers_kana' => $values['kana'],
      ':customers_address' => $values['address'],
      ':customers_tel' => $values['tel'],
      ':customers_pay' => $values['pay'],
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
    $stmt = $this->db->prepare("SELECT * FROM orders WHERE number LIKE :number AND customers_name LIKE :customers_name AND customers_tel LIKE :customers_tel AND status = :status AND delflag = 0;");
    $stmt->execute([
      ':number' => '%'.$values['number'].'%',
      ':customers_name' => '%'.$values['username'].'%',
      ':customers_tel' => '%'.$values['tel'].'%',
      ':status' => $values['status'],
    ]);
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

}