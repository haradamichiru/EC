<?php
require_once(__DIR__ .'/header.php');
?>
    <section class="completed">
      <h2>注文番号：<?= h($_SESSION['number']); ?></h2>
      <div class="thanks">
        <p>ご注文ありがとうございました。</p>
        <p>またのご利用をお待ちしております。</p>
      </div>
      <a class="back" href="<?= SITE_URL; ?>/index.php">トップへ戻る</a>
    </section>
<?php
  require_once(__DIR__ .'/footer.php');
?>

