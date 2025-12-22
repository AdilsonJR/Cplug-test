<?php

namespace App\Http\Controllers;

use App\DTOs\InventoryEntryDTO;
use App\Http\Requests\StoreInventoryRequest;
use App\UseCases\GetInventoryUseCase;
use App\UseCases\StoreInventoryUseCase;
use Exception;

class InventoryController extends Controller
{
    public function store(StoreInventoryRequest $request, StoreInventoryUseCase $useCase)
    {
        try {
            $inventory = $useCase->execute(InventoryEntryDTO::fromRequest($request));
            return response()->json($inventory, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index(GetInventoryUseCase $useCase)
    {
        return response()->json($useCase->execute());
    }
}
