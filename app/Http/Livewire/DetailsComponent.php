<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailsComponent extends Component
{
    public $slug;
    public function mount($slug)
    {
        $this->slug = $slug;
    }
    /* public  */
    public function render()
    {
        $product = Product::where('slug', $this->slug)->first();
        $popular_products = Product::inRandomOrder()->limit(4)->get();
        $releted_prouducs = Product::where('category_id', $product->category_id)->inRandomOrder()->limit(5)->get();

        return view('livewire.details-component', ['product' => $product, 'popular_products' => $popular_products, 'releted_prouducs' => $releted_prouducs])->layout('layouts.base');
    }
}
