<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Driver de sessão padrão
    |--------------------------------------------------------------------------
    |
    | Esta opção determina o driver de sessão padrão usado nas requisições
    | recebidas. O Laravel oferece várias opções para armazenar os dados da
    | sessão. O banco de dados é uma ótima opção padrão.
    |
    | Compatíveis: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Duração da sessão
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar por quantos minutos a sessão pode permanecer
    | inativa antes de expirar. Para que ela expire assim que o navegador for
    | fechado, use a opção de configuração expire_on_close.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Criptografia da sessão
    |--------------------------------------------------------------------------
    |
    | Esta opção permite definir que todos os dados da sessão sejam criptografados
    | antes do armazenamento. O Laravel faz a criptografia automaticamente, e
    | você pode usar a sessão normalmente.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /*
    |--------------------------------------------------------------------------
    | Local dos arquivos de sessão
    |--------------------------------------------------------------------------
    |
    | Ao usar o driver de sessão "file", os arquivos da sessão são gravados
    | em disco. O local padrão está definido aqui, mas você pode escolher
    | outro diretório para armazená-los.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Conexão de banco de dados da sessão
    |--------------------------------------------------------------------------
    |
    | Ao usar os drivers de sessão "database" ou "redis", você pode especificar
    | a conexão que será usada para gerenciar as sessões. Ela deve corresponder
    | a uma conexão definida nas configurações do banco de dados.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Tabela de sessões do banco de dados
    |--------------------------------------------------------------------------
    |
    | Ao usar o driver de sessão "database", você pode especificar a tabela
    | onde as sessões serão armazenadas. Há um valor padrão adequado, mas
    | você pode escolher outra tabela.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Armazenamento de cache da sessão
    |--------------------------------------------------------------------------
    |
    | Ao usar um dos back-ends de sessão do framework baseados em cache, você
    | pode definir o armazenamento usado para manter os dados entre requisições.
    | Ele deve corresponder a um dos armazenamentos de cache configurados.
    |
    | Afeta: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Probabilidade de limpeza das sessões
    |--------------------------------------------------------------------------
    |
    | Alguns drivers de sessão precisam limpar manualmente o armazenamento
    | para remover sessões antigas. Este valor define a chance de isso ocorrer
    | em cada requisição. Por padrão, a probabilidade é de 2 em 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Nome do cookie de sessão
    |--------------------------------------------------------------------------
    |
    | Aqui você pode alterar o nome do cookie de sessão criado pelo framework.
    | Em geral, não é necessário mudar esse valor, pois isso não oferece uma
    | melhoria significativa de segurança.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Caminho do cookie de sessão
    |--------------------------------------------------------------------------
    |
    | O caminho do cookie de sessão determina em quais caminhos ele estará
    | disponível. Normalmente, será o caminho raiz da aplicação, mas você
    | pode alterá-lo quando necessário.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Domínio do cookie de sessão
    |--------------------------------------------------------------------------
    |
    | Este valor determina em quais domínios e subdomínios o cookie de sessão
    | estará disponível. Por padrão, ele ficará disponível no domínio raiz,
    | sem subdomínios. Em geral, não é necessário alterar esse valor.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Cookies somente por HTTPS
    |--------------------------------------------------------------------------
    |
    | Se esta opção estiver como true, os cookies de sessão só serão enviados
    | ao servidor quando o navegador usar uma conexão HTTPS. Assim, o cookie
    | não será enviado quando isso não puder ser feito com segurança.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | Acesso somente por HTTP
    |--------------------------------------------------------------------------
    |
    | Se este valor estiver como true, o JavaScript não poderá acessar o valor
    | do cookie, que ficará disponível apenas pelo protocolo HTTP. Em geral,
    | não é recomendável desativar esta opção.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Cookies do mesmo site (Same-Site)
    |--------------------------------------------------------------------------
    |
    | Esta opção determina como os cookies se comportam em requisições entre
    | sites e pode ajudar a reduzir ataques CSRF. Por padrão, o valor será
    | "lax", permitindo requisições entre sites de forma segura.
    |
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Compatíveis: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Cookies particionados
    |--------------------------------------------------------------------------
    |
    | Se este valor estiver como true, o cookie será vinculado ao site de nível
    | superior em um contexto entre sites. O navegador aceita cookies particionados
    | quando estão marcados como "secure" e o atributo Same-Site está como "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | Serialização da sessão
    |--------------------------------------------------------------------------
    |
    | Este valor controla a estratégia de serialização dos dados da sessão,
    | que por padrão é JSON. Defini-lo como "php" permite armazenar objetos
    | PHP na sessão, mas pode deixar a aplicação vulnerável a ataques de
    | serialização por "cadeia de gadgets" se a APP_KEY for exposta.
    |
    | Compatíveis: "json", "php"
    |
    */

    'serialization' => 'json',

];
