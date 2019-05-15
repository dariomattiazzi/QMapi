<?php
namespace queryme\V1\Rest\Resultados;

class ResultadosResourceFactory
{
  public function __invoke($services)
  {
      $mapper = $services->get('queryme\V1\Rest\Resultados\ResultadosMapper');
      return new ResultadosResource($mapper);
  }
}
