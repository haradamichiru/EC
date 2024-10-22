<?php
require_once(__DIR__ .'/../config/config.php');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <title>Vue</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Antique+Soft&family=Zen+Old+Mincho&display=swap" rel="stylesheet">
  <script src="./js/check.js"></script>
  <script src="./js/count.js"></script>
  <script src="./js/validation.js"></script>
  <link rel="stylesheet" href="./css/shopping_styles.css">
</head>
<body>
<header class="header">
<div class="header_inner">
  <div><a href="<?= SITE_URL; ?>/index.php"><img src="./asset/img/Vue_logo.png"></a></div>
  <nav>
    <ul>
      <li class="menu"><a href="<?= SITE_URL; ?>/shopping_all.php"><img src="./asset/img/cart.jpg"></a></li>
    </ul>
  </nav>
</div>
</header>
<div class="main">
