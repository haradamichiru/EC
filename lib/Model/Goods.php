<?php
namespace Ec\Model;
class Goods extends \Ec\Model {
  // 商品追加
  public function create($values) {
    // サイズとカラーを配列のまま保存
    $size_data = serialize($values['size']);
    $color_data = serialize($values['color']);
    $stmt = $this->db->prepare("INSERT INTO goods (goods_name,price,explanation,image,size,color,created,modified) VALUES (:goods_name,:price,:explanation,:image,:size,:color,now(),now())");
    $res = $stmt->execute([
      ':goods_name' => $values['goods_name'],
      ':price' => $values['price'],
      ':explanation' => $values['explanation'],
      ':image' => $values['image'],
      ':size' => $size_data,
      ':color' => $color_data,
    ]);
  }

  // 商品詳細
  public function getGoods($goods_id) {
    $stmt = $this->db->prepare("SELECT * FROM goods WHERE id = :id AND delflag = 0");
    $stmt->bindValue(":id",$goods_id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_OBJ);
  }

  public function login($values) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email;");
    $stmt->execute([
      ':email' => $values['email']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    if (empty($user)) {
      throw new \Ec\Exception\UnmatchEmailOrPassword();
    }
    if (!password_verify($values['password'], $user->password)) {
      throw new \Ec\Exception\UnmatchEmailOrPassword();
    }
    if ($user->delflag == 1) {
      throw new \Ec\Exception\DeleteUser();
    }
    return $user;
  }

  public function find($id) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id;");
    $stmt->bindValue('id',$id);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    return $user;
  }

  // 商品一覧
  public function goods() {
    $stmt = $this->db->query("SELECT * FROM goods WHERE delflag = 0");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 設定値
  public function settings() {
    $stmt = $this->db->query("SELECT * FROM users");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 注文追加
  public function order($values) {
    $items_data = serialize($values['items']);
    $stmt = $this->db->prepare("INSERT INTO orders (number, goods,tax,customers_email,customers_name,customers_kana,customers_address,customers_tel,customers_pay,created,modified) VALUES (:number, :goods,:tax,:customers_email,:customers_name,:customers_kana,:customers_address,:customers_tel,:customers_pay,now(),now())");

    $res = $stmt->execute([
      ':number' => $values['number'],
      ':goods' => $items_data,
      ':tax' => $values['tax'],
      ':customers_email' => $values['email'],
      ':customers_name' => $values['name'],
      ':customers_kana' => $values['kana'],
      ':customers_address' => $values['address'],
      ':customers_tel' => $values['tel'],
      ':customers_pay' => $values['pay'],
    ]);

  }

  // 商品削除
  public function delete($values) {
    $stmt = $this->db->prepare("UPDATE goods SET delflag = :delflag, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $values['id'],
      ':delflag' => 1,
    ]);

  }

  // 商品更新
  public function goodsUpdate($values) {
    if (!empty($values['color'])) {
      $color = serialize(array_filter($values['color']));
    }
    if (!empty($values['size'])) {
      $size = serialize(array_filter($values['size']));
    }

    $stmt = $this->db->prepare("UPDATE goods SET goods_name = :goods_name, price = :price, explanation = :explanation, image = :image, size = :size, color = :color, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $values['id'],
      ':goods_name' => $values['goods_name'],
      ':price' => $values['price'],
      ':explanation' => $values['explanation'],
      ':image' => $values['image'],
      ':size' => $size,
      ':color' => $color,
    ]);
  }

  // 設定値更新
  public function settingUpdate($values) {
    $stmt = $this->db->prepare("UPDATE users SET postage = :postage, tax = :tax, modified = now() WHERE loginid = :loginid");
    $stmt->execute([
      ':loginid' => $values['id'],
      ':postage' => $values['postage'],
      ':tax' => $values['tax'],
    ]);
  }

}