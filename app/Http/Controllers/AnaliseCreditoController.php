<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitarAnaliseCreditoRequest;
use App\Services\AnaliseCreditoService;
use DomainException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use UnexpectedValueException;

class AnaliseCreditoController extends Controller
{
    public function __construct(
        private readonly AnaliseCreditoService $analiseCreditoService
    ) {}

    /**
     * Solicita uma nova análise de crédito.
     *
     * POST /api/analise-credito
     */
    public function solicitar(
        SolicitarAnaliseCreditoRequest $request
    ): JsonResponse {
        try {
            $analise = $this->analiseCreditoService->solicitar(
                $request->validated()
            );

            return response()->json($analise, 201);

        } catch (ConnectionException $exception) {
            return response()->json([
                'message' => 'Tempo limite ou falha de comunicação com o Bureau de Crédito.',
            ], 504);

        } catch (UnexpectedValueException $exception) {
            return response()->json([
                'message' => 'O Bureau de Crédito retornou uma resposta inválida.',
            ], 502);

        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'O Bureau de Crédito está indisponível no momento.',
            ], 502);
        }
    }

    /**
     * Confirma a contratação de uma análise de crédito aprovada.
     *
     * POST /api/analise-credito/{id}/contratar
     */
    public function contratar(int $id): JsonResponse
    {
        try {
            $analise = $this->analiseCreditoService->contratar($id);

            return response()->json([
                'message' => 'Crédito contratado com sucesso.',
                'analise' => $analise,
            ]);

        } catch (DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
