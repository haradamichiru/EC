<?php
namespace Bbs\Controller;
class Thread extends \Bbs\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if ($_POST['type'] === 'createthread') {
        $this->createThread();
      } elseif($_POST['type'] === 'createcomment') {
        $this->createComment();
      }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
      $threadData = $this->searchThread();
      return $threadData;
    }
  }
  private function createThread() {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
        $this->setErrors('create_thread', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
        $this->setErrors('create_thread', $e->getMessage());
    }
    $this->setValues('thread_name', $_POST['thread_name']);
    $this->setValues('comment', $_POST['comment']);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadModel->createThread([
        'title' => $_POST['thread_name'],
        'comment' => $_POST['comment'],
        'user_id' => $_SESSION['me']->id
      ]);
      header('Location: '. SITE_URL . '/thread_all.php');
      exit();
    }
  }

  private function createComment() {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
        $this->setErrors('comment', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
        $this->setErrors('comment', $e->getMessage());
    }
    $this->setValues('content', $_POST['content']);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadModel->createComment([
        'thread_id' => $_POST['thread_id'],
        'user_id' => $_SESSION['me']->id,
        'content' => $_POST['content']
      ]);
    }
    header('Location: '. SITE_URL . '/thread_disp.php?thread_id=' . $_POST['thread_id']);
    exit();
  }

  public function outputCsv($thread_id) {
    try {
      $threadModel = new \Bbs\Model\Thread();
      $data = $threadModel->getCommentCsv($thread_id);
      $csv = array('num','username','content','date');
      $csv = mb_convert_encoding($csv,'SJIS-WIN','UTF-8');
      $date = date("YmdH:i:s");
      header('Contet-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='. $date .'_thread.csv');
      $stream = fopen('php://output', 'w');
      stream_filter_prepend($stream,'convert.iconv.utf-8/cp932');
      $i = 0;
      foreach ($data as $row) {
        if ($i === 0) {
          fputcsv($stream , $csv);
        }
        fputcsv($stream , $row);
        $i++;
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function searchThread() {
    try {
      $this->validateSearch();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('keyword', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
      $this->setErrrors('keyword', $e->getMessage());
    }

    $keyword = $_GET['keyword'];
    $this->setValues('keyword', $keyword);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadData = $threadModel->searchThread($keyword);
      return $threadData;
    }
  }

  private function validate() {
    if ($_POST['type'] === 'createthread') {
      if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        echo "不正なトークンです!";
        exit();
      }
      if (!isset($_POST['thread_name'])) {
        echo '不正な投稿です';
        exit();
      }
      if ($_POST['thread_name'] === '' || $_POST['comment'] === '') {
        throw new \Bbs\Exception\EmptyPost("スレッド名または最初のコメントが入力されていません!");
      }
      if (mb_strlen($_POST['thread_name']) > 20) {
        throw new \Bbs\Exception\CharLength("スレッド名が長すぎます!");
      }
      if (mb_strlen($_POST['comment']) > 200) {
        throw new \Bbs\Exception\CharLength("コメントが長すぎます!");
      }
    }
    if ($_POST['type'] === 'createcomment') {
      if ($_POST['content'] === '') {
        throw new \Bbs\Exception\EmptyPost("コメントが入力されていません!");
      }
      if (mb_strlen($_POST['content']) > 200) {
        throw new \Bbs\Exception\CharLength("コメントが長すぎます!");
      }
    }
  }

  private function validateSearch() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
      if ($_GET['keyword'] === '') {
        throw new \Bbs\Exception\emptyPost("検索キーワードが入力されていません！");
      }
      if (mb_strlen($_GET['keyword']) > 20) {
        throw new \Bbs\Exception\CharLength("キーワードが長すぎます！");
      }
    }
  }
}