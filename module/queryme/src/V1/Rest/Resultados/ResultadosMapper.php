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

  public function getResultados($empresa, $encuesta, $id_pregunta) {

    $sql2 = new Sql($this->adapter);
    $select = $sql2->select();
    $select->from('preguntas');
    $select->where(array(
      'empresa'     => $empresa,
      'encuesta'    => $encuesta,
      'idpreguntas' => $id_pregunta
    ));
    $selectString = $sql2->getSqlStringForSqlObject($select);
    $results  = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $pregunta = $results->toArray();

    $tipo = $pregunta['0']['tipo'];

    switch ($tipo) {
      case 'selectfield':
      $query = "SELECT
      queryme.resultados.idpregunta,
      queryme.preguntas.texto,
      queryme.resultados.respuesta,
      queryme.opciones.valorOpcion
      FROM
      queryme.resultados
      INNER JOIN queryme.preguntas ON  (queryme.resultados.idpregunta = queryme.preguntas.idpreguntas)
      INNER JOIN queryme.opciones ON (queryme.resultados.idpregunta = queryme.opciones.idpregunta)
      WHERE
      queryme.preguntas.tipo = 'selectfield'
      AND queryme.resultados.respuesta = queryme.opciones.idopcion
      AND queryme.resultados.idpregunta = $id_pregunta
      ORDER BY queryme.resultados.idpregunta;";

      $sql2 = new Sql($this->adapter);
      $results  = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
      $resultados = $results->toArray();
      //print_r($resultados); die;

      foreach ($resultados as $key => $value) {
        $arr_respXpreg[$value['respuesta']][] = $value;
      }

      $tot_respuestas = count($resultados);

      foreach ($arr_respXpreg as $key => $value) {
        $porciento = count($arr_respXpreg[$key]);

        //  echo $tot_respuestas . " - " . $porciento; die;
        $porcentaje = number_format($porciento*100/$tot_respuestas , 2);

        $t['totalpreg'] = $porciento;
        $t['data1']     = $porcentaje;
        $t['os']        = $value['0']['valorOpcion'];

        $tot_respuestas_por_preg[]= $t;
      }

      //echo "\n".$tot_respuestas_por_preg."\n";
      //print_r($tot_respuestas_por_preg); die;
      $json->success = true;
      $json->type    = "cerrada";
      $json->data    = $tot_respuestas_por_preg;
      return $json;
      break;

      case 'textfield':
      // $query = "SELECT
      // queryme.resultados.idpregunta,
      // queryme.preguntas.texto,
      // queryme.resultados.respuesta
      // FROM
      // queryme.resultados
      // INNER JOIN queryme.preguntas ON  (queryme.resultados.idpregunta = queryme.preguntas.idpreguntas)
      // WHERE queryme.preguntas.tipo = 'textfield'
      // AND queryme.resultados.idpregunta = $id_pregunta
      // ORDER BY queryme.resultados.idpregunta;";
      $query = "SELECT
            pruebaQM.resultados.idpregunta,
            pruebaQM.preguntas.texto,
            pruebaQM.resultados.respuesta
            FROM
            pruebaQM.resultados
            INNER JOIN pruebaQM.preguntas ON  (pruebaQM.resultados.idpregunta = pruebaQM.preguntas.idpreguntas)
            WHERE pruebaQM.preguntas.tipo = 'textfield'
            AND pruebaQM.resultados.idpregunta = $id_pregunta
            ORDER BY pruebaQM.resultados.idpregunta;";
      $sql2 = new Sql($this->adapter);
      $results  = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);
      $resultados = $results->toArray();

      $i = 0;
      $total_respuestas = count($resultados);
      $tot_respuestas_por_preg['totalpreg'] = $total_respuestas;

      foreach ($resultados as $key => $value) {
        if (!empty($value['respuesta'])) {
          $i++;
          $tot_respuestas_por_preg[] = array("id" => "$i", "respuesta" => $value['respuesta']);
        }
      }

      $json->success = true;
      $json->type = "abierta";
      $json->data   = $tot_respuestas_por_preg;
      return $json;
      break;
      default:
      $json->success = true;
      $json->data   = "tipo de pregunta incorrecto";
      return $json;
    }
  }

  public function putResultados($empresa, $encuesta, $data) {
    foreach ($data as $c => $v){
      $id_preg   = substr ( $c, 5);
      $respuesta = $v;

      // obtengo el prox. nro encuestado
      $r2 = $this->adapter->query("SELECT MAX(encuestado)+1 AS proximo FROM resultados", Adapter::QUERY_MODE_EXECUTE)or die("{ \"success\": false, \"msg\": \"Error Al conectar a la DB.\"}");
      $last = $r2->toArray();
      $encuestado = $last[0]['proximo'];

      if (empty($encuestado)) {
  			$encuestado = 0;
  		}

      try {
        $dataInsert = array(
          "idpregunta" => $id_preg,
          "respuesta"  => $respuesta,
          'fecha'      => date("y-m-d H:i:s"),
          "encuestado" => $encuestado,
          "empresa"    => $empresa,
          "encuesta"   => $encuesta,
        );
        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('resultados');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);

      } catch (Exception $e) {
        $json = new stdClass();
        $json->success = false;
        die($json);
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
