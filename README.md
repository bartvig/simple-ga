# simple-ga
Simple Genetic Algorithm Implementation in PHP

This is a rather quick and dirty implementation of genetic algorithms with a generic genome and a population of genomes.

Some elements such as population size, how many of the best genomes to copy to the next generation, number of new random genomes for each generation, and mutation quotient can be configured with dependency injection (with Pimple).

Other customizations can be performed by overriding the classes `Genome` and `Population`.

For an example of how to use SimpleGA, see https://github.com/bartvig/queens-ga

## Installation
Use composer to install required libraries: `composer install`. Then you might have to run `composer dump-autoload -o` to make autoloading work properly.

Installation of composer is out of scope for this documentation.

## How to run
This is a generic implementation and can't be run on its own. See https://github.com/bartvig/queens-ga for an example of how to use this implementation.

## Configuration
These variables must be configured in a Pimple container:
- `population_size`: number of genomes in a population.
- `population_random`: how many genomes to generate randomly for each population. This can be useful to introduce new random genes in a population.
- `elite_count`: number of best genomes to copy to the next generation.
- `mutation_promille`: mutation rate in thousandths, e.g. a mutation rate of 5% is `50`.
- `genome`: generation of a specific genome implementation. This should return a new instance of a genome class. Example (when the Pimple container is called `$container`):
  `$container['genome'] = $container->factory(function ($c) {
  return new \QueensGA\QueensGenome($c); });`

This variable can be configured:
- `evaluate_points`: this is an associative array of keys and values to evaluate genomes against. E.g. some points in a quadratic equation: `[ 0 => 0, 2 => 4, 4 => 16 ]`.

## Genome
A genome is a proposed solution to the problem we're trying to solve.

### These functions must be overridden
`Genome::mutate()` and `Genome::evaluate()` don't do anything and must be overridden by a specific implementation of a problem to be solved with the genetic algorithm.

`mutate()` should change one or more genes in a genome by random. There is no default mutation.

`evaluate()` has to evaluate the fitness of the genome - the more fit the genome, the lower the value. 0 is the lowest allowed fitness.

### Optional functions to override
By default the genes of a genome are elements in an array. This makes crossover and producing offspring easy, as we only need to slice and merge arrays. You can override `Genome::generate()` and `Genome::getPart()`, if you want the genes to be constructed as something else than an array.

If you want to output a genome in each generation or when the execution is done, you should override `Genome::toString()`.

## Population
A population of genomes is handled by generating genomes, sorting genomes according to fitness, producing new genomes, and mutate genomes.

This class doesn't need to be overridden and handles the basics. `Population::initialize()` initializes the population according to the configuration. For each new generation, call `Population::nextGeneration()`. This function performs the following steps, using values from the configuration:
1. Copy most `elite_count` fit genomes to the next generation.
1. Produce offspring. More fit genomes are more likely to be selected for crossover. Two genomes are selected and two crossover points are chosen by random to generate two half genomes for each parent. These half genomes are mixed to produce two children.
1. Evaluate entire population. If `evaluate_points` is configured, use these values to evaluate the genomes. Else the genomes are evaluated on their own without external values.

When a generation is complete, you can get the most fit genome with `Population::getFittestGenome()`.
