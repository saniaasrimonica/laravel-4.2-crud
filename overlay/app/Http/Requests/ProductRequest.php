<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array {
        $sku = Rule::unique('products', 'sku')->where('user_id', $this->user()->id);
        if ($product = $this->route('product')) { $sku->ignore($product); }
        return [
            'name' => ['required', 'string', 'max:160'],
            'sku' => ['required', 'string', 'max:64', 'regex:/^[A-Z0-9_-]+$/', $sku],
            'description' => ['nullable', 'string', 'max:5000'],
            'price_cents' => ['required', 'integer', 'min:0', 'max:100000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }
}
