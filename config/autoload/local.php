<?php
return [
    'zf-mvc-auth' => [
        'authentication' => [
            'adapters' => [
                'queryme' => [
                    'adapter' => \ZF\MvcAuth\Authentication\OAuth2Adapter::class,
                    'storage' => [
                        'adapter' => \pdo::class,
                        'dsn' => 'mysql:host=localhost;dbname=queryme;',
                        // 'dsn' => 'mysql:host=grupobinario.sytes.net;dbname=queryme;',
                        'route' => '/oauth',
                        'username' => 'root',
                        'password' => 'frutill4s',
                    ],
                ],
            ],
        ],
    ],
    'db' => [
        'adapters' => [
            'dummy' => [
                'database' => 'queryme',
                'driver' => 'PDO_Mysql',
                'username' => 'root',
                'password' => 'frutill4s',
            ],
        ],
    ],
    'zf-oauth2' => [
          'access_lifetime' => 3600,
          'options' => [
              'always_issue_new_refresh_token' => true,
          ],
      ],
];
