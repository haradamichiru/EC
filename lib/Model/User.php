<?php
namespace Ec\Model;
class User extends \Ec\Model {
  public function login($values) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE loginid = :loginid;");
    $stmt->execute([
      ':loginid' => $values['loginid']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();

    if (empty($user)) {
      throw new \Ec\Exception\UnmatchIdOrPassword();
    }
    if (!password_verify($values['password'], $user->password)) {
      throw new \Ec\Exception\UnmatchIdOrPassword();
    }
    return $user;
  }

  // public function create($values) {
  //   $stmt = $this->db->prepare("INSERT INTO users (loginid, password, created) VALUES (:loginid, :password, now())");
  //   $user = $stmt->execute([
  //     ':loginid' => $values['loginid'],
  //     ':password' => password_hash($values['password'],PASSWORD_DEFAULT)
  //   ]);
  //   return $user;
  // }

  // public function find($id) {
  //   $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id;");
  //   $stmt->bindValue('id',$id);
  //   $stmt->execute();
  //   $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
  //   $user = $stmt->fetch();
  //   return $user;
  // }


}