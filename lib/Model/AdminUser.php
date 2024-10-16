<?php
namespace Bbs\Model;
class AdminUser extends \Bbs\Model {
  public function create($values) {
    $stmt = $this->db->prepare("INSERT INTO users (username,email,password,authority,delflag,created,modified) VALUES (:username,:email,:password,:authority,:delflag,now(),now())");
    $res = $stmt->execute([
      ':username' => $values['username'],
      ':email' => $values['email'],
      // パスワードのハッシュ化
      ':password' => password_hash($values['password'],PASSWORD_DEFAULT),
      ':authority' => $values['authority'],
      ':delflag' => $values['delflag']
    ]);
    // メールアドレスがユニークでなければfalseを返す
    if ($res === false) {
      throw new \Bbs\Exception\DuplicateEmail();
    }
  }

  public function admin() {
    $stmt = $this->db->query("SELECT * FROM goods");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  public function find($id) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id;");
    $stmt->bindValue('id',$id);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    return $user;
  }

  public function update($values) {
    $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email, image = :image, delflag = :delflag, authority = :authority, modified = now() WHERE id = :id");
    $stmt->execute([
      ':id' => $_SESSION['id'],
      'username' => $values['username'],
      'email' => $values['email'],
      'image' => $values['image'],
      // ':username' => $values[$id],
      // ':email' => $email[$id],
      // ':image' => $image[$id],
      'delflag' => $values['delflag'],
      'authority' => $values['authority'],
    ]);
  }

  public function delete() {
    $stmt = $this->db->prepare("UPDATE users SET delflag = :delflag, modified = now() WHERE id = :id");
    $stmt->execute([
      ':delflag' => 1,
      ':id' => $_SESSION['id']
    ]);
  }

  public function deleteImage() {
    $stmt = $this->db->prepare("UPDATE users SET image = :image, modified = now() WHERE id = :id");
    $stmt->execute([
      ':image' => NULL,
      ':id' => $_SESSION['me']->id,
    ]);
    $_SESSION['me']->image = NULL;
  }


}
