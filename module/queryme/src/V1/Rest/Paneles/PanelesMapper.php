<?php
namespace queryme\V1\Rest\Paneles;

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

class PanelesMapper
{
	protected $adapter;
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	public function getPaneles($empresa, $encuesta) {
		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('paneles');
		$select->where(array(
			'empresa'  => $empresa,
			'encuesta' => $encuesta,
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$paneles = $results->toArray();

		$json->success = true;
		$json->items   = $paneles;
		return $json;
	}

	public function GraboPaneles($data) {
		if ( $data->delete == 'true') {
			return $this->borrarPaneles($data);
		}else{

		}
		if ( $data->update == 'true') {
			// echo "ACTUALIZA";
			return $this->actualizaGraboPaneles($data);
		}else {
			// echo "CREA";
			return $this->creaGraboPaneles($data);
		}

	}

	public function creaGraboPaneles($data)
	{
		$query = "SELECT max(idpanel) + 1 as idpanel FROM paneles";
		$sql2 = new Sql($this->adapter);
		$results  = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$idpanel = $results->toArray();
		$idpanel = $idpanel['0']['idpanel'];
		try {
			$dataInsert = array(
				"idpanel" => $idpanel,
				"texto" => $data->texto
			);
			$sql = new Sql($this->adapter);
			$insert = $sql->insert();
			$insert->into('paneles');
			$insert->values($dataInsert);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
			$json = new stdClass();
			$json->success = true;
			return $json;
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo ingresar el panel.";
			return $json;
		}
	}

	public function actualizaGraboPaneles($data)
	{
		$idpanel = $data->idpanel;
		$texto = $data->texto;
		$sql = new Sql($this->adapter);
		$update = $sql->update();
		$update->table('paneles');
		$update->set(array(
			"texto"   => $texto
		));
		$update->where->equalTo("idpanel", $idpanel);
		$updateString = $sql->getSqlStringForSqlObject($update);
		$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
		$json = new stdClass();
		$json->success = true;
		return $json;
	}

	public function borrarPaneles($data)
	{
		$id = $data->idpanel;
		try {
			$sql = new Sql($this->adapter);
			$select = $sql->select();
			$select->from('paneles');
			$select->where('idpanel = '.$id);
			$selectString = $sql->getSqlStringForSqlObject($select);

			$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$panel = $results->toArray();

			if (!empty($panel)) {
				$sql = new Sql($this->adapter);
				$delete = $sql->delete();
				$delete->from('paneles');
				$delete->where(array(
					'idpanel' => $id
				));
				$deleteString = $sql->getSqlStringForSqlObject($delete);

				$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
				$oResponse = new Response();
				$response = new stdClass;
				$response->success = true;
				$response->msg = "Panel eliminado.";
				$oResponse->setContent(json_encode($response));
				return $oResponse;
			}else{
				$oResponse = new Response();
				$response = new stdClass;
				$response->success = false;
				$response->msg = "El panel no puede ser eliminado.";
				$oResponse->setContent(json_encode($response));
				return $oResponse;
			}
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo eliminar el panel.";
			return $json;
		}
	}
}
