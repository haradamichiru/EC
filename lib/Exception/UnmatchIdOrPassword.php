<?php
namespace Ec\Exception;
class UnmatchIdOrPassword extends \Exception {
  protected $message = 'ログインIDまたはパスワードが一致しません。';
}
