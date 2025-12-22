<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'integer', 'exists:products,id', 'required_without:sku'],
            'sku' => ['nullable', 'string', 'exists:products,sku', 'required_without:product_id'],
            'quantity' => ['required', 'integer'],
            'cost_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Ocorreu uma falha na validação dos dados.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
