<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Padrões de autenticação
    |--------------------------------------------------------------------------
    |
    | Esta opção define o "guard" de autenticação e o "broker" de redefinição
    | de senha padrão da aplicação. Você pode alterar esses valores conforme
    | necessário; eles são um bom ponto de partida para a maioria das aplicações.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de autenticação
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir cada guard de autenticação da aplicação.
    | Uma configuração padrão adequada já foi definida, usando armazenamento
    | de sessão e o provedor de usuários do Eloquent.
    |
    | Todos os guards de autenticação têm um provedor de usuários, que define
    | como os usuários são obtidos do banco de dados ou de outro sistema de
    | armazenamento usado pela aplicação. Normalmente, usa-se o Eloquent.
    |
    | Compatível: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Provedores de usuários
    |--------------------------------------------------------------------------
    |
    | Todos os guards de autenticação têm um provedor de usuários, que define
    | como os usuários são obtidos do banco de dados ou de outro sistema de
    | armazenamento usado pela aplicação. Normalmente, usa-se o Eloquent.
    |
    | Se houver várias tabelas ou modelos de usuários, você pode configurar
    | vários provedores para representá-los. Depois, esses provedores podem
    | ser associados a outros guards de autenticação definidos por você.
    |
    | Compatíveis: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Redefinição de senhas
    |--------------------------------------------------------------------------
    |
    | Estas opções definem o comportamento do recurso de redefinição de senha
    | do Laravel, incluindo a tabela usada para armazenar os tokens e o
    | provedor de usuários utilizado para localizar os usuários.
    |
    | O tempo de expiração define por quantos minutos cada token de redefinição
    | será válido. Esse recurso de segurança limita a validade dos tokens e o
    | tempo disponível para adivinhá-los. Altere-o se necessário.
    |
    | A opção de intervalo define quantos segundos o usuário deve aguardar
    | antes de gerar novos tokens. Isso evita a geração rápida de muitos
    | tokens de redefinição de senha.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tempo limite de confirmação de senha
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir quantos segundos a janela de confirmação de senha
    | permanece válida antes de solicitar que os usuários digitem a senha
    | novamente. Por padrão, o tempo limite é de três horas.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
