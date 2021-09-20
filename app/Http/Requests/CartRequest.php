<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => ['int', 'min:1', function ($attribute, $value, $fail) {
                $id = $this->product_id;
                $product = Product::find($id);
                if ($value > $product->quantity){
                    $fail('Quantity greater than quantity in stock');
                }
            }]
        ];
    }
}
