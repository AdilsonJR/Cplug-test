<?php

namespace App\Http\Controllers;

use App\DTOs\CreateSaleDTO;
use App\Http\Requests\StoreSaleRequest;
use App\UseCases\StoreSaleUseCase;
use Exception;

class SalesController extends Controller
{
    public function store(StoreSaleRequest $request, StoreSaleUseCase $useCase)
    {
        try {
            $sale = $useCase->execute(CreateSaleDTO::fromRequest($request));

            return response()->json([
                'id' => $sale->id,
                'status' => $sale->status,
                'created_at' => $sale->created_at,
                'updated_at' => $sale->updated_at,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
