<?php

namespace Tests\Feature;

use App\Models\AnaliseCredito;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnaliseCreditoTest extends TestCase
{
    use RefreshDatabase;

    public function test_score_alto_aprova_credito_com_taxa_de_2_9_porcento(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678903',
                'score' => 850,
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Score Alto',
            'cpf' => '12345678903',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 10000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'aprovado')
            ->assertJsonPath('score', 850)
            ->assertJsonPath('taxa_juros', '2.90')
            ->assertJsonPath('valor_parcela', '1123.33')
            ->assertJsonPath('motivo_rejeicao', null);

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678903',
            'status' => 'aprovado',
            'score' => 850,
            'taxa_juros' => 2.90,
        ]);
    }

    public function test_score_medio_aprova_credito_com_taxa_de_4_5_porcento(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678902',
                'score' => 550,
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Score Médio',
            'cpf' => '12345678902',
            'renda_mensal' => 10000,
            'tipo_credito' => 'automotivo',
            'valor_solicitado' => 10000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'aprovado')
            ->assertJsonPath('score', 550)
            ->assertJsonPath('taxa_juros', '4.50')
            ->assertJsonPath('valor_parcela', '1283.33');

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678902',
            'status' => 'aprovado',
            'score' => 550,
            'taxa_juros' => 4.50,
        ]);
    }

    public function test_renda_abaixo_do_minimo_reprova_credito(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678903',
                'score' => 850,
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Renda Baixa',
            'cpf' => '12345678903',
            'renda_mensal' => 1400,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 1000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'reprovado')
            ->assertJsonPath(
                'motivo_rejeicao',
                'Renda mínima insuficiente'
            );

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678903',
            'status' => 'reprovado',
            'motivo_rejeicao' => 'Renda mínima insuficiente',
        ]);
    }

    public function test_score_abaixo_de_400_reprova_credito(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678901',
                'score' => 150,
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Score Baixo',
            'cpf' => '12345678901',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'reprovado')
            ->assertJsonPath('score', 150)
            ->assertJsonPath(
                'motivo_rejeicao',
                'Score de crédito muito baixo'
            );

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678901',
            'status' => 'reprovado',
            'motivo_rejeicao' => 'Score de crédito muito baixo',
        ]);
    }

    public function test_parcela_superior_a_30_porcento_da_renda_reprova_credito(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678903',
                'score' => 850,
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Renda Comprometida',
            'cpf' => '12345678903',
            'renda_mensal' => 2000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 10000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'reprovado')
            ->assertJsonPath(
                'motivo_rejeicao',
                'Comprometimento de renda superior a 30%'
            )
            ->assertJsonPath('taxa_juros', '2.90')
            ->assertJsonPath('valor_parcela', '1123.33');

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678903',
            'status' => 'reprovado',
            'motivo_rejeicao' => 'Comprometimento de renda superior a 30%',
        ]);
    }

    public function test_falha_http_500_do_bureau_retorna_resposta_limpa_sem_crash(): void
    {
        Http::fake([
            '*' => Http::response([
                'message' => 'Erro interno no Bureau',
            ], 500),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Bureau Indisponível',
            'cpf' => '12345678904',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $response
            ->assertStatus(502)
            ->assertJson([
                'message' => 'O Bureau de Crédito está indisponível no momento.',
            ]);

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678904',
            'status' => 'pendente',
        ]);
    }

    public function test_credito_aprovado_pode_ser_contratado(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678903',
                'score' => 850,
            ], 200),
        ]);

        $analiseResponse = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Contratação',
            'cpf' => '12345678903',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $analiseResponse
            ->assertCreated()
            ->assertJsonPath('status', 'aprovado');

        $analiseId = $analiseResponse->json('id');

        $response = $this->postJson(
            "/api/analise-credito/{$analiseId}/contratar"
        );

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Crédito contratado com sucesso.')
            ->assertJsonPath('analise.status', 'contratado');

        $this->assertDatabaseHas('analises_credito', [
            'id' => $analiseId,
            'status' => 'contratado',
        ]);
    }

    public function test_cliente_e_criado_automaticamente_quando_cpf_nao_existe(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '98765432103',
                'score' => 850,
            ], 200),
        ]);

        $this->assertDatabaseMissing('clientes', [
            'cpf' => '98765432103',
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Novo Cliente',
            'cpf' => '98765432103',
            'renda_mensal' => 10000,
            'tipo_credito' => 'imobiliario',
            'valor_solicitado' => 5000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'aprovado');

        $cliente = Cliente::where('cpf', '98765432103')->first();

        $this->assertNotNull($cliente);
        $this->assertSame('Novo Cliente', $cliente->nome);
        $this->assertSame('10000.00', $cliente->renda_mensal);
        $this->assertNull($cliente->email);

        $analise = AnaliseCredito::where(
            'cpf',
            '98765432103'
        )->first();

        $this->assertNotNull($analise);
        $this->assertSame($cliente->id, $analise->cliente_id);
    }

    public function test_resposta_do_bureau_sem_score_retorna_erro_controlado(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678906',
                'status_bureau' => 'ok',
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Resposta Inválida',
            'cpf' => '12345678906',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $response
            ->assertStatus(502)
            ->assertJson([
                'message' => 'O Bureau de Crédito retornou uma resposta inválida.',
            ]);

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678906',
            'status' => 'pendente',
        ]);
    }

    public function test_analise_reprovada_nao_pode_ser_contratada(): void
    {
        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678901',
                'score' => 150,
            ], 200),
        ]);

        $analiseResponse = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Reprovado',
            'cpf' => '12345678901',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $analiseId = $analiseResponse->json('id');

        $response = $this->postJson(
            "/api/analise-credito/{$analiseId}/contratar"
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'A análise de crédito não está aprovada para contratação.',
            ]);

        $this->assertDatabaseHas('analises_credito', [
            'id' => $analiseId,
            'status' => 'reprovado',
        ]);
    }

    public function test_falha_de_conexao_com_bureau_retorna_504_sem_crash(): void
    {
        Http::fake([
            '*' => Http::failedConnection(
                'Timeout ao consultar o Bureau'
            ),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Timeout',
            'cpf' => '12345678905',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $response
            ->assertStatus(504)
            ->assertJson([
                'message' => 'Tempo limite ou falha de comunicação com o Bureau de Crédito.',
            ]);

        $this->assertDatabaseHas('analises_credito', [
            'cpf' => '12345678905',
            'status' => 'pendente',
        ]);
    }

    public function test_retorna_404_ao_contratar_analise_inexistente(): void
    {
        $response = $this->postJson(
            '/api/analise-credito/999999/contratar'
        );

        $response->assertNotFound();
    }

    public function test_cliente_existente_e_reutilizado_em_nova_analise(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Cliente Existente',
            'cpf' => '12345678903',
            'email' => 'existente@example.com',
            'telefone' => null,
            'renda_mensal' => 10000,
        ]);

        Http::fake([
            '*' => Http::response([
                'cpf' => '12345678903',
                'score' => 850,
            ], 200),
        ]);

        $response = $this->postJson('/api/analise-credito', [
            'nome' => 'Cliente Existente',
            'cpf' => '12345678903',
            'renda_mensal' => 10000,
            'tipo_credito' => 'pessoal',
            'valor_solicitado' => 5000,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('cliente_id', $cliente->id);

        $this->assertSame(
            1,
            Cliente::where('cpf', '12345678903')->count()
        );

        $this->assertDatabaseHas('analises_credito', [
            'cliente_id' => $cliente->id,
            'cpf' => '12345678903',
        ]);
    }

    public function test_valida_dados_obrigatorios_da_solicitacao_de_analise(): void
    {
        Http::fake();

        $response = $this->postJson(
            '/api/analise-credito',
            []
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'nome',
                'cpf',
                'renda_mensal',
                'tipo_credito',
                'valor_solicitado',
            ]);

        Http::assertNothingSent();
    }
}
