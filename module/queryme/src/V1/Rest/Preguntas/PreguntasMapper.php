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
use Zend\Http\Response;
use Zend\Http\Response\Stream;

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
		//$select->join(array("paneles" => "paneles"),'paneles.idpanel = preguntas.idpanel',array('*'),'inner');
		$select->where(array(
			'preguntas.empresa'  => $empresa,
			'preguntas.encuesta' => $encuesta,
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$preguntas = $results->toArray();

		foreach($preguntas as $key => $row) {
			$panel = $this->buscoPanel($row ['idpanel']);
			$arr_preg[] = array(
				'idpanel' => $row ['idpanel'],
				'encuesta' => $row ['encuesta'],
				'empresa' => $row ['empresa'],
				'validator' => $row ['validator'],
				'tipo' => $row ['tipo'],
				'texto' => $row ['texto'],
				'idpreguntas' => $row ['idpreguntas'],
				'estado' => $row ['estado'],
				'paneltexto' => $panel,
			);

		}

/*
		//obtiene el total de resultados
		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('resultados');
		$select->where(array(
			'resultados.empresa'  => $empresa,
			'resultados.encuesta' => $encuesta,
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$resultados = $results->toArray();
		$cantidadresultados = count($resultados);

		//obtiene el total de preguntas
		$sql3 = new Sql($this->adapter);
		$select = $sql3->select();
		$select->from('preguntas');
		$select->where(array(
			'preguntas.empresa'  => $empresa,
			'preguntas.encuesta' => $encuesta,
		));
		$selectString = $sql3->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$preguntas = $results->toArray();
		$cantidadpreguntas = count($preguntas);

		$cantidadEncuestados = $cantidadresultados/$cantidadpreguntas;
		*/

		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('resultados');
		$select->where(array(
						'resultados.empresa'  => $empresa,
						'resultados.encuesta' => $encuesta,
						'resultados.idpregunta' => 1,
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$resultados = $results->toArray();
		$cantidadEncuestados = count($resultados);


		$json->success = true;
		$json->cantidadEncuestados = $cantidadEncuestados;
		$json->items   = $arr_preg;
		return $json;
	}

	function buscoPanel($idpanel)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('paneles');
		$select->where(array('idpanel' => $idpanel));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$p = $results->toArray();

		if (!empty($p[0]['texto'])) {
			$panel = $p[0]['texto'];
			return $panel;
		}else {
			$panel = "-";
			return $panel;
		}
	}
	public function getPregXPanel($idpanel) {
		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('preguntas');
		$select->where(array(
			'idpanel'  => $idpanel
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$preguntas = $results->toArray();

		$json->success = true;
		$json->items   = $preguntas;
		return $json;
	}

	public function GraboPreguntas($data) {
		if ( $data->delete == 'true') {
			//BORRA PREGUNTA
			return $this->borrarPregunta($data);
		}

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
		$query       = "SELECT max(idpreguntas) + 1 as idpregunta FROM preguntas";
		$sql2        = new Sql($this->adapter);
		$results     = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$idpanel     = $results->toArray();
		$id_pregunta = $idpanel['0']['idpregunta'];

		if (empty($id_pregunta)) {
			$id_pregunta = 1;
		}

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

	public function borrarPregunta($data)
	{
		$id = $data->idpregunta;
		try {
			$sql = new Sql($this->adapter);
			$select = $sql->select();
			$select->from('preguntas');
			$select->where('idpreguntas = '.$id);
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$pregunta = $results->toArray();

			if (!empty($pregunta)) {
				$sql = new Sql($this->adapter);
				$delete = $sql->delete();
				$delete->from('preguntas');
				$delete->where(array(
					'idpreguntas' => $id
				));
				$deleteString = $sql->getSqlStringForSqlObject($delete);
				$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
				$oResponse = new Response();
				$response = new stdClass;
				$response->success = true;
				$response->msg = "Pregunta eliminada.";
				$oResponse->setContent(json_encode($response));
				return $oResponse;
			}else{
				$oResponse = new Response();
				$response = new stdClass;
				$response->success = false;
				$response->msg = "La pregunta no puede ser eliminada.";
				$oResponse->setContent(json_encode($response));
				return $oResponse;
			}
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo eliminar la pregunta.";
			return $json;
		}
	}
}
