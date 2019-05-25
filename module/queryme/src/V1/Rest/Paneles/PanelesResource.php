<?php
namespace queryme\V1\Rest\Paneles;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class PanelesResource extends AbstractResourceListener
{
  protected $mapper;
  public function __construct($mapper)
  {
    $this->mapper = $mapper;
  }

  /**
  * Create a resource
  *
  * @param  mixed $data
  * @return ApiProblem|mixed
  */
  public function create($data)
  {
    //return new ApiProblem(405, 'The POST method has not been defined');
    return $this->mapper->GraboPaneles($data);
  }

  /**
  * Delete a resource
  *
  * @param  mixed $id
  * @return ApiProblem|mixed
  */
  public function delete($id)
  {
    return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    // return $this->mapper->delete($id);
  }

  /**
  * Delete a collection, or members of a collection
  *
  * @param  mixed $data
  * @return ApiProblem|mixed
  */
  public function deleteList($data)
  {
    return new ApiProblem(405, 'The DELETE method has not been defined for collections');
  }

  /**
  * Fetch a resource
  *
  * @param  mixed $id
  * @return ApiProblem|mixed
  */
  public function fetch($id)
  {
    return new ApiProblem(405, 'The GET method has not been defined for individual resources');
  }

  /**
  * Fetch all or a subset of resources
  *
  * @param  array $params
  * @return ApiProblem|mixed
  */
  public function fetchAll($params = [])
  {
    //        return new ApiProblem(405, 'The GET method has not been defined for collections');
    $headers = apache_request_headers ();

    $empresa = $headers['empresa'];
    $encuesta = $headers['encuesta'];

    return $this->mapper->getPaneles($empresa, $encuesta);

  }

  /**
  * Patch (partial in-place update) a resource
  *
  * @param  mixed $id
  * @param  mixed $data
  * @return ApiProblem|mixed
  */
  public function patch($id, $data)
  {
    return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
  }

  /**
  * Patch (partial in-place update) a collection or members of a collection
  *
  * @param  mixed $data
  * @return ApiProblem|mixed
  */
  public function patchList($data)
  {
    return new ApiProblem(405, 'The PATCH method has not been defined for collections');
  }

  /**
  * Replace a collection or members of a collection
  *
  * @param  mixed $data
  * @return ApiProblem|mixed
  */
  public function replaceList($data)
  {
    return new ApiProblem(405, 'The PUT method has not been defined for collections');
  }

  /**
  * Update a resource
  *
  * @param  mixed $id
  * @param  mixed $data
  * @return ApiProblem|mixed
  */
  public function update($id, $data)
  {
    return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
  }
}
