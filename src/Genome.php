<?php
/**
 * @file
 * Genome class.
 */

namespace SimpleGA;

/**
 * Class Genome
 *
 * Class for handling a single genome.
 *
 * @package SimpleGA
 */
class Genome implements GenomeInterface {

  /**
   * @var null
   *   Random number generator.
   */
  protected $randomGenerator = NULL;

  /**
   * @var null
   *   Fitness of genome.
   */
  protected $fitness = NULL;

  /**
   * @var array
   *   Genes as an array.
   */
  protected $genome = [];

  /**
   * Genome constructor.
   *
   * @param $randomGenerator
   *   Random number generator to be used for selecting genes.
   */
  public function __construct($randomGenerator) {
    $this->randomGenerator = $randomGenerator;
  }

  /**
   * Mutate genome.
   *
   * Override this method.
   */
  public function mutate() {
  }

  /**
   * Generate genome by gene parts.
   *
   * @param array $parts
   *   One or more gene parts.
   */
  public function generate($parts = []) {
    if (!empty($parts)) {
      foreach ($parts as $part) {
        $this->genome = array_merge($this->genome, $part);
      }
    }
  }

  /**
   * Evaluate genome.
   *
   * Override this method.
   */
  public function evaluate($test = []) {
  }

  /**
   * Get fitness of genome, if it's evaluated.
   *
   * @return int
   *   Return fitness.
   *
   * @throws \SimpleGA\SimpleGAException
   *   Throw exception if fitness hasn't been evaluated.
   */
  public function getFitness() {
    if (is_null($this->fitness)) {
      throw new SimpleGAException('Genome is not evaluated', SIMPLEGA_NO_FITNESS);
    }

    return $this->fitness;
  }

  /**
   * Get part of genome.
   *
   * Override this method.
   *
   * @param int $a
   *   First point to split.
   *
   * @param int $b
   *   Second point to split.
   *
   * @return array
   *   Return part of genome.
   */
  public function getPart($a, $b) {
    $genome = $this->genome;
    $result = array_slice($genome, $a - 1, $b - $a + 1);
    return $result;
  }

  /**
   * Return genome as a string.
   *
   * Override this method.
   *
   * @return string
   */
  public function toString() {
    return '';
  }

}
