<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    /**
     * Lista todos os clientes cadastrados.
     *
     * GET /api/clientes
     */
    public function index(): JsonResponse
    {
        $clientes = Cliente::query()
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json($clientes);
    }

    /**
     * Cadastra um novo cliente.
     *
     * POST /api/clientes
     */
    public function store(StoreClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated());

        return response()->json($cliente, 201);
    }

    /**
     * Exibe os dados de um cliente específico.
     *
     * GET /api/clientes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        return response()->json($cliente);
    }

    /**
     * Atualiza os dados de um cliente existente.
     *
     * PUT/PATCH /api/clientes/{id}
     */
    public function update(UpdateClienteRequest $request, int $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->update($request->validated());

        return response()->json($cliente);
    }

    /**
     * Remove um cliente do sistema.
     *
     * DELETE /api/clientes/{id}
     */
    public function destroy(int $id)
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->delete();

        return response()->noContent();
    }
}
