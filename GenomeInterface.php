<?php
/**
 * @file
 * Gene
 */

namespace SimpleGA;

interface GenomeInterface {

  public function __construct(RandomInterface $randomGenerator);

  public function generate(array $parts);

  public function mutate();

  public function evaluate();

  public function getFitness();

  static public function create(RandomInterface $randomGenerator);

}
