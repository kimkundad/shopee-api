<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use App\Models\product;
use App\Models\shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ApiController extends Controller
{
    //
    public function get_category_all()
    {

        $objs = category::select('cat_name', 'image', 'id')->where('status', 1)->get();

        return response()->json([
            'category' => $objs,
            'full_paht_img' => env('APP_URL') . '/images/shopee/category/'
        ], 201);
    }
    public function get_all_product($id)
    {

        $objs = DB::table('shop_list_products')
            ->join('products', 'shop_list_products.product_id', '=', 'products.id')
            ->where('shop_list_products.shop_id', '=', $id)
            ->get();

        return response()->json([
            'product' => $objs,
        ], 201);
    }

    public function get_product(Request $request)
    {
        $product_id = $request->input('product_id');
        $shop_id = $request->input('shop_id');
        $objs = DB::table('shop_list_products')
            ->join('products', 'shop_list_products.product_id', '=', 'products.id')
            ->where('shop_list_products.shop_id', '=', $shop_id)
            ->where('products.id','=',$product_id)
            ->first()->map(function ($item) {
                $item->allImage = DB::table('product_images')->where('product_id', '=', $item->product_id)->get();
                return $item;
            });
        if($objs !== null &&$objs->type == 2){
            $objs->map(function ($item) {
                $item->allOption1 = DB::table('product_options')->where('product_id','=',$item->product_id)->get();
                return $item;
            });
        }/* else if($objs[0]->type == 3){
            $objs->map(function ($item) use ($id) {
                $item->allOption1 = DB::table('product_options')->where('product_id','=',$id)->get();
                return $item;
            });
            $objs->map(function ($item) use ($id) {
                $item->allOption1 = DB::table('product_options')->where('product_id','=',$id)->get();
                return $item;
            });
        } */

        return response()->json([
            'product' => $objs,
        ], 201);
    }
    public function set_active_product(Request $request)
    {
        try {
            // Validate input
            $id = $request->input('id');
            $checked = $request->input('checked');
            if (!$id || $checked === null) {
                throw new Exception('Invalid input');
            }
            if ($checked === "true") {
                product::where('id', '=', $id)->update(['active' => 1]);
            } else {
                product::where('id', '=', $id)->update(['active' => 0]);
            }

            $products = product::select('id', 'img_product', 'name_product', 'cost', 'price', 'maker', 'created_at', 'stock', 'active')->get();
            return response()->json([
                'product' => $products,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function set_active_allProduct(Request $request)
    {
        try {
            // Validate input
            $checked = filter_var($request->input('checked'), FILTER_VALIDATE_BOOLEAN);
            if ($checked) {
                product::query()->update(['active' => 1]);
            } else {
                product::query()->update(['active' => 0]);
            }
            $products = Product::all();
            return response()->json([
                'product' => $products,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function delete_product(Request $request)
    {
        try {
            // Validate input
            product::where('id', '=', $request->id)->delete();

            $products = product::all();
            return response()->json([
                'product' => $products,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function addProduct(Request $request)
    {
        try {
            $objs = new product();
            $objs->name_product = $request['name_product'];
            $objs->detail_product = $request['detail_product'];
            $objs->cost = $request['cost'];
            $objs->price = $request['price'];
            $objs->category = $request['category'];
            $objs->price_sales = $request['price_sales'];
            $objs->stock = $request['stock'];
            $objs->weight = $request['weight'];
            $objs->width_product = $request['width_product'];
            $objs->sku = $request['sku'];
            $objs->height_product = $request['height_product'];
            $objs->user_code = $request['user_code'];
            $objs->active = 0;
            $objs->save();

            $products = product::all();
            return response()->json([
                'product' => $products,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function get_shop_name()
    {

        $objs = shop::where('id', '=', 2)->get();

        return response()->json([
            'shop' => $objs,
        ], 201);
    }

    public function search_product(Request $request, $id)
    {
        $search = $request->input('search');
        /* $objs = DB::table('products')
            ->where(DB::raw("CONCAT(name_product, detail_product)"), 'LIKE', "%$search%")
            ->get(); */
        $objs = DB::table('shop_list_products')
            ->join('products', 'shop_list_products.product_id', '=', 'products.id')
            ->where('shop_list_products.shop_id', '=', $id)
            ->whereRaw("CONCAT(products.name_product, products.detail_product) LIKE ?", ["%$search%"])
            ->get();
        return response()->json([
            'product' => $objs,
        ], 201);
    }
}
