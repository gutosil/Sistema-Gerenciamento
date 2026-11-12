<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created_updated_listed_and_deleted(): void
    {
        $client = $this->postJson('/api/clients', [
            'name' => 'Senac',
            'document' => '12.345.678/0001-90',
            'email' => 'contato@senac.test',
            'address' => 'Rua Central, 10',
        ])->assertCreated()
            ->assertJsonPath('name', 'Senac')
            ->json();

        $this->getJson('/api/clients')
            ->assertOk()
            ->assertJsonPath('data.0.id', $client['id']);

        $this->putJson("/api/clients/{$client['id']}", [
            'name' => 'Senac Atualizado',
            'document' => '12.345.678/0001-90',
        ])->assertOk()
            ->assertJsonPath('name', 'Senac Atualizado');

        $this->deleteJson("/api/clients/{$client['id']}")
            ->assertNoContent();

        $this->assertDatabaseMissing('clients', ['id' => $client['id']]);
    }

    public function test_client_document_must_be_unique(): void
    {
        $attributes = [
            'name' => 'Primeiro cliente',
            'document' => '12345678000190',
        ];

        $this->postJson('/api/clients', $attributes)->assertCreated();
        $this->postJson('/api/clients', [...$attributes, 'name' => 'Outro cliente'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    public function test_user_password_is_hashed_and_not_returned_by_the_api(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Vendedor',
            'email' => 'vendedor@example.test',
            'password' => 'senha-segura-123',
            'role' => 'seller',
        ])->assertCreated()
            ->assertJsonMissingPath('password');

        $this->assertDatabaseHas('users', [
            'id' => $response->json('id'),
            'email' => 'vendedor@example.test',
        ]);
        $this->assertDatabaseMissing('users', ['password' => 'senha-segura-123']);
    }

    public function test_contracts_and_installments_are_linked_to_their_records(): void
    {
        $client = $this->postJson('/api/clients', [
            'name' => 'Cliente do contrato',
            'document' => '12345678901234',
        ])->assertCreated()->json();

        $seller = $this->postJson('/api/users', [
            'name' => 'Vendedor do contrato',
            'email' => 'vendedor-contrato@example.test',
            'password' => 'senha-segura-123',
            'role' => 'seller',
        ])->assertCreated()->json();

        $contract = $this->postJson('/api/contracts', [
            'client_id' => $client['id'],
            'salesperson_id' => $seller['id'],
            'total_amount' => 1200,
            'payment_method' => 'installments',
            'installments_count' => 2,
        ])->assertCreated()
            ->assertJsonPath('client.id', $client['id'])
            ->assertJsonPath('salesperson.id', $seller['id'])
            ->json();

        $this->postJson('/api/installments', [
            'contract_id' => $contract['id'],
            'number' => 1,
            'due_date' => '2026-12-01',
            'amount' => 600,
            'commission_amount' => 60,
        ])->assertCreated();

        $this->getJson("/api/contracts/{$contract['id']}")
            ->assertOk()
            ->assertJsonCount(1, 'installments')
            ->assertJsonPath('installments.0.amount', '600.00');

        $this->deleteJson("/api/clients/{$client['id']}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'Não é possível excluir um cliente que possui contratos.');

        $this->deleteJson("/api/users/{$seller['id']}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'Não é possível excluir um vendedor que possui contratos.');
    }

    public function test_demo_seeder_creates_five_records_per_main_list_without_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('clients', 5);
        $this->assertDatabaseCount('contracts', 5);
        $this->assertDatabaseCount('installments', 25);
        $this->assertDatabaseCount('products', 5);
        $this->assertDatabaseCount('customer_types', 5);
        $this->assertDatabaseCount('contract_types', 5);
    }
}
