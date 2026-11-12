<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nome da aplicação
    |--------------------------------------------------------------------------
    |
    | Este valor é o nome da sua aplicação, usado quando o framework precisar
    | exibi-lo em uma notificação ou em outros elementos da interface.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Ambiente da aplicação
    |--------------------------------------------------------------------------
    |
    | Este valor determina o "ambiente" em que sua aplicação está sendo
    | executada. Ele pode influenciar a configuração dos serviços utilizados.
    | Defina-o no arquivo ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo de depuração da aplicação
    |--------------------------------------------------------------------------
    |
    | No modo de depuração, erros detalhados com rastreamentos de pilha serão
    | exibidos sempre que ocorrer uma falha na aplicação. Se estiver desativado,
    | será exibida uma página de erro genérica.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL da aplicação
    |--------------------------------------------------------------------------
    |
    | Esta URL é usada pelo console para gerar endereços corretamente ao usar
    | a ferramenta de linha de comando Artisan. Defina-a como a URL base da
    | aplicação para que esteja disponível nos comandos Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Fuso horário da aplicação
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar o fuso horário padrão da aplicação, usado
    | pelas funções de data e hora do PHP. O padrão é "UTC", adequado para
    | a maioria dos casos.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Configuração do idioma da aplicação
    |--------------------------------------------------------------------------
    |
    | O idioma da aplicação define o idioma padrão usado pelos métodos de
    | tradução e localização do Laravel. Esta opção pode ser definida para
    | qualquer idioma para o qual existam textos traduzidos.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Chave de criptografia
    |--------------------------------------------------------------------------
    |
    | Esta chave é usada pelos serviços de criptografia do Laravel e deve ser
    | definida como uma sequência aleatória de 32 caracteres para proteger
    | os valores criptografados. Faça isso antes de implantar a aplicação.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver do modo de manutenção
    |--------------------------------------------------------------------------
    |
    | Estas opções determinam o driver usado para controlar o estado do
    | "modo de manutenção" do Laravel. O driver "cache" permite controlar
    | o modo de manutenção em várias máquinas.
    |
    | Drivers compatíveis: "file", "cache", "array"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
