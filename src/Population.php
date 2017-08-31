<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 8/31/17
 * Time: 10:17 AM
 */

namespace SimpleGA;


use Pimple\Container;

class Population implements PopulationInterface {

  /** @var \Pimple\Container $container */
  protected $container;

  /** @var Genome[] $genomes */
  protected $genomes = [];

  public function __construct(Container $container) {
    $this->container = $container;
  }

  /**
   * Initialize all genomes in population.
   */
  public function initialize() {
    $size = $this->container['population_size'];
    for ($i = 0; $i < $size; $i++) {
      $genome = $this->container['genome'];
      $genome->generate();
      $this->genomes[] = $genome;
    }

    // Evaluate genomes.
    $this->evaluatePopulation();

    // Sort genomes according to fitness.
    $this->sortPopulation();
  }

  public function getFittestGenome() {
    return $this->genomes[0];
  }

  public function nextGeneration() {
    // Copy the elites to the new population.
    $new_genomes = $this->copyElite();

    // Produce offspring until new population is done.
  }

  protected function evaluatePopulation() {
    foreach ($this->genomes as $genome) {
      $genome->evaluate();
    }
  }

  protected function sortPopulation() {
    usort($this->genomes, function($a, $b) {
      /** @var \SimpleGA\Genome $a */
      /** @var \SimpleGA\Genome $b */
      $a_fitness = $a->getFitness();
      $b_fitness = $b->getFitness();
      if ($a_fitness == $b_fitness) {
        return 0;
      }

      return $a_fitness > $b_fitness;
    });
  }

  protected function copyElite() {

  }

}
