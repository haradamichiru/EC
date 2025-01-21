<?php
require_once(__DIR__ .'/../config/config.php');
if (strpos($file_name,'login.php') !== false) {
}
else {
  if (!isset($_SESSION['me'])) {
    header('Location: ' .SITE_URL .'/login.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>運営者ログイン</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Antique+Soft&family=Zen+Old+Mincho&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/admin_styles.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <script src="https://kit.fontawesome.com/8bc1904d08.js"></script>
  <script src="./js/image.js"></script>
  <script src="./js/validation.js"></script>
  <script src="./js/order.js"></script>
  <script src="./js/goods.js"></script>
  <script src="./js/sort.js"></script>
</head>
<body>
  <header class="header">
    <div class="header__inner">
      <div class="header_title">
        <span><img src="./asset/img/Vue_logo.png"></span>
        <span>運営者管理メニュー</span>
      </div>
      <nav>
        <ul>
          <?php if (isset($_SESSION['me'])) { ?>
          <li class="menu"><a href="<?= SITE_URL; ?>/goods_confirm.php">商品管理</a></li>
          <li class="menu"><a href="<?= SITE_URL; ?>/order_confirm.php?page_id=1">発送管理</a></li>
          <form action="logout.php" method="post" id="logout">
            <li class="menu"><input class="logout" type="submit" value="ログアウト"></li>
            <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
          </form>
          <?php } ?>
        </ul>
      </nav>
    </div>
  </header>
  <main class="main">
