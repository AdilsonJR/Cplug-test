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
            return response()->validationErrorApi( $e->getMessage(), 400);
        }
    }

    public function index(GetInventoryUseCase $useCase)
    {
        try {
            return response()->json($useCase->execute());
        } catch (Exception $e) {
            return response()->validationErrorApi( $e->getMessage(), 400);
        }
    }
}
