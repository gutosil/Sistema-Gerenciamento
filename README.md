# Gerenciamento— Gestão de vendas e comissões

O protótipo original continua disponível em `index.html`. A aplicação funcional foi separada em:

- `backend/`: API REST em Laravel com banco SQLite, migrations e CRUD para usuários, clientes, contratos, parcelas, produtos e tipos.
- `frontend/`: aplicação React baseada nas telas e no visual do protótipo atual.

## Requisitos

- PHP 8.3 ou superior, Composer e extensão PDO SQLite.
- Node.js e npm.

## Executar localmente

Em um terminal, inicie a API:

```powershell
cd backend
php artisan migrate --seed
php artisan serve
```

Em outro terminal, inicie o React:

```powershell
cd frontend
npm install
npm run dev
```

Abra o endereço exibido pelo Vite. Durante o desenvolvimento, as requisições `/api` são encaminhadas para o Laravel em `http://127.0.0.1:8000`.

O seeder cria cinco registros demonstrativos de cada cadastro principal e cinco parcelas para cada contrato. Ele pode ser executado novamente sem duplicar os dados de demonstração usando `php artisan db:seed`.

## API

Os endpoints REST ficam sob `/api`: `users`, `clients`, `contracts`, `installments`, `products`, `customer-types` e `contract-types`. Cada recurso aceita operações de listagem, consulta, criação, edição e exclusão; o Laravel valida os dados e retorna erros JSON com status HTTP apropriados.

O banco SQLite é configurado em `backend/.env`. A API está preparada para desenvolvimento local; antes de publicar em rede, configure autenticação e autorização para proteger os dados.
