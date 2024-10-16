<?php
namespace Bbs\Controller;
class AdminUser extends \Bbs\Controller {
  public function adminRun() {
    $aut = (int)$_SESSION['me']->authority;
    if (!($_SESSION['me']->authority == "99")) {
      header('Location: '. SITE_URL . '/mypage.php');
      exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
      $this->adminUser();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
      $this->newUser();
    }
  }

  public function updateRun() {
    $this->userUpdate();
  }

  public function deleteRun() {
    $this->userDelete();
  }

  protected function newUser() {
    $errorMessages = [];
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    } if (!empty($errorMessages)) {
      $this->setErrors('username', $errorMessages['username']);
      $this->setErrors('email', $errorMessages['email']);
      $this->setErrors('password', $errorMessages['password']);
      $this->setErrors('authority', $errorMessages['authority']);
      $this->setErrors('delflag', $errorMessages['delflag']);
      $this->setValues('email', $_POST['email']);
      $this->setValues('username', $_POST['username']);
      $this->setValues('image', $_POST['image']);
      $this->setValues('authority', $_POST['authority']);
      $this->setValues('delflag', $_POST['delflag']);  
      return $errorMessages;
    } else {
      try {
        $AdminMod = new \Bbs\Model\AdminUser();
        $user = $AdminMod->create([
          'email' => $_POST['email'],
          'username' => $_POST['username'],
          'password' => $_POST['password'],
          'image' => $_POST['image'],
          'authority' => $_POST['authority'],
          'delflag' => $_POST['delflag']
        ]);
      }
      catch (\Bbs\Exception\DuplicateEmail $e) {
        $this->setErrors('email', $e->getMessage());
        return;
      }
      header('Location:'. SITE_URL . '/admin-users.php');
      exit();
    }
  }

  protected function adminUser() {
    $errorMessages = [];
    try {
      $this->validate();
    } catch (\Exception $e) {
      $errorMessages = json_decode($e->getMessage(), true);
    }
    if (!empty($errorMessages)) {
      $this->setErrors('id', $errorMessages['rajio']);
      $this->setErrors('username', $errorMessages['username']);
      $this->setErrors('email', $errorMessages['email']);
      $this->setErrors('authority', $errorMessages['authority']);
      $this->setErrors('delflag', $errorMessages['delflag']);
      $this->setValues('id', $_POST['id']);
      $this->setValues('email', $_POST['email'][$_POST['id']]);
      $this->setValues('username', $_POST['username'][$_POST['id']]);
      $this->setValues('image', $_POST['image'][$_POST['id']]);
      $this->setValues('authority', $_POST['authority'][$_POST['id']]);
      $this->setValues('delflag', $_POST['delflag'][$_POST['id']]);
      // var_dump($errorMessages);
      return $errorMessages;
    } elseif ($_POST['action'] == '更新') {
      $_SESSION['id'] = $_POST['id'];
      $_SESSION['username'] = $_POST['username'];
      $_SESSION['email'] = $_POST['email'];
      $_SESSION['image'] = $_POST['image'];
      $_SESSION['authority'] = $_POST['authority'];
      $_SESSION['delflag'] = $_POST['delflag'];
      header('Location: ' . SITE_URL . '/admin-update.php');
    } elseif ($_POST['action'] == '削除') {
      $_SESSION['id'] = $_POST['id'];
      $_SESSION['username'] = $_POST['username'];
      $_SESSION['email'] = $_POST['email'];
      $_SESSION['image'] = $_POST['image'];
      $_SESSION['authority'] = $_POST['authority'];
      $_SESSION['delflag'] = $_POST['delflag'];
      header('Location: ' . SITE_URL . '/admin-delete.php');
    }
  }

  private function validate() {
    if (isset($_POST['action'])) {
      $id = $_POST['id'];
      if ($id === NULL) {
        $errors['rajio'] = "ラジオボタンが選択されていません!";
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
        return;
      }
      if ($_POST['username'][$id] === '') {
        $errors['username'] = "ユーザー名が入力されていません!";
      }
      if (!filter_var($_POST['email'][$id],FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "メールアドレスが不正です!";
      }
      if ($_POST['username'][$id] === '') {
        $errors['username'] = "ユーザー名が入力されていません!";
      }
      if (!($_POST['authority'][$id] == "1" || $_POST['authority'][$id] == "99")) {
        $errors['authority'] = "権限は「1=一般ユーザー」か「99=管理者」を入力してください！";
      }
      if (!($_POST['delflag'][$id] == "1" || $_POST['delflag'][$id] == "0")) {
        $errors['delflag'] = "削除フラグは0か1を入力してください！（1は削除されたユーザーになります）";
      }
      if (!empty($errors)) {
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
      }
    }
    if (isset($_POST['password'])) {
      if (!isset($_POST['email']) || !isset($_POST['username']) || !isset($_POST['password'])) {
        echo "不正なフォームから登録されています!";
        exit();
      }
      if ($_POST['username'] === '') {
        $errors['username'] = "ユーザー名が入力されていません!";
      }
      if (!preg_match('/\A[a-zA-Z0-9]+\z/', $_POST['password'])) {
        $errors['password'] = "パスワードが不正です!";
      }
      if (!($_POST['authority'] == "1" || $_POST['authority'] == "99")) {
        $errors['authority'] = "権限は「1=一般ユーザー」か「99=管理者」を入力してください！";
      }
      if (!($_POST['delflag'] == "1" || $_POST['delflag'] == "0")) {
        $errors['delflag'] = "削除フラグは0か1を入力してください！（1は削除されたユーザーになります）";
      }
      if (!filter_var($_POST['email'][$id],FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "メールアドレスが不正です!";
      }
      if (!empty($errors)) {
        throw new \Exception(json_encode($errors, JSON_UNESCAPED_UNICODE));
      }
    }
  }

  private function userUpdate() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type']) == 'update') {
      $id = $_SESSION['id'];
      $username = $_SESSION['username'];
      $email = $_SESSION['email'];
      $image = $_SESSION['image'];
      $authority = $_SESSION['authority'];
      $delflag = $_SESSION['delflag'];
      $AdminMod = new \Bbs\Model\AdminUser();
      $AdminMod->update([
        'username' => $username[$id],
        'email' => $email[$id],
        'image' => $image[$id],
        'authority' => $authority[$id],
        'delflag' => $delflag[$id],
      ]);
    header('Location: ' . SITE_URL . '/admin-users.php');
    exit();
    }
  }

  public function userDelete() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type']) == 'delete') {

      $adminMod = new \Bbs\Model\AdminUser();
      $adminMod->delete();

      header('Location: ' . SITE_URL . '/admin-users.php');
      exit();
    }
  }

}
