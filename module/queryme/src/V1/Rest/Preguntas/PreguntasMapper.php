<?php
namespace queryme\V1\Rest\Preguntas;

use OAuth2\Storage\Pdo;
use OAuth2\Storage\AccessTokenInterface as AccessTokenStorageInterface;
use OAuth2\Storage\RefreshTokenInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Update;
use Zend\Db\Adapter\Driver;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Zend\Db\Adapter\AdapterServiceFactory;
use Zend\Db\Adapter\AdapterAbstractServiceFactory;
use ZF\OAuth2\Factory\AuthControllerFactory;
use ZF\MvcAuth\Authentication\OAuth2Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use stdClass;
//use queryme\V1\Rest\OAuthAccess;
use Zend\Db\Sql\Predicate\IsNull;

class PreguntasMapper
{
	protected $adapter;
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	public function getPreguntas($empresa, $encuesta) {
		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('preguntas');
		$select->where(array(
			'empresa'  => $empresa,
			'encuesta' => $encuesta,
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$preguntas = $results->toArray();

		$json->success = true;
		$json->items   = $preguntas;
		return $json;
	}

	public function GraboPreguntas($data) {
		if ( $data->update == 'true') {
			// echo "ACTUALIZA";
			return $this->actualizaGraboPreguntas($data);
		}else {
			// echo "CREA";
			return $this->creaGraboPreguntas($data);
		}
	}

	public function creaGraboPreguntas($data)
	{
		$query = "SELECT max(idpreguntas) + 1 as idpregunta FROM preguntas";
		$sql2  = new Sql($this->adapter);
		$results     = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$idpanel     = $results->toArray();
		$id_pregunta = $idpanel['0']['idpregunta'];

		$headers = apache_request_headers ();
		$empresa = $headers['empresa'];
		$encuesta = $headers['encuesta'];

		try {
			$dataInsert = array(
				"idpreguntas" => $id_pregunta,
				"empresa"     => $empresa,
				"encuesta"    => $encuesta,
				"idpanel"     => $data->idpanel,
				"texto"       => $data->texto,
				"tipo"        => $data->tipo
			);
			$sql = new Sql($this->adapter);
			$insert = $sql->insert();
			$insert->into('preguntas');
			$insert->values($dataInsert);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
			$json = new stdClass();
			$json->success = true;
			return $json;
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo ingresar la pregunta.";
			return $json;
		}
	}

	public function actualizaGraboPreguntas($data)
	{
		$idpregunta = $data->idpreguntas;
		$idpanel    = $data->idpanel;
		$tipo       = $data->tipo;
		$texto      = $data->texto;
		$sql = new Sql($this->adapter);
		$update = $sql->update();
		$update->table('preguntas');
		$update->set(array(
			"idpanel" => $idpanel,
			"tipo"    => $tipo,
 			"texto"   => $texto
		));
		$update->where->equalTo("idpreguntas", $idpregunta);
		$updateString = $sql->getSqlStringForSqlObject($update);
		$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
		$json = new stdClass();
		$json->success = true;
		return $json;
	}
}
