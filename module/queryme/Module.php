<?php
namespace queryme;

use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'queryme\V1\Rest\Empresas\EmpresasMapper' => function ($sm2) {
                    $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                    return new \queryme\V1\Rest\Empresas\EmpresasMapper($adapter2);
                },
                'queryme\V1\Rest\Paneles\PanelesMapper' => function ($sm2) {
                    $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                    return new \queryme\V1\Rest\Paneles\PanelesMapper($adapter2);
                },
                'queryme\V1\Rest\Preguntas\PreguntasMapper' => function ($sm2) {
                    $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                    return new \queryme\V1\Rest\Preguntas\PreguntasMapper($adapter2);
                },
                'queryme\V1\Rest\Respuestas\RespuestasMapper' => function ($sm2) {
                    $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                    return new \queryme\V1\Rest\Respuestas\RespuestasMapper($adapter2);
                },
                'queryme\V1\Rest\Resultados\ResultadosMapper' => function ($sm2) {
                    $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                    return new \queryme\V1\Rest\Resultados\ResultadosMapper($adapter2);
                },
                'queryme\V1\Rest\Opciones\OpcionesMapper' => function ($sm2) {
                    $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                    return new \queryme\V1\Rest\Opciones\OpcionesMapper($adapter2);
                }

            )
        );
    }
}
