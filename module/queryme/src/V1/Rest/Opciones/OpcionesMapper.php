<?php
namespace queryme\V1\Rest\Opciones;

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

class OpcionesMapper
{
	protected $adapter;
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}
	public function getData($oppr){
		//print_r($oppr);
		$data ="";
		foreach($oppr as $val){

			if ($val == end($oppr)){
				$data .= "{ value".$oppr[0][idpregunta].": ".$val[idopcion].", display".$oppr[0][idpregunta].": '".$val[valorOpcion]."'}";
			}else{
				$data .= "{ value".$oppr[0][idpregunta].": ".$val[idopcion].", display".$oppr[0][idpregunta].": '".$val[valorOpcion]."'},";
			}
		}
		//die;
		return $data;
	}

	public function getOpciobesXPreg($id_pregunta) {
		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('opciones');
		$select->where(array(
			'idpregunta'  => $id_pregunta
		));
		$selectString = $sql2->getSqlStringForSqlObject($select);
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$opciones = $results->toArray();

		$json->success = true;
		$json->items   = $opciones;
		return $json;
	}

	public function GraboOpciones($data) {
		if ( $data->update == 'true') {
			return $this->actualizaGraboOpciones($data);
		}else {
			return $this->creaGraboOpciones($data);
		}
	}

	public function creaGraboOpciones($data)
	{
		$query = "SELECT max(idopciones) + 1 as idopciones FROM opciones";
		$sql2  = new Sql($this->adapter);
		$results     = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$idopciones  = $results->toArray();
		$idopciones  = $idopciones['0']['idopciones'];

		$query = "SELECT max(idopcion) + 1 as idopcion FROM opciones WHERE idpregunta = $data->idpregunta";
		$sql2  = new Sql($this->adapter);
		$results   = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$idopcion  = $results->toArray();
		$idopcion  = $idopcion['0']['idopcion'];

		$headers = apache_request_headers ();
		$empresa = $headers['empresa'];
		$encuesta = $headers['encuesta'];

		try {
			$dataInsert = array(
				"idopciones" => $idopciones,
				"idpregunta" => $data->idpregunta,
				"idopcion"   => $idopcion,
				"valorOpcion"     => $data->valorOpcion,
				"empresa"     => $empresa,
				"encuesta"    => $encuesta
			);
			//{ "update":false,"idpregunta":"0", "valorOpcion":"La sala", "empresa": 1, "encuesta": 1 }
			$sql = new Sql($this->adapter);
			$insert = $sql->insert();
			$insert->into('opciones');
			$insert->values($dataInsert);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
			$json = new stdClass();
			$json->success = true;
			return $json;
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo ingresar la Opcion.";
			return $json;
		}
	}

	public function actualizaGraboOpciones($data)
	{

		$headers = apache_request_headers ();
		$empresa = $headers['empresa'];
		$encuesta = $headers['encuesta'];

		$dataUpdate = array(
			"idpregunta"  => $data->idpregunta,
			"valorOpcion" => $data->valorOpcion,
			"empresa"     => $empresa,
			"encuesta"    => $encuesta
		);

		$sql = new Sql($this->adapter);
		$update = $sql->update();
		$update->table('opciones');
		$update->set($dataUpdate);
		$update->where->equalTo("idopciones", $data->idopciones);
		$updateString = $sql->getSqlStringForSqlObject($update);
		$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
		$json = new stdClass();
		$json->success = true;
		return $json;
	}

	public function getOpciones($empresa, $encuesta) {

		$string= "";
		$sql2 = new Sql($this->adapter);
		$select = $sql2->select();
		$select->from('opciones');
		$select->where(array(
			'empresa'  => 1,
			'encuesta' => 1,
		));
		//$select->group('idpregunta);
		$selectString = $sql2->getSqlStringForSqlObject($select);

		$selectString = $selectString ." group by idpregunta";
		//print_r($selectString);die;
		$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$opciones = $results->toArray();
		//print_r($opciones );die;
		foreach($opciones as $op){

			$sql2 = new Sql($this->adapter);
			$select = $sql2->select();
			$select->from('opciones');
			$select->where(array(
				'empresa'  => 1,
				'encuesta' => 1,
				'idpregunta' =>$op[idpregunta]
			));

			$selectString = $sql2->getSqlStringForSqlObject($select);
			//$selectString = $selectString ." group by'idpregunta','idopcion";
			//print_r($selectString);die;

			$results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$oppr = $results->toArray();
			$string .= "Ext.define('Query.store.store".$oppr[0][idpregunta]."', {
				extend: 'Ext.data.Store',
				alias: 'store.store".$oppr[0][idpregunta]."',
				storeId:'store".$oppr[0][idpregunta]."',
				fields: [
					'value".$oppr[0][idpregunta]."', 'display".$oppr[0][idpregunta]."'
				],
				autoLoad:false,

				data: { items: [ ". $this->getData($oppr)."]}    ,
				proxy: {
					type: 'memory',
					reader: {
						type: 'json',
						rootProperty: 'items'
					}
				}
			});
			Ext.create('Query.store.store".$oppr[0][idpregunta]."');";

		}
		print_r($string);die;
	}
}
