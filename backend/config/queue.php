<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nome da conexão padrão da fila
    |--------------------------------------------------------------------------
    |
    | As filas do Laravel oferecem vários back-ends por meio de uma API
    | unificada, permitindo acessar cada um deles com a mesma sintaxe.
    | A conexão padrão da fila é definida abaixo.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Conexões das filas
    |--------------------------------------------------------------------------
    |
    | Aqui você pode configurar as opções de conexão de cada back-end de fila
    | usado pela aplicação. Há um exemplo para cada back-end compatível com
    | Laravel, e você também pode adicionar outros.
    |
    | Drivers disponíveis: "sync", "database", "beanstalkd", "sqs", "redis",
    |          "deferred", "background", "failover", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

        'deferred' => [
            'driver' => 'deferred',
        ],

        'background' => [
            'driver' => 'background',
        ],

        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'deferred',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Agrupamento de tarefas
    |--------------------------------------------------------------------------
    |
    | As opções a seguir configuram o banco de dados e a tabela que armazenam
    | informações sobre o agrupamento de tarefas. Você pode alterá-las para
    | qualquer conexão e tabela definidas na aplicação.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tarefas de fila com falha
    |--------------------------------------------------------------------------
    |
    | Estas opções configuram o registro de tarefas de fila com falha e
    | permitem controlar como e onde elas são armazenadas. O Laravel oferece
    | suporte ao armazenamento em um arquivo simples ou em um banco de dados.
    |
    | Drivers compatíveis: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];
