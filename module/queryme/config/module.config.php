<?php
return [
    'service_manager' => [
        'factories' => [
            \queryme\V1\Rest\Empresas\EmpresasResource::class => \queryme\V1\Rest\Empresas\EmpresasResourceFactory::class,
            \queryme\V1\Rest\Paneles\PanelesResource::class => \queryme\V1\Rest\Paneles\PanelesResourceFactory::class,
            \queryme\V1\Rest\Preguntas\PreguntasResource::class => \queryme\V1\Rest\Preguntas\PreguntasResourceFactory::class,
            \queryme\V1\Rest\Resultados\ResultadosResource::class => \queryme\V1\Rest\Resultados\ResultadosResourceFactory::class,
            \queryme\V1\Rest\Opciones\OpcionesResource::class => \queryme\V1\Rest\Opciones\OpcionesResourceFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'queryme.rest.empresas' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/empresas[/:empresas_id]',
                    'defaults' => [
                        'controller' => 'queryme\\V1\\Rest\\Empresas\\Controller',
                    ],
                ],
            ],
            'queryme.rest.paneles' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/paneles[/:paneles_id]',
                    'defaults' => [
                        'controller' => 'queryme\\V1\\Rest\\Paneles\\Controller',
                    ],
                ],
            ],
            'queryme.rest.preguntas' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/preguntas[/:preguntas_id]',
                    'defaults' => [
                        'controller' => 'queryme\\V1\\Rest\\Preguntas\\Controller',
                    ],
                ],
            ],
            'queryme.rest.resultados' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/resultados[/:resultados_id]',
                    'defaults' => [
                        'controller' => 'queryme\\V1\\Rest\\Resultados\\Controller',
                    ],
                ],
            ],
            'queryme.rest.opciones' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/opciones[/:opciones_id]',
                    'defaults' => [
                        'controller' => 'queryme\\V1\\Rest\\Opciones\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'queryme.rest.empresas',
            1 => 'queryme.rest.paneles',
            2 => 'queryme.rest.preguntas',
            3 => 'queryme.rest.resultados',
            4 => 'queryme.rest.opciones',
        ],
    ],
    'zf-rest' => [
        'queryme\\V1\\Rest\\Empresas\\Controller' => [
            'listener' => \queryme\V1\Rest\Empresas\EmpresasResource::class,
            'route_name' => 'queryme.rest.empresas',
            'route_identifier_name' => 'empresas_id',
            'collection_name' => 'empresas',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \queryme\V1\Rest\Empresas\EmpresasEntity::class,
            'collection_class' => \queryme\V1\Rest\Empresas\EmpresasCollection::class,
            'service_name' => 'empresas',
        ],
        'queryme\\V1\\Rest\\Paneles\\Controller' => [
            'listener' => \queryme\V1\Rest\Paneles\PanelesResource::class,
            'route_name' => 'queryme.rest.paneles',
            'route_identifier_name' => 'paneles_id',
            'collection_name' => 'paneles',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \queryme\V1\Rest\Paneles\PanelesEntity::class,
            'collection_class' => \queryme\V1\Rest\Paneles\PanelesCollection::class,
            'service_name' => 'paneles',
        ],
        'queryme\\V1\\Rest\\Preguntas\\Controller' => [
            'listener' => \queryme\V1\Rest\Preguntas\PreguntasResource::class,
            'route_name' => 'queryme.rest.preguntas',
            'route_identifier_name' => 'preguntas_id',
            'collection_name' => 'preguntas',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \queryme\V1\Rest\Preguntas\PreguntasEntity::class,
            'collection_class' => \queryme\V1\Rest\Preguntas\PreguntasCollection::class,
            'service_name' => 'preguntas',
        ],
        'queryme\\V1\\Rest\\Resultados\\Controller' => [
            'listener' => \queryme\V1\Rest\Resultados\ResultadosResource::class,
            'route_name' => 'queryme.rest.resultados',
            'route_identifier_name' => 'resultados_id',
            'collection_name' => 'resultados',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
                4 => 'POST',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \queryme\V1\Rest\Resultados\ResultadosEntity::class,
            'collection_class' => \queryme\V1\Rest\Resultados\ResultadosCollection::class,
            'service_name' => 'resultados',
        ],
        'queryme\\V1\\Rest\\Opciones\\Controller' => [
            'listener' => \queryme\V1\Rest\Opciones\OpcionesResource::class,
            'route_name' => 'queryme.rest.opciones',
            'route_identifier_name' => 'opciones_id',
            'collection_name' => 'opciones',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \queryme\V1\Rest\Opciones\OpcionesEntity::class,
            'collection_class' => \queryme\V1\Rest\Opciones\OpcionesCollection::class,
            'service_name' => 'opciones',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'queryme\\V1\\Rest\\Empresas\\Controller' => 'HalJson',
            'queryme\\V1\\Rest\\Paneles\\Controller' => 'HalJson',
            'queryme\\V1\\Rest\\Preguntas\\Controller' => 'HalJson',
            'queryme\\V1\\Rest\\Resultados\\Controller' => 'HalJson',
            'queryme\\V1\\Rest\\Opciones\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'queryme\\V1\\Rest\\Empresas\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Paneles\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Preguntas\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Resultados\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Opciones\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'queryme\\V1\\Rest\\Empresas\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Paneles\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Preguntas\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Resultados\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/json',
            ],
            'queryme\\V1\\Rest\\Opciones\\Controller' => [
                0 => 'application/vnd.queryme.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \queryme\V1\Rest\Empresas\EmpresasEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.empresas',
                'route_identifier_name' => 'empresas_id',
                'hydrator' => \Zend\Hydrator\ArraySerializable::class,
            ],
            \queryme\V1\Rest\Empresas\EmpresasCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.empresas',
                'route_identifier_name' => 'empresas_id',
                'is_collection' => true,
            ],
            \queryme\V1\Rest\Paneles\PanelesEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.paneles',
                'route_identifier_name' => 'paneles_id',
                'hydrator' => \Zend\Hydrator\ArraySerializable::class,
            ],
            \queryme\V1\Rest\Paneles\PanelesCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.paneles',
                'route_identifier_name' => 'paneles_id',
                'is_collection' => true,
            ],
            \queryme\V1\Rest\Preguntas\PreguntasEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.preguntas',
                'route_identifier_name' => 'preguntas_id',
                'hydrator' => \Zend\Hydrator\ArraySerializable::class,
            ],
            \queryme\V1\Rest\Preguntas\PreguntasCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.preguntas',
                'route_identifier_name' => 'preguntas_id',
                'is_collection' => true,
            ],
            \queryme\V1\Rest\Resultados\ResultadosEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.resultados',
                'route_identifier_name' => 'resultados_id',
                'hydrator' => \Zend\Hydrator\ArraySerializable::class,
            ],
            \queryme\V1\Rest\Resultados\ResultadosCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.resultados',
                'route_identifier_name' => 'resultados_id',
                'is_collection' => true,
            ],
            \queryme\V1\Rest\Opciones\OpcionesEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.opciones',
                'route_identifier_name' => 'opciones_id',
                'hydrator' => \Zend\Hydrator\ArraySerializable::class,
            ],
            \queryme\V1\Rest\Opciones\OpcionesCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'queryme.rest.opciones',
                'route_identifier_name' => 'opciones_id',
                'is_collection' => true,
            ],
        ],
    ],
];
