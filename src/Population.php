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

    $random = $this->container['random_generator'];
    // Produce offspring until new population is done.
    $population_size = $this->container['population_size'];
    while (count($new_genomes) < $population_size) {
      // Find two parents.
      $first_parent = $this->findParent();
      $second_parent = $this->findParent();

      // Produce two offspring for these two parents.
      $split_a = $random();
      $split_b = $random();
      while ($split_a == $split_b) {
        $split_b = $random();
      }
      $split = [$split_a, $split_b];
      sort($split);

      $first_child = $first_parent->getPart(1, $split[0] - 1) + $second_parent->getPart($split[0], $split[1]) + $first_parent->getPart($split[1], 8);
      $second_child = $second_parent->getPart(1, $split[0] - 1) + $first_parent->getPart($split[0], $split[1]) + $second_parent->getPart($split[1], 8);
      $new_genomes[] = $first_child;
      $new_genomes[] = $second_child;
    }
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
    $elite = [];
    for ($i = 0; $i < $this->container['elite_count']; $i++) {
      $elite[] = $this->genomes[$i];
    }
    return $elite;
  }

  protected function findParent() {
    $max = 65535;
    $random = random_int(0, $max);
    $current = 0;

    foreach ($this->genomes as $genome) {
      /** @var \QueensGA\QueensGenome $genome */
      $val = $max / ($genome->getFitness() + 1);
      $current += $val;
      if ($current >= $random) {
        return $genome;
      }
    }
  }

}
