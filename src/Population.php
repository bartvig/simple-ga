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

  protected $genomes = [];

  public function __construct(Container $container) {
    $this->container = $container;
  }

  public function initialize() {
    $size = $this->container['population_size'];
    for ($i = 0; $i < $size; $i++) {
      $genome = $this->container['genome'];
      $genome->generate();
      $genomes[] = $genome;
    }
  }

}
