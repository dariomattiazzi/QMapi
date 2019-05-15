<?php
namespace queryme\V1\Rest\Preguntas;

class PreguntasResourceFactory
{
  public function __invoke($services)
  {
      $mapper = $services->get('queryme\V1\Rest\Preguntas\PreguntasMapper');
      return new PreguntasResource($mapper);
  }
}
