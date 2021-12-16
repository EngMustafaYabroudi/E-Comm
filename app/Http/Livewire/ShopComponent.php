<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Cart;
use Livewire\Component;
use Livewire\WithPagination;

class ShopComponent extends Component
{
    public function store($product_id, $product_name, $product_price)
    {
        Cart::add(['id' => $product_id, 'name' => $product_name, 'qty' => 1, 'price' => $product_price])->associate('App\Models\Product');
        session()->flush('success message', 'Item add in cart');
        dd(Cart::count());
        /* return redirect()->route('product.cart'); */
    }
    use WithPagination;
    public function render()
    {
        $products = Product::paginate(12);
        return view('livewire.shop-component', ['products' => $products])->layout('layouts.base');
    }
}
