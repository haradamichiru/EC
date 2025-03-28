<?php
namespace Ec\Model;
class Goods extends \Ec\Model {
  // 商品追加
  public function create($values) {
    $stmt = $this->db->prepare("INSERT INTO goods (name,price,explanation,image,created,modified) VALUES (:name,:price,:explanation,:image,now(),now())");
    $res = $stmt->execute([
      ':name' => $values['goods_name'],
      ':price' => $values['price'],
      ':explanation' => $values['explanation'],
      ':image' => $values['image'],
    ]);
  }

  // サイズ追加
  public function sizeCreate($values) {
    $stmt = $this->db->prepare("INSERT INTO sizes (id,size,created,modified) VALUES (:id,:size,now(),now())");
    $res = $stmt->execute([
      ':id' => $values['id'],
      ':size' => $values['size'],
    ]);
  }

  // サイズ更新
  public function sizeUpdate($values) {
    $stmt = $this->db->prepare("UPDATE sizes SET size = :size, modified = now() WHERE id = :id");
    $res = $stmt->execute([
      ':id' => $values['id'],
      ':size' => $values['size'],
    ]);
  }

  // カラー追加
  public function colorCreate($values) {
    $stmt = $this->db->prepare("INSERT INTO colors (id,color,created,modified) VALUES (:id,:color,now(),now())");
    $res = $stmt->execute([
      ':id' => $values['id'],
      ':color' => $values['color'],
    ]);
  }

  // カラー更新
  public function colorUpdate($values) {
    $stmt = $this->db->prepare("UPDATE colors SET color = :color, modified = now() WHERE id = :id");
    $res = $stmt->execute([
      ':id' => $values['id'],
      ':color' => $values['color'],
    ]);
  }

  // 商品詳細
  public function getGoods($goods_id) {
    $stmt = $this->db->prepare("SELECT * FROM goods WHERE id = :id AND delflag = 0");
    $stmt->bindValue(":id",$goods_id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_OBJ);
  }

  // ログイン
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

  // 商品一覧
  public function goods() {
    $stmt = $this->db->query("SELECT * FROM goods WHERE delflag = 0");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // サイズ一覧
  public function sizes() {
    $stmt = $this->db->query("SELECT * FROM sizes WHERE delflag = 0");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 商品サイズ一覧
  public function goods_sizes() {
    $stmt = $this->db->query("SELECT * FROM goods_size");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // カラー一覧
  public function colors() {
    $stmt = $this->db->query("SELECT * FROM colors WHERE delflag = 0");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 商品カラー一覧
  public function goods_colors() {
    $stmt = $this->db->query("SELECT * FROM goods_color");
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
    $stmt = $this->db->prepare("UPDATE goods SET name = :goods_name, price = :price, explanation = :explanation, image = :image, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $values['id'],
      ':goods_name' => $values['goods_name'],
      ':price' => $values['price'],
      ':explanation' => $values['explanation'],
      ':image' => $values['image'],
    ]);
  }

  // 商品サイズ追加
  public function goodsSizeCreate($values) {
    $stmt = $this->db->prepare("INSERT INTO goods_size (goods_id,size,created,modified) VALUES (:goods_id,:size,now(),now())");
    $res = $stmt->execute([
      ':goods_id' => $values['goods_id'],
      ':size' => $values['size'],
    ]);
  }

  // 商品サイズ更新
  public function goodsSizeUpdate($values) {
    $stmt = $this->db->prepare("UPDATE goods_size SET size = :size, delflag = :delflag, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $values['id'],
      ':size' => $values['size'],
      ':delflag' => $values['delflag'],
    ]);
  }

  // 商品カラー追加
  public function goodsColorCreate($values) {
    $stmt = $this->db->prepare("INSERT INTO goods_color (goods_id,color,created,modified) VALUES (:goods_id,:color,now(),now())");
    $res = $stmt->execute([
      ':goods_id' => $values['goods_id'],
      ':color' => $values['color'],
    ]);
  }

  // 商品カラー更新
  public function goodsColorUpdate($values) {
    $stmt = $this->db->prepare("UPDATE goods_color SET color = :color, delflag = :delflag, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $values['id'],
      ':color' => $values['color'],
      ':delflag' => $values['delflag'],
    ]);
  }


  // 設定値更新
  public function settingUpdate($values) {
    $stmt = $this->db->prepare("UPDATE users SET postage = :postage, modified = now() WHERE loginid = :loginid");
    $stmt->execute([
      ':loginid' => $values['id'],
      ':postage' => $values['postage'],
    ]);
  }

}