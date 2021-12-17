<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        //$this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    public function index()
    {
        return Product::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'name'                     => 'required|min:4|max:255',
            'slug'                     => 'required|min:4',
            'regular_price'            => 'required|numeric|min:0',
            'commun_info'              => 'required|min:4',
            'image'                    => 'required_without:image_upload|url|nullable',
            'image_upload'             => 'required_without:image|file|image|nullable',
            'quantity'                 => 'required|numeric|min:0',
            'category_id'              => 'required|numeric|exists:categories,id',
            'expiry_date'              => 'required|date',
            'commun_info'              => 'required|url'
        ]);
        if (Carbon::createFromFormat('Y-m-d', $request->expiry_date) == Carbon::now()->format('Y-m-d')) {
            return "this product has finshed Expriate Date";
        }
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name, '-');
        $product->commun_info = $request->commun_info;
        $product->quantity = $request->quantity;
        if ($request->has('image_upload')) {
            $image = $request->image_upload;
            $path = $image->store('product-images', 'public');
            $product->image = $path;
        } else {
            $product->image = $request->image;
        }
        $product->category_id = $request->category_id;
        $product->expiry_date = $request->expiry_date;
        /*  $product->regular_price = $request->regular_price; */
        if (Carbon::createFromFormat('Y-m-d', $request->expiry_date)->subDays(30) >= Carbon::now()) {
            $product->regular_price = $request->regular_price - ($request->regular_price * 30 / 100);
        } elseif (Carbon::createFromFormat('Y-m-d', $request->expiry_date)->subDays(15) >= Carbon::now()) {
            $product->regular_price = $request->regular_price - ($request->regular_price * 15 / 100);
        } else  $product->regular_price = $request->regular_price - ($request->regular_price * 70 / 100);


        $product->save();
        return ['product' => $product];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->increment('views');
        if (Carbon::createFromFormat('Y-m-d', $product->expiry_date) == Carbon::now()->format('Y-m-d')) {
            $product->delete();
            return "this product has finshed Expriate Date";
        }
        return $product;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        /* $request->validate([
            'name'                     => 'required|min:4|max:255',
            'slug'                     => 'required|min:4',
            'regular_price'            => 'required|numeric|min:0',
            'commun_info'              => 'required|min:4',
            'image'                    => 'required_without:image_upload|url|nullable',
            'image_upload'             => 'required_without:image|file|image|nullable',
            'quantity'                 => 'required|numeric|min:0',
            'category_id'              => 'required|numeric|exists:categories,id',
            'commun_info'              => 'required|url'
        ]); */
        //dd("hello");
        //return $request->all();
        if (Carbon::createFromFormat('Y-m-d', $product->expiry_date) == Carbon::now()->format('Y-m-d')) {
            $product->delete();
            return "this product has finshed Expriate Date";
        }
        $product->name = $request->name;
        $product->slug = Str::slug($request->name, '-');
        $product->commun_info = $request->commun_info;
        $product->quantity = $request->quantity;
        if ($request->has('image_upload')) {
            $image = $request->image_upload;
            $path = $image->store('product-images', 'public');
            $product->image = $path;
        } else {
            $product->image = $request->image;
        }
        $product->category_id = $request->category_id;

        if (Carbon::createFromFormat('Y-m-d', $product->expiry_date)->subDays(30) >= Carbon::now()) {
            $product->regular_price = $request->regular_price - ($request->regular_price * 30 / 100);
        } elseif (Carbon::createFromFormat('Y-m-d', $product->expiry_date)->subDays(15) >= Carbon::now()) {
            $product->regular_price = $request->regular_price - ($request->regular_price * 15 / 100);
        } else  $product->regular_price = $request->regular_price - ($request->regular_price * 70 / 100);

        $product->save();
        return ['product' => $product];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //$product->delete();
        $product->delete();
        return "has delete";
    }
    public function restoreAll()
    {
        //return "sfgdhgjkh";
        Product::onlyTrashed()->restore();

        return "has return products";
    }
    public function restore($id)
    {
        $product = Product::findOrFail($id);
        $product->withTrashed()->restore();

        return "with returned";
    }
    public function search(Request $request)
    {
        $name =  $request->name;
        $search =  $request->search;
        if ($name == null) {
            return back();
        }
        if ($search == "name" or $search == null) {
            $product = Product::where('name', 'like', '%' . $name . '%')->get();
            return  ['product' => $product];
        }
        if ($search == "category") {
            $id = Category::where('name', 'like', '%' . $name . '%')->first()->id;
            $product = Product::where('category_id', $id)->get();
            return  ['product' => $product];
        }
        if ($search == "expiry_date") {
            $product = Product::where("expiry_date", 'like', $name)->get();
            return  ['product' => $product];
        }
    }
}
