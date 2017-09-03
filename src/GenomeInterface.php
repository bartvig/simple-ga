<?php
/**
 * @file
 * Gene
 */

namespace SimpleGA;

interface GenomeInterface {

  public function __construct($container);

  public function getSize();

  public function generate($parts);

  public function mutate();

  public function evaluate($test);

  public function getFitness();

  public function getPart($a, $b);

  public function toString();

}
