<?php
/**
 * @file
 * Population class.
 */

namespace SimpleGA;


use Pimple\Container;

/**
 * Class Population
 *
 * Class for handling a population of genomes.
 *
 * @package SimpleGA
 */
class Population implements PopulationInterface {

  /** @var \Pimple\Container $container */
  protected $container;

  /** @var Genome[] $genomes */
  protected $genomes = [];

  /** @var int $currentGeneration */
  protected $currentGeneration = 0;

  /**
   * @var int $sum
   *   Sum of all fitnesses.
   */
  protected $sum = 0;

  /**
   * @var int $max
   *   Max fitness.
   */
  protected $max = 0;

  /**
   * Population constructor.
   *
   * @param \Pimple\Container $container
   */
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

    // Calculate sum of all fitnesses.
    $this->calculateSum();
  }

  /**
   * @return \SimpleGA\Genome
   *   Get fittest genome.
   */
  public function getFittestGenome() {
    return $this->genomes[0];
  }

  /**
   * @return int
   *   Get current generation.
   */
  public function getGeneration() {
    return $this->currentGeneration;
  }

  /**
   * Perform next generation.
   *
   * Process:
   *  - Copy elite, i.e. n most fit genomes are copied to next generation.
   *  - Produce new offspring from parents selected by fitness and randomness.
   *  - Mutate some genomes.
   *  - Evaluate all genomes.
   *  - Sort genomes by fitness.
   *  - Calculate sum of fitnesses and max fitness.
   *
   * @return bool
   *   Return TRUE if new generation was generated, FALSE if we have reached
   *   the final generation.
   */
  public function nextGeneration() {
    if (++$this->currentGeneration > $this->container['generations']) {
      return FALSE;
    }
    // Copy the elites to the new population.
    $new_genomes = $this->copyElite();

    // Produce offspring, mutate, evaluate, etc.
    $this->produceOffspring($new_genomes);
    $this->mutatePopulation();
    $this->evaluatePopulation();
    $this->sortPopulation();
    $this->calculateSum();

    return TRUE;
  }

  /**
   * Produce offspring.
   *
   * Select two new parents and perform crossover to produce two new children,
   * until we have reached the configured population size.
   *
   * @param $new_genomes
   *   Existing genomes.
   */
  protected function produceOffspring($new_genomes) {
    $random = $this->container['random_generator'];

    // Produce offspring until new population is done.
    $population_size = $this->container['population_size'];
    while (count($new_genomes) < $population_size) {
      // Find two parents.
      $first_parent = $this->findParent();
      $second_parent = $this->findParent();

      // Select where to split genomes.
      $split_a = $random();
      $split_b = $random();
      // Make sure the two split points are different.
      while ($split_a == $split_b) {
        $split_b = $random();
      }
      $split = [$split_a, $split_b];
      sort($split);

      /** @var Genome $first_child */
      $first_child = $this->container['genome'];
      /** @var Genome $second_child */
      $second_child = $this->container['genome'];

      // Produce new genes.
      $first_child_parts = [$first_parent->getPart(1, $split[0] - 1), $second_parent->getPart($split[0], $split[1]), $first_parent->getPart($split[1] + 1, 8)];
      $second_child_parts = [$second_parent->getPart(1, $split[0] - 1), $first_parent->getPart($split[0], $split[1]), $second_parent->getPart($split[1] + 1, 8)];

      // Generate new child genomes from new genes.
      $first_child->generate($first_child_parts);
      $second_child->generate($second_child_parts);

      // Clone children and mutate.
      $third_child = clone $first_child;
      $third_child->mutate();
      $fourth_child = clone $second_child;
      $fourth_child->mutate();

      // Save genomes.
      $new_genomes[] = $first_child;
      $new_genomes[] = $second_child;
      $new_genomes[] = $third_child;
      $new_genomes[] = $fourth_child;
    }
    $this->genomes = $new_genomes;
  }

  /**
   * Mutate population.
   *
   * Go through all genomes and generate a random number. If the random number
   * is below the configured mutation threshold (in promilles), mutate genome.
   */
  protected function mutatePopulation() {
    $mutation = $this->container['mutation_promille'];
    foreach ($this->genomes as $genome) {
      $rnd = rand(0, 1000);
      if ($rnd < $mutation) {
        $genome->mutate();
      }
    }
  }

  /**
   * Evaluate fitness for all genomes in population.
   */
  protected function evaluatePopulation() {
    foreach ($this->genomes as $genome) {
      $genome->evaluate();
    }
  }

  /**
   * Sort population according to fitness (lower fitness is better).
   */
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

  /**
   * Calculate fitness sum and max sum.
   */
  protected function calculateSum() {
    $sum = 0;
    foreach ($this->genomes as $genome) {
      if ($genome->getFitness() > $this->max) {
        $this->max = $genome->getFitness();
      }
    }

    // Generate sum by subtracting fitness from max fitness and add 1.
    // This ensures that lower fitnesses will get a higher select rate when
    // finding parents to produce offspring.
    foreach ($this->genomes as $genome) {
      $sum = $sum + $this->max - $genome->getFitness() + 1;
    }
    $this->sum = $sum;
  }

  /**
   * Copy the n most fit genomes to the next generation.
   *
   * @return array
   *   Array of genomes.
   */
  protected function copyElite() {
    $elite = [];
    for ($i = 0; $i < $this->container['elite_count']; $i++) {
      $elite[] = $this->genomes[$i];
    }
    return $elite;
  }

  /**
   * Find parent by random.
   *
   * The better the fitness, the more likely the genome will be selected.
   * This works linearly.
   *
   * @return \QueensGA\QueensGenome|\SimpleGA\Genome
   *   Return found genome.
   *
   * @throws \SimpleGA\SimpleGAException
   *   Throws exception if no genome is selected. This will only happen, if
   *   the genome data is corrupted, the fitness sum is wrong, or the max
   *   fitness is wrong.
   */
  protected function findParent() {
    $sum = $this->sum;
    $random = random_int(0, $sum);
    $current = 0;
    $max = $this->max;

    foreach ($this->genomes as $genome) {
      /** @var \QueensGA\QueensGenome $genome */
      $val = $max - $genome->getFitness() + 1;
      $current += $val;
      if ($current >= $random) {
        return $genome;
      }
    }

    throw new SimpleGAException('No genome found. This means that data is corrupted.', SIMPLEGA_NO_GENOME_FOUND);
  }
}
