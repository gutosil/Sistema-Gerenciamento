<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disco padrão do sistema de arquivos
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar o disco padrão do sistema de arquivos usado
    | pelo framework. Além do disco "local", há diversos discos em nuvem
    | disponíveis para armazenar arquivos da aplicação.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Discos do sistema de arquivos
    |--------------------------------------------------------------------------
    |
    | Abaixo você pode configurar quantos discos forem necessários e até
    | vários discos para o mesmo driver. Há exemplos dos principais drivers
    | de armazenamento compatíveis para referência.
    |
    | Drivers compatíveis: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Links simbólicos
    |--------------------------------------------------------------------------
    |
    | Aqui você pode configurar os links simbólicos criados ao executar o
    | comando Artisan `storage:link`. As chaves do array devem indicar os
    | caminhos dos links, e os valores devem indicar seus destinos.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
