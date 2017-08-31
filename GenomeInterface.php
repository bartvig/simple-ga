<?php
/**
 * @file
 * Gene
 */

namespace SimpleGA;

interface GenomeInterface {

  public function __construct(RandomInterface $randomGenerator);

  public function generate($firstPart, $secondPart, $thirdPart);

  public function mutate();

  public function evaluate();

  public function getFitness();

  static public function create(RandomInterface $randomGenerator);

}
