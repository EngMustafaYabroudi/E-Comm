<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('auth:sanctum');
    }
    public function index()
    {

        return Product::paginate(4);
        /*  return  Product::where('deleted_at', '2021-12-17 08:39:24')->get(); */
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function  create()
    {

        //$categories = Category::all();
        return  Category::all();/* view('create', ['categories' => $categories]) */;
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
            'regular_price'            => 'required|numeric|min:0',
            'commun_info'              => 'required|min:4|url',
            //'image'                    => 'required_without:image_upload|url|nullable',
            'image'                    => 'required|file|image',
            'quantity'                 => 'required|numeric|min:0',
            'category_id'              => 'required|numeric|exists:categories,id',
            'expiry_date'              => 'required|date',
        ]);
        if ($request->expiry_date <= Carbon::now()->format('Y-m-d')) {
            return "this product has finshed Expriate Date";
        }
        $product = new Product();


        $product->name = $request->name;
        $product->commun_info = $request->commun_info;
        $product->quantity = $request->quantity;
<<<<<<< HEAD
        if ($request->has('image_upload')) {
            $image = $request->image_upload;
            $path = $image->store('product-images','public');
            $product->image = $path;
        } else {
            $product->image = $request->image;
        }
=======

        $image = $request->image;
        $path = $image->store('product-images', 'public');
        $product->image = $path;


>>>>>>> 925cc81f07e6bac08f26d15ed1685d6ecac5f9e4
        $product->category_id = $request->category_id;
        $product->expiry_date = $request->expiry_date;
        $product->regular_price = $request->regular_price;
        if (Carbon::createFromFormat('Y-m-d', $request->expiry_date)->subDays(30) >= Carbon::now()) {
            $product->sale_price = $request->regular_price - ($request->regular_price * 30 / 100);
        } elseif (Carbon::createFromFormat('Y-m-d', $request->expiry_date)->subDays(15) >= Carbon::now()) {
            $product->sale_price = $request->regular_price - ($request->regular_price * 15 / 100);
        } else  $product->sale_price = $request->regular_price - ($request->regular_price * 70 / 100);


        $product->save();


        $prod_user = new ProductUser();
        $prod_user->user_id    =   Auth::user()->id;
        $prod_user->product_id =   $product->id;
        $prod_user->is_user   =   1;
        $prod_user->save();

        return ['product' => $product/* , 'prod_user' => $prod_user */];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function comment(Request $request, $id)
    {
        $prod_user = new ProductUser();
        $prod_user->user_id = Auth::user()->id;
        $prod_user->product_id = $id;
        $prod_user->comment = $request->comment;
        //$prod_user->check = 0;
        $prod_user->save();
        return ['comment' => $prod_user->comment];
    }

    public function showComments($id)
    {
        $prod_users = ProductUser::where('product_id', $id)->get();
        return  ['comments' => $prod_users];
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        if ($product->expiry_date <= Carbon::now()->format('Y-m-d')) {
            $product->delete();
            return "this product has finshed Expriate Date";
        }
        $user = ProductUser::where('product_id', $id)->get();
        $inc = 0;

        foreach ($user as $us) {
            if ($us->user_id == Auth::user()->id && $us->check == 0) {
                if ($inc == 0) {
                    $product->increment('views');
                    $inc = 1;
                }
                $product->increment('views');
                $us->update([
                    'user_id'    =>  Auth::user()->id,
                    'product_id' =>  $id,
                    'check'      => 1
                ]);
            }
        }
        if (ProductUser::where('user_id', Auth::user()->id)->get() == null) {
            $prod = ProductUser::create([
                'user_id'    =>  Auth::user()->id,
                'product_id' => $id,
                'check'      => 1,
                'is_user'    => false
            ]);
            $product->increment('views');
        } else {
            $variable = ProductUser::where('user_id', Auth::user()->id)->get();
            //return $variable;
            foreach ($variable as $key) {
                if ($key->product_id == $id) {
                    return $product;
                }
            }
            $prod = ProductUser::create([
                'user_id'    =>  Auth::user()->id,
                'product_id' => $id,
                'check'      => 1,
                'is_user'    => false
            ]);
            $product->increment('views');
        }




        /* "name":"alia",
            "expiry_date":"2022-8-25",
            "image":"http://127.0.0.1:8000/api/products",
            "commun_info":"http://127.0.0.1:8000/api/products",
            "category_id":1,
            "regular_price":444,
            "quantity":444 */


        return ['product' => $product];
    }

    public function getProductToUSer()
    {
        $products = ProductUser::where('user_id', Auth::user()->id)->get();
        $prod = [];
        $i = 0;
        foreach ($products as $product) {
            if ($product->is_user == 1) {
                $hasProduct = Product::findOrFail($product->product_id);
                $prod[$i] = $hasProduct;
                $i++;
            }
        }
        /*  print_r($prod); */
        return  $prod;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->expiry_date <= Carbon::now()->format('Y-m-d')) {
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
        $product->sale_price = $request->sale_price;
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
    public function destroy($id)
    {

        $product = Product::findOrFail($id);

        $product->delete();
        return "has deleted";
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
    public function sort(Request $request)
    {
        $sort =  $request->sort;
        return  Product::orderBy($sort, 'desc')->limit(6)->get();
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
