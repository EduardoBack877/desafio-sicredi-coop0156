<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use UnexpectedValueException;

class BureauService
{
    public function consultarScore(string $cpf): int
    {
        $url = rtrim(config('services.score_bureau.url'), '/');
        $timeout = config('services.score_bureau.timeout', 3);

        try {
            $response = Http::acceptJson()
                ->timeout($timeout)
                ->get("{$url}/{$cpf}");
        } catch (ConnectionException $exception) {
            throw new ConnectionException(
                'Tempo limite ou falha de conexão com o Bureau.',
                previous: $exception
            );
        }

        if (! $response->successful()) {
            throw new RuntimeException(
                'O Bureau de Crédito está indisponível no momento.'
            );
        }

        $dados = $response->json();

        if (
            ! is_array($dados)
            || ! array_key_exists('score', $dados)
            || ! is_numeric($dados['score'])
        ) {
            throw new UnexpectedValueException(
                'O Bureau retornou uma resposta inválida.'
            );
        }

        return (int) $dados['score'];
    }
}
