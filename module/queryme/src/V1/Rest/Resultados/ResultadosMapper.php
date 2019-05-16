<?php
namespace queryme\V1\Rest\Resultados;

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

class ResultadosMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function getResultados($empresa, $encuesta) {
 $json = '{
	"success": true,
	"data": [{
		"os": "Si",
		"data1": 67.3
	}, {
		"os": "Pocas veces",
		"data1": 2.7
	}, {
		"os": "No",
		"data1": 17.9
	}]
}';
	die($json);
//select count(idpregunta) from resultados where empresa =1 and encuesta =1 and idpregunta = 0;
//select count(res.respuesta)as cantidad, res.respuesta from resultados res where res.idpregunta =  0 and empresa =1 and encuesta =1 group by res.respuesta,res.idpregunta;
	return $json;
  }

  public function putResultados($empresa, $encuesta, $data) {
//print_r($data);die;
    foreach ($data as $c => $v){
      $id_preg   = substr ( $c, 5);
      $respuesta = $v;

      // obtengo el prox. nro encuestado
      $r2 = $this->adapter->query("SELECT MAX(encuestado)+1 AS proximo FROM resultados", Adapter::QUERY_MODE_EXECUTE)or die("{ \"success\": false, \"msg\": \"Error Al conectar a la DB.\"}");
      $last = $r2->toArray();
      $encuestado = $last[0]['proximo'];

      try {
        $dataInsert = array(
          "idpregunta" => $id_preg,
          "respuesta"  => $respuesta,
          'fecha'      => date("d-m-y H:i:s"),
          "encuestado" => $encuestado,
          "empresa"    => $empresa,
          "encuesta"   => $encuesta,
        );
        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('resultados');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        //echo $insertString; die;
        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);

      } catch (Exception $e) {
        $json = new stdClass();
        $json->success = false;
        $json->msg = "No se pudo ingresar el resultado.";
        return $json;
      }

    }
    $json = new stdClass();
    $json->success = true;
    $json->msg     = "Respuestas grabadas correctamente.";
    return $json;

  }
}
