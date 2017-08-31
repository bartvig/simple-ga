<?php
/**
 * @file
 * Gene
 */

namespace SimpleGA;

interface GenomeInterface {

  public function __construct($randomGenerator);

  public function generate(array $parts);

  public function mutate();

  public function evaluate();

  public function getFitness();

  public function toString();

}
