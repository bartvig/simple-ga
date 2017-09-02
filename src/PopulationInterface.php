<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 8/31/17
 * Time: 10:11 AM
 */

namespace SimpleGA;


use Pimple\Container;

interface PopulationInterface {

  public function __construct(Container $container);

  public function initialize();

  public function nextGeneration();

  public function getGeneration();

  public function getFittestGenome($count);

  public function exportGenomes($count);

  public function importGenomes($genomes);

}
