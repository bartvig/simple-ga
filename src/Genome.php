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

  public function __construct($randomGenerator) {
    $this->randomGenerator = $randomGenerator;
  }

  public function mutate() {
    // TODO: Implement mutate() method.
  }

  public function generate(array $parts = []) {
    if (!empty($parts)) {
      foreach ($parts as $part) {
        $this->genome = $this->genome + $part;
      }
      return;
    }
  }

  public function evaluate() {
  }

  public function getFitness() {
    if (is_null($this->fitness)) {
      throw new SimpleGAException('Genome is not evaluated', SIMPLEGA_NO_FITNESS);
    }

    return $this->fitness;
  }

  public function toString() {
  }

}
