<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    protected $cart;

    public function __construct(CartRepositoryInterface $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        //$cart = App::make(CartRepositoryInterface::class);
        //Or u can used it as a parameter for any method or inside constructor
        $carts = $this->cart->all();
        $total = $this->cart->total();
        return view('front.cart', [
            'carts' => $carts,
            'total' => $total
        ]);
    }

    public function store(CartRequest $request)
    {
        $cart = $this->cart->add($request->post('product_id'), $request->post('quantity', 1));
        if ($cart){
            Product::where('id','=',$request->post('product_id'))->update([
                'quantity' => DB::raw('quantity - ' . $request->post('quantity'))
            ]);
        }

        if ($request->expectsJson()){
            return $this->cart->all();
        }
        return redirect()->back()->with('success', __('Item added to cart!'));
    }
}
