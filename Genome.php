<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 8/31/17
 * Time: 9:58 AM
 */

namespace SimpleGA;


class Genome implements GenomeInterface {

  protected $randomGenerator = NULL;

  protected $fitness = NULL;

  protected $genome;

  public function __construct(\SimpleGA\RandomInterface $randomGenerator) {
    $this->randomGenerator = $randomGenerator;
  }

  public function mutate() {
    // TODO: Implement mutate() method.
  }

  public function generate($firstPart = NULL, $secondPart = NULL, $thirdPart = NULL) {
    if (!is_null($firstPart) && !is_null($secondPart) && !is_null($thirdPart)) {
      $this->genome = $firstPart + $secondPart + $thirdPart;
      return;
    }
  }

  public function evaluate() {
    // TODO: Implement evaluate() method.
  }

  public function getFitness() {
    // TODO: Implement getFitness() method.
    if (is_null($this->fitness)) {
      throw new SimpleGAException('Genome is not evaluated', SIMPLEGA_NO_FITNESS);
    }
  }

  static public function create(RandomInterface $randomGenerator) {
    return function($randomGenerator) {
      return new self($randomGenerator);
    };
  }

}
