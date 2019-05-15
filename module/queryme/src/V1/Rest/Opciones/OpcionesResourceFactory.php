<?php
namespace queryme\V1\Rest\Opciones;

class OpcionesResourceFactory
{
  public function __invoke($services)
  {
      $mapper = $services->get('queryme\V1\Rest\Opciones\OpcionesMapper');
      return new OpcionesResource($mapper);
  }}
