<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 8/31/17
 * Time: 10:02 AM
 */

namespace SimpleGA;


use Throwable;

define('SIMPLEGA_NO_FITNESS', 1);

class SimpleGAException extends \Exception {
  public function __construct(
    $message = "",
    $code = 0,
    \Throwable $previous = NULL
  ) {
    parent::__construct($message, $code, $previous);
  }
}
