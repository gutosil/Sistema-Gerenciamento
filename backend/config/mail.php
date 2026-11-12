<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serviço de e-mail padrão
    |--------------------------------------------------------------------------
    |
    | Esta opção controla o serviço de e-mail padrão usado para enviar mensagens,
    | a menos que outro seja especificado no envio. Os demais serviços podem ser
    | configurados no array "mailers". Há exemplos de cada tipo.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Configurações dos serviços de e-mail
    |--------------------------------------------------------------------------
    |
    | Aqui você pode configurar todos os serviços de e-mail usados pela
    | aplicação e suas respectivas opções. Alguns exemplos já estão definidos;
    | você pode adicionar outros conforme necessário.
    |
    | O Laravel oferece vários drivers de "transporte" de e-mail. Abaixo,
    | você pode especificar quais serão usados e adicionar outros serviços,
    | se necessário.
    |
    | Compatíveis: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Endereço global do remetente
    |--------------------------------------------------------------------------
    |
    | Você pode definir um mesmo endereço para o envio de todos os e-mails da
    | aplicação. Aqui, especifique o nome e o endereço usados globalmente.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')),
    ],

];
