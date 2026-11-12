<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\CustomerType;
use App\Models\Installment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $customerTypes = collect([
            ['name' => 'Novo', 'new_commission_percent' => 10, 'existing_commission_percent' => 8],
            ['name' => 'Antigo', 'new_commission_percent' => 8, 'existing_commission_percent' => 10],
            ['name' => 'Vintage', 'new_commission_percent' => 7, 'existing_commission_percent' => 7],
            ['name' => 'Parceiro', 'new_commission_percent' => 12, 'existing_commission_percent' => 10],
            ['name' => 'Indicação', 'new_commission_percent' => 9, 'existing_commission_percent' => 8],
        ])->map(fn (array $attributes) => CustomerType::firstOrCreate(
            ['name' => $attributes['name']],
            $attributes + ['active' => true],
        ));

        $contractTypes = collect(['Pontual', 'Contínuo', 'Projeto', 'Suporte', 'Consultoria'])
            ->map(fn (string $name) => ContractType::firstOrCreate(
                ['name' => $name],
                ['active' => true],
            ));

        $users = collect([
            ['name' => 'Calebe Pereira', 'email' => 'calebe@example.test', 'role' => 'admin'],
            ['name' => 'Gustavo Almeida', 'email' => 'gustavo@example.test', 'role' => 'seller'],
            ['name' => 'João Martins', 'email' => 'joao@example.test', 'role' => 'seller'],
            ['name' => 'Caio Oliveira', 'email' => 'caio@example.test', 'role' => 'seller'],
            ['name' => 'Wesley Souza', 'email' => 'wesley@example.test', 'role' => 'finance'],
        ])->map(fn (array $attributes, int $index) => User::firstOrCreate(
            ['email' => $attributes['email']],
            $attributes + [
                'cpf' => sprintf('%03d.000.000-%02d', $index + 100, $index + 10),
                'phone' => sprintf('(67) 9%04d-%04d', 9000 + $index, 1000 + $index),
                'password' => Str::random(48),
                'active' => true,
            ],
        ));

        $clients = collect([
            ['name' => 'Senac', 'document' => '12.345.678/0001-90'],
            ['name' => 'IMPCG', 'document' => '23.456.789/0001-01'],
            ['name' => 'Mais Code', 'document' => '34.567.890/0001-12'],
            ['name' => 'Instituto Campo Grande', 'document' => '45.678.901/0001-23'],
            ['name' => 'Prefeitura Municipal', 'document' => '56.789.012/0001-34'],
        ])->map(function (array $attributes, int $index) use ($customerTypes) {
            return Client::firstOrCreate(
                ['document' => $attributes['document']],
                $attributes + [
                    'email' => sprintf('contato%d@example.test', $index + 1),
                    'address' => sprintf('Rua das Empresas, %d - Campo Grande/MS', 100 + $index),
                    'customer_type_id' => $customerTypes[$index]->id,
                    'active' => true,
                ],
            );
        });

        $products = collect([
            ['name' => 'Dev PHP', 'description' => 'Desenvolvimento e manutenção em PHP', 'price' => 60, 'new_commission_percent' => 10, 'existing_commission_percent' => 10],
            ['name' => 'Desenvolvimento Web', 'description' => 'Criação de aplicações web', 'price' => 120, 'new_commission_percent' => 12, 'existing_commission_percent' => 10],
            ['name' => 'Sistema de Gestão', 'description' => 'Implantação do sistema de gestão', 'price' => 180, 'new_commission_percent' => 10, 'existing_commission_percent' => 8],
            ['name' => 'Suporte Mensal', 'description' => 'Suporte técnico recorrente', 'price' => 90, 'new_commission_percent' => 8, 'existing_commission_percent' => 8],
            ['name' => 'Consultoria Digital', 'description' => 'Consultoria em tecnologia e processos', 'price' => 150, 'new_commission_percent' => 11, 'existing_commission_percent' => 9],
        ])->map(fn (array $attributes) => Product::firstOrCreate(
            ['name' => $attributes['name']],
            $attributes + ['active' => true],
        ));

        foreach (range(0, 4) as $index) {
            $startedAt = Carbon::create(2026, 6, 1)->addMonths($index);
            $customerType = $customerTypes[$index];
            $product = $products[$index];
            $contract = Contract::firstOrCreate(
                ['client_id' => $clients[$index]->id],
                [
                    'salesperson_id' => $users[1 + ($index % 3)]->id,
                    'product_id' => $product->id,
                    'contract_type_id' => $contractTypes[$index]->id,
                    'customer_type_id' => $customerType->id,
                    'started_at' => $startedAt->toDateString(),
                    'ended_at' => $startedAt->copy()->addYear()->toDateString(),
                    'hours_worked' => 80 + ($index * 20),
                    'contact_name' => sprintf('Contato %d', $index + 1),
                    'contact_phone' => sprintf('(67) 9%04d-%04d', 9100 + $index, 2000 + $index),
                    'contact_email' => sprintf('responsavel%d@example.test', $index + 1),
                    'entry_amount' => 2000 + ($index * 500),
                    'total_amount' => 10000 + ($index * 2500),
                    'payment_method' => 'installments',
                    'installments_count' => 5,
                    'status' => $index === 2 ? 'completed' : 'active',
                ],
            );

            $installmentAmount = round(($contract->total_amount - $contract->entry_amount) / 5, 2);
            $commissionRate = $index % 2 === 0
                ? $product->new_commission_percent
                : $product->existing_commission_percent;

            foreach (range(1, 5) as $number) {
                Installment::firstOrCreate(
                    ['contract_id' => $contract->id, 'number' => $number],
                    [
                        'due_date' => $startedAt->copy()->addMonths($number)->toDateString(),
                        'amount' => $installmentAmount,
                        'commission_amount' => round($installmentAmount * $commissionRate / 100, 2),
                        'status' => $number === 1 && $index < 2 ? 'paid' : 'pending',
                    ],
                );
            }
        }
    }
}
