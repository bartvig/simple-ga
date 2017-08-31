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
define('SIMPLEGA_NO_GENOME_FOUND', 2);
define('SIMPLEGA_TOO_MANY_GENES', 3);
define('SIMPLEGA_WRONG_GENE_VALUE', 4);
define('SIMPLEGA_GENE_NOT_EXISTS', 5);

class SimpleGAException extends \Exception {
  public function __construct(
    $message = "",
    $code = 0,
    Throwable $previous = NULL
  ) {
    parent::__construct($message, $code, $previous);
  }
}
