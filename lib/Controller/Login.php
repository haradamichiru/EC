<?php
namespace Ec\Controller;
class Login extends \Ec\Controller {
  public function run() {
    if ($this->isLoggedIn()) {
      header('Location: '. SITE_URL . '/goods_confirm.php');
      exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->postProcess();
    }
  }

  protected function postProcess() {
          $this->validate();

    // try {
    // } catch (\Ec\Exception\EmptyPost $e) {
    //   $this->setErrors('login', $e->getMessage());
    // }
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
      header('Location: '. SITE_URL . '/goods_confirm.php');
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