<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use Symfony\Component\Intl\Countries;

class CheckoutController extends Controller
{
    protected $cart;

    public function __construct(CartRepositoryInterface $cart)
    {
        $this->cart = $cart;
    }

    public function create()
    {
        return view('front.checkout', [
            'cart' => $this->cart,
            'user' => Auth::user(),
            'countries' => Countries::getNames(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'billing_name' => ['required', 'string'],
            'billing_phone' => 'required',
            'billing_email' => 'required|email',
            'billing_address' => 'required',
            'billing_city' => 'required',
            'billing_country' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $request->merge([
               'total' => $this->cart->total()
            ]);
            //event('order.creating');
            $order = Order::create($request->all());
            event(new OrderCreated($order));
            $items = [];
            foreach ($this->cart->all() as $item) {
                $items[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ];
                /*$order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);*/
            }
            //return $items;
            DB::table('order_items')->insert($items);
            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return redirect()->route('orders');
    }
}
