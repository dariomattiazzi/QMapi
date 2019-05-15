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
}
