<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serviços de terceiros
    |--------------------------------------------------------------------------
    |
    | Este arquivo armazena as credenciais de serviços de terceiros, como
    | Resend, Postmark, AWS e outros. Ele é o local convencional para essas
    | informações e permite que os pacotes encontrem as credenciais dos
    | diferentes serviços.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
