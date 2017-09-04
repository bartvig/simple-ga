<?php
/**
 * @file
 * Islands interface
 */

namespace SimpleGA;


use Pimple\Container;

interface IslandsInterface {

  public function __construct(Container $container);

  public function initialize();

  public function nextGeneration();

  public function getGeneration();

  public function getFittestGenome();

  public function exchangeGenomes();

}
