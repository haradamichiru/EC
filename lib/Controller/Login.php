<?php
namespace Ec\Controller;
class Login extends \Ec\Controller {
  public function run() {
    if ($this->isLoggedIn()) {
      header('Location: '. SITE_URL . '/goods_confirm.php');
      exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->login();
    }
  }

  // ログイン処理
  protected function login() {
    $this->validate();
    $this->setValues('loginid', $_POST['id']);
    if ($this->hasError()) {
      return;
    } else {
      try {
        $userModel = new \Ec\Model\User();
        $user = $userModel->login([
          'loginid' => $_POST['id'],
          'password' => $_POST['password']
        ]);
      }
      catch (\Ec\Exception\UnmatchIdOrPassword $e) {
        $this->setErrors('login', $e->getMessage());
        return;
      }
      session_regenerate_id(true);
      $_SESSION['me'] = $user;
      header('Location: '. SITE_URL . '/goods_confirm.php'); //商品管理に遷移
      exit();
    }
  }

  private function validate() {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "トークンが不正です!";
      exit();
    }
  }
}