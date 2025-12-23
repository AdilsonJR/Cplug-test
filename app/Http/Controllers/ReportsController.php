<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReportRequest;
use App\UseCases\GetSalesReportUseCase;

class ReportsController extends Controller
{
    public function sales(SalesReportRequest $request, GetSalesReportUseCase $useCase)
    {
        $data = $request->validated();

        return response()->json($useCase->execute(
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['product_sku'] ?? null
        ));
    }
}
