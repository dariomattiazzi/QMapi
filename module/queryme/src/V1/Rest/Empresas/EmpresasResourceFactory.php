<?php
namespace queryme\V1\Rest\Empresas;

class EmpresasResourceFactory
{
  public function __invoke($services)
  {
      $mapper = $services->get('queryme\V1\Rest\Empresas\EmpresasMapper');
      return new EmpresasResource($mapper);
  }
}
