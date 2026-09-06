<?php

namespace Tests\Feature;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_cliente_com_dados_validos(): void
    {
        $response = $this->postJson('/api/clientes', [
            'nome' => 'Eduardo Back',
            'cpf' => '12345678901',
            'email' => 'eduardo@email.com',
            'telefone' => '51999999999',
            'renda_mensal' => 5000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'nome' => 'Eduardo Back',
                'cpf' => '12345678901',
                'email' => 'eduardo@email.com',
            ]);

        $this->assertDatabaseHas('clientes', [
            'cpf' => '12345678901',
            'email' => 'eduardo@email.com',
        ]);
    }

    public function test_falha_ao_criar_cliente_sem_campos_obrigatorios(): void
    {
        $response = $this->postJson('/api/clientes', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'nome',
                'cpf',
                'email',
                'renda_mensal',
            ]);
    }

    public function test_falha_ao_criar_cliente_com_cpf_duplicado(): void
    {
        Cliente::create([
            'nome' => 'Cliente Existente',
            'cpf' => '12345678901',
            'email' => 'existente@email.com',
            'telefone' => null,
            'renda_mensal' => 3000,
        ]);

        $response = $this->postJson('/api/clientes', [
            'nome' => 'Novo Cliente',
            'cpf' => '12345678901',
            'email' => 'novo@email.com',
            'renda_mensal' => 4000,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_falha_ao_criar_cliente_com_email_duplicado(): void
    {
        Cliente::create([
            'nome' => 'Cliente Existente',
            'cpf' => '12345678901',
            'email' => 'existente@email.com',
            'telefone' => null,
            'renda_mensal' => 3000,
        ]);

        $response = $this->postJson('/api/clientes', [
            'nome' => 'Novo Cliente',
            'cpf' => '98765432109',
            'email' => 'existente@email.com',
            'renda_mensal' => 4000,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_lista_clientes_de_forma_paginada(): void
    {
        Cliente::create([
            'nome' => 'Cliente 1',
            'cpf' => '11111111111',
            'email' => 'cliente1@email.com',
            'telefone' => null,
            'renda_mensal' => 3000,
        ]);

        Cliente::create([
            'nome' => 'Cliente 2',
            'cpf' => '22222222222',
            'email' => 'cliente2@email.com',
            'telefone' => null,
            'renda_mensal' => 4000,
        ]);

        $response = $this->getJson('/api/clientes');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'current_page',
                'data',
                'per_page',
                'total',
            ]);
    }

    public function test_exibe_cliente_existente_por_id(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Eduardo Back',
            'cpf' => '12345678901',
            'email' => 'eduardo@email.com',
            'telefone' => null,
            'renda_mensal' => 5000,
        ]);

        $response = $this->getJson("/api/clientes/{$cliente->id}");

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $cliente->id,
                'nome' => 'Eduardo Back',
            ]);
    }

    public function test_retorna_404_ao_buscar_cliente_inexistente(): void
    {
        $response = $this->getJson('/api/clientes/999');

        $response->assertNotFound();
    }

    public function test_atualiza_parcialmente_cliente_existente(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Eduardo Back',
            'cpf' => '12345678901',
            'email' => 'eduardo@email.com',
            'telefone' => null,
            'renda_mensal' => 5000,
        ]);

        $response = $this->patchJson("/api/clientes/{$cliente->id}", [
            'renda_mensal' => 7500,
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $cliente->id,
                'renda_mensal' => '7500.00',
            ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'renda_mensal' => 7500,
        ]);
    }

    public function test_remove_cliente_existente(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Eduardo Back',
            'cpf' => '12345678901',
            'email' => 'eduardo@email.com',
            'telefone' => null,
            'renda_mensal' => 5000,
        ]);

        $response = $this->deleteJson("/api/clientes/{$cliente->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('clientes', [
            'id' => $cliente->id,
        ]);
    }

    public function test_retorna_404_ao_remover_cliente_inexistente(): void
    {
        $response = $this->deleteJson('/api/clientes/999');

        $response->assertNotFound();
    }
}