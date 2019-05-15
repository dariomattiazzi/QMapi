<?php
namespace queryme\V1\Rest\Paneles;

class PanelesResourceFactory
{
  public function __invoke($services)
  {
      $mapper = $services->get('queryme\V1\Rest\Paneles\PanelesMapper');
      return new PanelesResource($mapper);
  }}
