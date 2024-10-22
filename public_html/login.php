<?php
require_once(__DIR__ .'/header_admin.php');
$app = new Ec\Controller\Login();
$app->run();
?>
    <div class="login">
      <h1>運営者ログイン</h1>
        <form class="login_form" method="post" onsubmit="return validateFormAdmin()" name="adminForm">
          <div class="login_id">
            <label for="id">ログインID</label>
            <input class="form-text" type="text" name="id" value="<?= isset($app->getValues()->loginid) ? h($app->getValues()->loginid) : ''; ?>">
            <p class="err-txt" id="err-id"></p>
          </div>
          <div class="login_password">
            <label for="password">パスワード</label>
            <input class="form-text" type="text" name="password" value="">
            <p class="err-txt" id="err-password"></p>
          </div>
        <p class="err-txt"><?= h($app->getErrors('login')); ?></p>
        <input class="btn" type="submit" value="ログイン">
        <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
      </form>
    </div>
<?php
  require_once(__DIR__ .'/footer_admin.php');
?>

