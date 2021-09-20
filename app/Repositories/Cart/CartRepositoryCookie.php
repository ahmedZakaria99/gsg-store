<?php

namespace App\Repositories\Cart;

use Illuminate\Support\Facades\Cookie;

class CartRepositoryCookie implements CartRepositoryInterface
{
    protected $name = 'cart';

    public function all()
    {
        return Cookie::get($this->name, []);
    }

    public function add($item, $qty = 1)
    {
        $items = $this->all();
        $items[] = $item;
        Cookie::queue($this->name, $items, 60, '/', null, false, true);
    }

    public function clear()
    {
        Cookie::queue($this->name, '', -60);
    }
}
