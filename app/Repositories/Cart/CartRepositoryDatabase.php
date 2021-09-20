<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class CartRepositoryDatabase implements CartRepositoryInterface
{
    protected $items;

    public function __construct()
    {
        $this->items = collect([]);
    }

    public function all()
    {
        if ($this->items->count() == 0) {
            $this->items = Cart::where('cookie_id', $this->getCookieId())
                ->orWhere('user_id', Auth::id())
                ->get();
        }
        return $this->items;
    }

    public function add($item, $qty = 1)
    {
        $cart = Cart::updateOrCreate([
            'product_id' => ($item instanceof Product) ? $item->id : $item,
            'cookie_id' => $this->getCookieId(),
        ], [
            'user_id' => Auth::id(),
            'quantity' => DB::raw('quantity + ' . $qty),
        ]);
        $this->items = collect([]);
        return $cart;
    }

    public function clear()
    {
        Cart::where('cookie_id', $this->getCookieId())
            ->orWhere('user_id', Auth::id())
            ->delete();
    }

    protected function getCookieId()
    {
        $id = Cookie::get('cart_cookie_id');
        if (!$id) {
            $id = Str::uuid();
            Cookie::queue('cart_cookie_id', $id, 30);
        }
        return $id;
    }

    public function total()
    {
        $items = $this->all();
        return $items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    public function quantity()
    {
        $items = $this->all();
        return $items->sum('quantity');
    }
}
