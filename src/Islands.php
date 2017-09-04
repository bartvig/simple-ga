<?php
/**
 * @file
 * Islands class
 */

namespace SimpleGA;


use Pimple\Container;

class Islands implements IslandsInterface {

  /** @var \Pimple\Container $container */
  protected $container;

  /** @var int $currentGeneration */
  protected $currentGeneration = 1;

  /** @var \SimpleGA\Population[] $islands */
  protected $islands = [];

  /** @var \SimpleGA\Genome[] $most_fit */
  protected $most_fit = [];

  public function __construct(Container $container) {
    $this->container = $container;
  }

  public function initialize() {
    $islands_count = $this->container['islands_count'];

    $most_fit = [];
    for ($i = 0; $i < $islands_count; $i++) {
      $population = new Population($this->container);
      if (isset($this->container['islands_highest_promille'])) {
        $mutation_lo = $this->container['mutation_promille'];
        $mutation_hi = $this->container['islands_highest_promille'];
        $mutation_new = ($mutation_hi - $mutation_lo) / $islands_count * $i + $mutation_lo;
        $population->setMutation($mutation_new);
      }
      $population->initialize();
      $this->islands[] = $population;
      $most_fit[] = $population->getFittestGenome();
    }
    $this->most_fit = $most_fit;
    $this->sortIslands();
  }

  public function getFittestGenome() {
    return $this->most_fit[0];
  }

  public function getGeneration() {
    return $this->currentGeneration;
  }

  public function nextGeneration() {
    $this->currentGeneration++;

    $islands_exchange_generations = isset($this->container['islands_exchange_generations']) ? $this->container['islands_exchange_generations'] : 0;
    if ($islands_exchange_generations && $this->currentGeneration % $islands_exchange_generations == 0) {
      $this->exchangeGenomes();
    }

    $most_fit = [];

    foreach ($this->islands as $island) {
      $island->nextGeneration();
      $most_fit[] = $island->getFittestGenome();
    }

    $this->most_fit = $most_fit;
    $this->sortIslands();
    return $this->currentGeneration;
  }

  protected function sortIslands() {
    usort($this->most_fit, function($a, $b) {
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

  public function exchangeGenomes() {
    $exchange_count = $this->container['islands_exchange_count'];

    $exchanged_genomes = [];

    foreach ($this->islands as $island) {
      $genomes = $island->getFittestGenome($exchange_count);
      $exchanged_genomes[] = array_merge($exchanged_genomes, $genomes);
    }

    foreach ($exchanged_genomes as $genome) {
      // Choose island to inject genome to.
      $rnd = rand(0, count($this->islands) - 1);
      $this->islands[$rnd]->importGenomes([$genome]);
    }

  }

}
