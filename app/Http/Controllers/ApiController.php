<?php

namespace App\Http\Controllers;

use App\Models\orders;
use App\Models\addresses;
use App\Models\order_details;
use App\Models\product_option;
use Illuminate\Http\Request;
use App\Models\category;
use App\Models\product;
use App\Models\shop;
use App\Models\carts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    //ดึงข้อมูลสินค้าทั้งหมดมาแสดงในหน้าร้าน
    public function get_all_product($id)
    {

        $objs = DB::table('shop_list_products')
            ->join('products', 'shop_list_products.product_id', '=', 'products.id')
            ->where('shop_list_products.shop_id', '=', $id)
            ->where('products.active', '=', 1)
            ->get();

        return response()->json([
            'product' => $objs,
        ], 201);
    }

    // ดึงข้อมูลสินค้าทั้งหมด
    public function get_allproduct()
    {

        $objs = DB::table('products')->select('*')->get();

        return response()->json([
            'product' => $objs,
        ], 201);
    }

    public function get_all_shops()
    {

        $objs = DB::table('shops')
            ->select('*')
            ->get();

        return response()->json([
            'shops' => $objs,
        ], 201);
    }

    public function get_search_shops(Request $request)
    {
        $search = $request->query('search');

        if ($search != 'null') {
            $stores = shop::when($search, function ($query, $search) {
                return $query->where('name_shop', 'like', '%' . $search . '%');
            })->get();
        } else {
            $stores = DB::table('shops')->select('*')->get();
        }

        return response()->json([
            'shops' => $stores,
        ], 201);
    }

    public function get_search_date_shops(Request $request)
    {
        $search = $request->query('search');

        if ($search != 'null') {
            $stores = shop::when($search, function ($query, $search) {
                $query->whereDate('created_at', $search);
            })->get();
        } else {
            $stores = DB::table('shops')->select('*')->get();
        }

        return response()->json([
            'shops' => $stores,
        ], 201);
    }

    public function get_filter_shops(Request $request)
    {
        $type = $request->query('type');

        if ($type == 'asc') {
            $stores = DB::table('shops')
                ->select('*')
                ->orderBy('name_shop', 'asc')
                ->get();
        } else if ($type == 'createdDateShop') {
            $stores = DB::table('shops')
                ->select('*')
                ->orderBy('created_at', 'asc')
                ->get();
        } else if ($type == 'modifiedDateShop') {
            $stores = DB::table('shops')
                ->select('*')
                ->orderBy('updated_at', 'asc')
                ->get();
        } else {
            $stores = DB::table('shops')
                ->select('*')
                ->get();
        }

        return response()->json([
            'shops' => $stores,
        ], 201);
    }

    // ดึงข้อมูลสินค้ามาแสดงหน้ารายละเอียดสินค้า
    public function get_product(Request $request)
    {
        if ($request->carts !== null) {
            $carts_id = $request->carts;
            $products = DB::table('carts')
                ->join('shops', 'carts.shop_id', '=', 'shops.id')
                ->select([
                    'shops.id',
                    'shops.name_shop AS name_shop',
                ])
                ->orderByRaw('MAX(carts.created_at) DESC')
                ->groupBy('shops.id', 'name_shop')
                ->whereIn('carts.id', $carts_id)
                ->get()
                ->map(function ($item) use ($carts_id) {
                    $item->product = DB::table('carts')
                        ->join('shop_list_products', 'shop_list_products.shop_id', '=', 'carts.shop_id')
                        ->join('products', 'products.id', '=', 'carts.product_id')
                        ->leftjoin('product_options', 'product_options.id', '=', 'carts.product_options_id')
                        ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'carts.product_suboptions_id')
                        ->select([
                            DB::raw('DISTINCT carts.id'),
                            'carts.product_id',
                            'products.name_product' => 'name_product',
                            'products.detail_product' => 'detail_product',
                            'products.price AS price_type_1',
                            'products.type AS type_product',
                            'products.price_sales' => 'price_sales',
                            'products.img_product' => 'img_product',
                            'products.option1' => 'option1',
                            'products.option2' => 'option2',
                            'carts.num' => 'num',
                            'product_options.id as option1Id',
                            'product_suboptions.id as option2Id',
                            'product_options.op_name' => 'op_name',
                            'product_options.price AS price_type_2',
                            'product_suboptions.sub_op_name' => 'sub_op_name',
                            'product_suboptions.price AS price_type_3',
                        ])
                        ->whereIn('carts.id', $carts_id)
                        ->get();
                    return $item;
                });

            return response()->json([
                'product' => $products
            ], 201);
        }


        $product_id = $request->product_id;
        $shop_id = $request->shop_id;
        if ($shop_id !== null) {
            $objs = DB::table('shop_list_products')
                ->join('products', 'shop_list_products.product_id', '=', 'products.id')
                ->where('shop_list_products.shop_id', '=', $shop_id)
                ->where('products.id', '=', $product_id)
                ->where('products.active', '=', 1)
                ->get();
        } else {
            $objs = DB::table('products')
                ->select('*', 'id as product_id')
                ->where('products.id', '=', $product_id)
                ->get();
        }

        $objs->map(function ($item) {
            $item->allImage = DB::table('product_images')->where('product_id', '=', $item->product_id)->get();
            return $item;
        });
        if ($objs !== null && $objs[0]->type == 2) {
            $objs->map(function ($item) {
                $item->allOption1 = DB::table('product_options')->where('product_id', '=', $item->product_id)->where('status', '=', 1)->get();
                return $item;
            });
        } else if ($objs !== null && $objs[0]->type == 3) {
            $objs->map(function ($item) {
                $item->allOption1 = DB::table('product_options')->where('product_id', '=', $item->product_id)->where('status', '=', 1)->get();
                $item->allOption1->map(function ($item2) {
                    $item2->allOption2 = DB::table('product_suboptions')->where('op_id', '=', $item2->id)->where('status', '=', 1)->get();
                    return $item2;
                });
                return $item;
            });
            $allOption = DB::table('product_options')->select('id')->where('product_id', '=', $product_id)->where('status', '=', 1)->pluck('id');
            $allSubOption = DB::table('product_suboptions')
                ->whereIn('op_id', $allOption)
                ->where('status', '=', 1)
                ->select('sub_op_name')
                ->distinct()
                ->get();
        }

        if ($objs[0]->type == 3) {
            return response()->json([
                'product' => $objs,
                'allSupOption' => $allSubOption,
            ], 201);
        } else {
            return response()->json([
                'product' => $objs,
                'allSupOption' => [],
            ], 201);
        }
    }

    // สร้าง order
    public function created_order(Request $request)
    {
        if ($request->product_id !== null) {
            $order = new orders();
            $order->user_id = $request->user_id;
            $order->shop_id = $request->shop_id;
            $order->order_detail_id = 0;
            $order->num = $request->num;
            $order->price = $request->total;
            $order->discount = $request->discount;
            $order->status = $request->status;
            $order->save();

            $order_detail = new order_details();
            $order_detail->product_id = $request->product_id;
            $order_detail->user_id = $request->user_id;
            $order_detail->order_id = $order->id;
            $order_detail->option1 = $request->option1;
            $order_detail->option2 = $request->option2;
            $order_detail->num = $request->num;
            $order_detail->save();

            return response()->json([
                'order' => $order
            ], 201);
        } else {
            $products = json_decode($request->products, true);
            $order = new orders();
            $order->user_id = $request->user_id;
            $order->shop_id = $request->shop_id;
            $order->order_detail_id = 0;
            $order->num = $request->num;
            $order->price = $request->total;
            $order->discount = $request->discount;
            $order->status = $request->status;
            $order->save();

            foreach ($products as $index => $item) {
                foreach ($item['product'] as $subIndex => $subItem) {
                    $order_detail = new order_details();
                    $order_detail->product_id = $subItem['product_id'];
                    $order_detail->user_id = $request->user_id;
                    $order_detail->order_id = $order->id;
                    $order_detail->option1 = $subItem['option1Id'];
                    $order_detail->option2 = $subItem['option2Id'];
                    $order_detail->num = $subItem['num'];
                    $order_detail->save();
                }
            }
            return response()->json([
                'order' => $order
            ], 201);
        }
    }

    // ดึงข้อมูล order
    public function get_order(Request $request)
    {
        $user_id = $request->input('user_id');
        $shop_id = $request->input('shop_id');

        $orders = DB::table('orders')
            ->join('shops', 'shops.id', '=', 'orders.shop_id')
            ->where('orders.user_id', '=', $user_id)
            ->where('orders.shop_id', '=', $shop_id)
            ->orderBy('orders.updated_at', 'desc')
            ->select([
                'shops.id as id_shop',
                'shops.name_shop as name_shop',
                'orders.status',
                'orders.price',
                'orders.num',
                'orders.discount',
                'orders.id'
            ])
            ->get()
            ->map(function ($item) {
                $item->item = DB::table('order_details')
                    ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                    ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
                    ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
                    ->select([
                        'products.name_product',
                        'products.detail_product',
                        'products.img_product',
                        'products.type',
                        'products.price',
                        'order_details.num',
                        'products.option1',
                        'products.option2',
                        'product_options.op_name',
                        'product_options.price as op_price',
                        'product_suboptions.sub_op_name',
                        'product_suboptions.price as sub_op_price',
                    ])
                    ->where('order_details.order_id', '=', $item->id)
                    ->orderBy('order_details.updated_at', 'desc')
                    ->get();

                return $item;
            });

        return response()->json([
            'orders' => $orders
        ], 201);
    }

    public function getUser(Request $request)
    {
        $user = DB::table('users')->where('id', '=', $request->user_id)->first();
        return response()->json([
            'user' => $user,
        ], 201);
    }
    // แก้ไขข้อมูลผู้ใช้
    public function editUser(Request $request)
    {
        DB::table('users')->where('id', '=', $request->user_id)->update([
            'name' => $request->name,
        ]);
    }

    // ตั้งค่าเปิดปิดใช้งานสินค้า
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

    // ตั้งค่าเปิดปิดใช้งานสินค้าทั้งหมด
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

    // ลบสินค้าในหน้าคลังสินค้าหลังบ้าน
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

    // เพิ่มสินค้า
    public function addProduct(Request $request)
    {
        if ($request->option1 == "ตัวเลือกที่ 1" || $request->option2 == "ตัวเลือกที่ 2") {
            $product = new product();
            $product->name_product = $request->name_product;
            $product->detail_product = $request->detail_product;
            $product->price = $request->price;
            $product->price_sales = $request->price_sales;
            $product->cost = $request->cost;
            $product->stock = $request->stock;
            $product->weight = $request->weight;
            $product->width_product = $request->width_product;
            $product->length_product = $request->length_product;
            $product->height_product = $request->height_product;
            $product->sku = $request->sku;
            $product->category = $request->category;
            $product->type = 1;
            $product->active = 1;
        } else if ($request->sub_option == "ตัวเลือกที่ 2") {
            $product = new product();
            $product->name_product = $request->name_product;
            $product->detail_product = $request->detail_product;
            $product->price = $request->price;
            $product->price_sales = $request->price_sales;
            $product->cost = $request->cost;
            $product->stock = $request->stock;
            $product->weight = $request->weight;
            $product->width_product = $request->width_product;
            $product->length_product = $request->length_product;
            $product->height_product = $request->height_product;
            $product->sku = $request->sku;
            $product->option1 = $request->option1;
            $product->category = $request->category;
            $product->type = 2;
            $product->active = 1;
        } else {
            $product = new product();
            $product->name_product = $request->name_product;
            $product->detail_product = $request->detail_product;
            $product->price = $request->price;
            $product->price_sales = $request->price_sales;
            $product->cost = $request->cost;
            $product->stock = $request->stock;
            $product->weight = $request->weight;
            $product->width_product = $request->width_product;
            $product->length_product = $request->length_product;
            $product->height_product = $request->height_product;
            $product->sku = $request->sku;
            $product->option1 = $request->option1;
            $product->option1 = $request->option2;
            $product->category = $request->category;
            $product->type = 3;
            $product->active = 1;
        }


        $files = $request->file('file');
        $filePaths = null;
        $product_id = 0;
        $first = true;
        $dataOption = json_decode($request->dataOption, true);
        foreach ($files as $index => $file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('do_spaces')->put('shopee/products/' . $file->hashName(), $image, 'public');
            $filePaths = $file->hashName();
            if ($first) {
                $product->img_product = $filePaths;
                $product->save();
                $product_id = product::select('id')->orderBy('created_at', 'desc')->first();
                $first = false;
            } else {
                $id_image = DB::table('product_images')->insertGetId([
                    'image' => $filePaths,
                    'product_id' => $product_id['id'],
                    'status' => 0,
                ]);

                foreach ($dataOption as $item) {
                    if ($item['indexImageOption'] == $index) {
                        $pro_option = new product_option;
                        $pro_option->product_id = $product->id;
                        $pro_option->img_id = $id_image;
                        $pro_option->op_name = $item['nameOption'];
                        $pro_option->img_name = $filePaths;
                        $pro_option->price = $item['priceOption'];
                        $pro_option->stock = $item['stockOption'];
                        $pro_option->sku = $item['skuOption'];
                        $pro_option->status = 1;
                        $pro_option->save();
                        /* $id_pro_option = DB::table('product_options')->lastInsertId([
                            'product_id' => $product->id,
                            'img_id' => $id_image,
                            'op_name' => $item['nameOption'],
                            'img_name' => $filePaths,
                            'price' => $item['priceOption'],
                            'stock' => $item['stockOption'],
                            'sku' => $item['skuOption'],
                            'status' => 1,
                        ]); */
                        foreach ($item['subOption'] as $subItem) {
                            DB::table('product_suboptions')->insert([
                                'op_id' => $pro_option->id,
                                'sub_op_name' => $subItem['nameSubOption'],
                                'price' => $subItem['priceSubOption'],
                                'stock' => $subItem['stockSubOption'],
                                'sku' => $subItem['skuSubOption'],
                                'status' => 1,
                            ]);
                        }
                    }
                }
            }
        }
        return response()->json([
            'product' => 'a',
        ], 201);
    }

    // แก้ไขข้อมูลสินค้า
    public function editProduct(Request $request, $id)
    {
        DB::table('products')
            ->join('product_options', 'product_options.product_id', '=', 'products.id')
            ->join('product_suboptions', 'product_suboptions.op_id', '=', 'product_option.id')
            ->join('shop_list_products', 'products.id', '=', 'shop_list_products.product_id')
            ->join('product_images', 'product_images.product_id', '=', 'products.id')
            ->where('products.id', '=', $id)
            ->delete();
    }

    // ดึงข้อมูลร้านค้า
    public function get_shop_name($id)
    {

        $objs = shop::where('url_shop', '=', $id)->get();

        return response()->json([
            'shop' => $objs,
        ], 201);
    }

    // ค้นหาสินค้าในหน้าแรกของร้านค้า
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

    // เพิ่มสินค้าลงในรถเข็น
    public function addProductToCart(Request $request)
    {
        $cartItem = DB::table('carts')->where([
            ['product_id', '=', $request->input('productId')],
            ['product_options_id', '=', $request->input('productOptionId')],
            ['product_suboptions_id', '=', $request->input('productSubOptionId')],
        ])->first();
        if ($cartItem !== null) {
            $sum = $cartItem->num + $request->input('num');
            DB::table('carts')->where('id', '=', $cartItem->id)->update([
                'num' => $sum
            ]);
        } else {
            $objs = new carts();
            $objs->user_id = $request->input('user_id');
            $objs->shop_id = $request->input('shopId');
            $objs->product_id = $request->input('productId');
            $objs->product_options_id = $request->input('productOptionId');
            $objs->product_suboptions_id = $request->input('productSubOptionId');
            $objs->num = $request->input('num');
            $objs->save();
        }
        return response()->json([
            'status' => $cartItem,
        ], 201);
    }

    // ดึงข้อมูลในรถเข็นมาแสดง
    public function getAllCartItem(Request $request)
    {
        $objs = DB::table('carts')
            ->join('shops', 'carts.shop_id', '=', 'shops.id')
            ->where('carts.user_id', '=', $request->user_id)
            ->select([
                'shops.id',
                'shops.name_shop AS name_shop',
            ])
            ->orderByRaw('MAX(carts.created_at) DESC')
            ->groupBy('shops.id', 'name_shop')
            ->get()
            ->map(function ($item) use ($request) {
                $item->product = DB::table('carts')
                    ->join('shop_list_products', 'shop_list_products.shop_id', '=', 'carts.shop_id')
                    ->join('products', 'products.id', '=', 'carts.product_id')
                    ->leftjoin('product_options', 'product_options.id', '=', 'carts.product_options_id')
                    ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'carts.product_suboptions_id')
                    ->select([
                        DB::raw('DISTINCT carts.id'),
                        'carts.product_id',
                        'products.name_product' => 'name_product',
                        'products.detail_product' => 'detail_product',
                        'products.price AS price_type_1',
                        'products.type AS type_product',
                        'products.price_sales' => 'price_sales',
                        'products.img_product' => 'img_product',
                        'products.option1' => 'option1',
                        'products.option2' => 'option2',
                        'carts.num' => 'num',
                        'product_options.op_name' => 'op_name',
                        'product_options.price AS price_type_2',
                        'product_suboptions.sub_op_name' => 'sub_op_name',
                        'product_suboptions.price AS price_type_3',
                    ])
                    ->where('shop_list_products.shop_id', '=', $item->id)
                    ->where('carts.user_id', '=', $request->user_id)
                    ->get();
                return $item;
            });
        return response()->json([
            'cartItem' => $objs,
        ], 201);
    }

    // ลบสินค้าออกจากรถเข็น
    public function deleteItemCart(Request $request)
    {
        DB::table('carts')->whereIn('id', $request->cart_id)->delete();

        $objs = DB::table('carts')
            ->join('shops', 'carts.shop_id', '=', 'shops.id')
            ->select([
                'shops.id',
                'shops.name_shop AS name_shop',
            ])
            ->orderByRaw('MAX(carts.created_at) DESC')
            ->groupBy('shops.id', 'name_shop')
            ->get()
            ->map(function ($item) {
                $item->product = DB::table('carts')
                    ->join('shop_list_products', 'shop_list_products.shop_id', '=', 'carts.shop_id')
                    ->join('products', 'products.id', '=', 'carts.product_id')
                    ->leftjoin('product_options', 'product_options.id', '=', 'carts.product_options_id')
                    ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'carts.product_suboptions_id')
                    ->select([
                        DB::raw('DISTINCT carts.id'),
                        'products.name_product' => 'name_product',
                        'products.detail_product' => 'detail_product',
                        'products.price AS price_type_1',
                        'products.type AS type_product',
                        'products.price_sales' => 'price_sales',
                        'products.img_product' => 'img_product',
                        'products.option1' => 'option1',
                        'products.option2' => 'option2',
                        'carts.num' => 'num',
                        'product_options.op_name' => 'op_name',
                        'product_options.price AS price_type_2',
                        'product_suboptions.sub_op_name' => 'sub_op_name',
                        'product_suboptions.price AS price_type_3',
                    ])
                    ->where('shop_list_products.shop_id', '=', $item->id)
                    ->get();
                return $item;
            });
        return response()->json([
            'cartItem' => $objs,
        ]);
    }

    // เพิ่มที่อยู่ใหม่
    public function newAddress(Request $request)
    {
        if ($request->default == 1) {
            DB::table('addresses')->where('user_id', '=', $request->user_id)->update([
                'default' => 0
            ]);
        }
        $newAddress = new addresses();
        $newAddress->user_id = $request->user_id;
        $newAddress->name = $request->name;
        $newAddress->tel = $request->tel;
        $newAddress->address = $request->address;
        $newAddress->sub_district = $request->subDistrict;
        $newAddress->district = $request->district;
        $newAddress->province = $request->province;
        $newAddress->postcode = $request->postcode;
        $newAddress->default = $request->default;
        $newAddress->save();
    }

    // ตั้งค่าที่อยู่เริ่มต้น
    public function setDefaultAddress(Request $request) {
        DB::table('addresses')->where('user_id', '=', $request->user_id)->update([
            'default' => 0
        ]);
        DB::table('addresses')->where('id','=',$request->id)->update(['default'=>1]);
        $address = DB::table('addresses')->where('user_id','=',$request->user_id)->get();
        return response()->json([
            'address' => $address,
        ],201);
    }

    // ดึงข้อมูลที่อยู่
    public function getAddress(Request $request) {
        $address = DB::table('addresses')->where('user_id','=',$request->user_id)->where('default','=',1)->first();

        return response()->json([
            'address' => $address,
        ],201);
    }

    // ดึงข้อมูลที่อยู่ทั้งหมด
    public function getAllAddress(Request $request)
    {
        $address = DB::table('addresses')->where('user_id', '=', $request->user_id)->get();

        return response()->json([
            'address' => $address,
        ], 201);
    }
    // -------------------------------ดึงข้อมูลของ users และ role ของ users ออกมาทั้งหมด create by อั้นเอง----------------------------
    public function getAllUsers()
    {
        $objs = DB::table('users')->select('users.*', 'users.id as userID', 'roles.name as role_name', 'users.name as user_name', 'users.created_at as user_created_at', 'sub_admins.*')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->join('sub_admins', 'sub_admins.sub_admin', '=', 'users.id')
            ->orderBy('users.id', 'desc')
            ->get();

        return response()->json([
            'users' => $objs,
        ], 201);
    }

    // -------------------------------ฟังก์ชันสร้าง Sub-Admin create by อั้นเอง---------------------------------
    public function createSubAdmin(Request $request)
    {
        $permission = [
            'set_permission_dashboard' => $request['set_permission_dashboard'],
            'set_permission_my_shop' => $request['set_permission_my_shop'],
            'set_permission_stock' => $request['set_permission_stock'],
            'set_permission_report' => $request['set_permission_report'],
            'set_permission_admin_manage' => $request['set_permission_admin_manage'],
            'set_permission_settings' => $request['set_permission_settings']
        ];

        $json_permission = json_encode($permission);

        DB::table('users')->insert([
            'name' => $request['name_sub_admin'],
            'email' => $request['email_sub_admin'],
            'password' => Hash::make($request['password_sub_admin']),
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // รับค่า ID ของแถวที่เพิ่งถูกเพิ่มล่าสุดเข้าไปในตาราง users
        $lastInsertId = DB::getPdo()->lastInsertId();

        // insert role sub admin
        DB::table('role_user')->insert([
            'role_id' => 4,
            'user_id' => $lastInsertId
        ]);

        // set permissions and owner admin
        DB::table('sub_admins')->insert([
            'owner_admin' => 1,
            'sub_admin' => $lastInsertId,
            'permission' => $json_permission,
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'success' => 'Insert Sub-Admin successfully!',
        ], 201);
    }

    // -------------------------------ฟังก์ชันแก้ไขข้อมูล Sub-Admin create by อั้นเอง-------------------------
    public function updateSubAdmin(Request $request)
    {
        $userID = $request['userID'];
        $password = $request['password_sub_admin'];
        $permission = [
            'set_permission_dashboard' => $request['set_permission_dashboard'],
            'set_permission_my_shop' => $request['set_permission_my_shop'],
            'set_permission_stock' => $request['set_permission_stock'],
            'set_permission_report' => $request['set_permission_report'],
            'set_permission_admin_manage' => $request['set_permission_admin_manage'],
            'set_permission_settings' => $request['set_permission_settings']
        ];
        $json_permission = json_encode($permission);
        if ($password != '') {
            DB::table('users')->where('id', $userID)->update([
                'name' => $request['name_sub_admin'],
                'email' => $request['email_sub_admin'],
                'password' => Hash::make($password),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            DB::table('users')->where('id', $userID)->update([
                'name' => $request['name_sub_admin'],
                'email' => $request['email_sub_admin'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        // set permissions and owner admin
        DB::table('sub_admins')->where('sub_admin', $userID)->update([
            'permission' => $json_permission,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json([
            'success' => 'Update Sub-Admin successfully!',
        ], 201);
    }

    // ------------------------------------ฟังก์ชันลบข้อมูล Sub-Admin create by อั้นเอง----------------------------------
    public function deleteSubAdmin(Request $request)
    {
        // delete user role sub-admin
        DB::table('users')->where('id', $request['userID'])->delete();

        // delete role user sub-admin
        DB::table('role_user')->where('user_id', $request['userID'])->delete();

        // delete permissions and owner admin
        DB::table('sub_admins')->where('sub_admin', $request['userID'])->delete();

        return response()->json([
            'success' => 'Delete Sub-Admin successfully!',
        ], 201);
    }

    // ------------------------------------ฟังก์ชัน filter ค้นหาข้อมูล Sub-admin จากวันที่สร้าง create by อั้นเอง-------------------------------
    public function getSearchDateSubAdmin(Request $request)
    {
        $search = $request->query('search');

        if ($search != 'null') {
            $objs = DB::table('users')->select('users.*', 'users.id as userID', 'roles.name as role_name', 'users.name as user_name', 'users.created_at as user_created_at', 'sub_admins.*')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->join('sub_admins', 'sub_admins.sub_admin', '=', 'users.id')
                ->whereDate('users.created_at', $search)
                ->orderBy('users.id', 'desc')
                ->get();
        } else {
            $objs = DB::table('users')->select('users.*', 'users.id as userID', 'roles.name as role_name', 'users.name as user_name', 'users.created_at as user_created_at', 'sub_admins.*')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->join('sub_admins', 'sub_admins.sub_admin', '=', 'users.id')
                ->orderBy('users.id', 'desc')
                ->get();
        }

        return response()->json([
            'users' => $objs,
        ], 201);
    }

    // --------------------------------ฟังก์ชันค้นหาข้อมูล Sub-Admin จากชื่อของ sub-admin create by อั้นเอง------------------------------
    public function getSearchName(Request $request)
    {
        $search = $request->query('search');

        if ($search != 'null') {
            $objs = DB::table('users')->select('users.*', 'users.id as userID', 'roles.name as role_name', 'users.name as user_name', 'users.created_at as user_created_at', 'sub_admins.*')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->join('sub_admins', 'sub_admins.sub_admin', '=', 'users.id')
                ->where('users.name', 'like', '%' . $search . '%')
                ->orderBy('users.id', 'desc')
                ->get();
        } else {
            $objs = DB::table('users')->select('users.*', 'users.id as userID', 'roles.name as role_name', 'users.name as user_name', 'users.created_at as user_created_at', 'sub_admins.*')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->join('sub_admins', 'sub_admins.sub_admin', '=', 'users.id')
                ->orderBy('users.id', 'desc')
                ->get();
        }

        return response()->json([
            'users' => $objs,
        ], 201);
    }

    // ---------------------------------ฟังก์ชันสร้างร้านค้า create by อั้นเอง----------------------------
    public function createShop(Request $request)
    {
        if ($request->hasFile('file') && $request->hasFile('file2')) {
            $files = $request->file('file');
            $filePaths = null;
            foreach ($files as $file) {
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $image = Image::make($file->getRealPath());
                $image->resize(250, 250, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->stream();
                Storage::disk('do_spaces')->put('shopee/shop/' . $file->hashName(), $image, 'public');
                $filePaths = $file->hashName();
            }

            $files2 = $request->file('file2');
            $filePaths2 = null;
            foreach ($files2 as $file2) {
                $filename2 = time() . '.' . $file2->getClientOriginalExtension();
                $image2 = Image::make($file2->getRealPath());
                $image2->resize(450, 200, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image2->stream();
                Storage::disk('do_spaces')->put('shopee/cover_img_shop/' . $file2->hashName(), $image2, 'public');
                $filePaths2 = $file2->hashName();
            }

            DB::table('shops')->insert([
                'name_shop' => $request['nameShop'],
                'detail_shop' => $request['detailShop'],
                'img_shop' => $filePaths,
                'cover_img_shop' => $filePaths2,
                'theme' => $request->themeShop,
                'created_at' =>  date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            // รับค่า ID ของแถวที่เพิ่งถูกเพิ่มล่าสุดเข้าไปในตาราง users
            $lastInsertId = DB::getPdo()->lastInsertId();

            if ($request->input('category')) {
                $InputCategory = $request->input('category');
                if (is_array($InputCategory)) {
                    foreach ($InputCategory as $category) {
                        DB::table('categorys_shop')->insert([
                            'category_name' => $category,
                            'shop_id' => $lastInsertId,
                            'created_at' =>  date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }


            return response()->json([
                'success' => 'Create Shop successfully!',
            ], 201);
        } else {
            return response()->json([
                'errors' => 'Insert Shop Errors!',
            ], 500);
        }
    }

    // -----------------------------------ฟังก์ชันแก้ไขข้อมูลร้านค้า create by อั้นเอง---------------------------------
    public function editShop(Request $request)
    {
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $filePaths = null;
            foreach ($files as $file) {
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $image = Image::make($file->getRealPath());
                $image->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->stream();
                Storage::disk('do_spaces')->put('shopee/shop/' . $file->hashName(), $image, 'public');
                $filePaths = $file->hashName();
            }

            DB::table('shops')->where('id', $request['shopID'])->update([
                'img_shop' => $filePaths,
            ]);
        }

        if ($request->hasFile('file2')) {
            $files2 = $request->file('file2');
            $filePaths2 = null;
            foreach ($files2 as $file2) {
                $filename2 = time() . '.' . $file2->getClientOriginalExtension();
                $image2 = Image::make($file2->getRealPath());
                $image2->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image2->stream();
                Storage::disk('do_spaces')->put('shopee/cover_img_shop/' . $file2->hashName(), $image2, 'public');
                $filePaths2 = $file2->hashName();
            }

            DB::table('shops')->where('id', $request['shopID'])->update([
                'cover_img_shop' => $filePaths2,
            ]);
        }

        DB::table('shops')->where('id', $request['shopID'])->update([
            'name_shop' => $request['editNameShop'],
            'detail_shop' => $request['editDetailShop'],
            'theme' => $request->editThemeShop,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($request->input('selectID')) {
            DB::table('shop_list_products')->where('shop_id', $request['shopID'])->delete();
            $selectedProducts = $request->input('selectID');
            if (is_array($selectedProducts)) {
                foreach ($selectedProducts as $productId) {
                    DB::table('shop_list_products')->insert([
                        'product_id' => $productId,
                        'shop_id' => $request->shopID,
                        'created_at' =>  date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => 'Edit Shop successfully!',
        ], 201);
    }

    // ------------------------------------ฟังก์ชันลบร้านค้า create by อั้นเอง------------------------------------
    public function DeleteShop(Request $request)
    {
        $shopID = $request['shopID'];

        $imgshop = DB::table('shops')->select('*')->where('id', $shopID)->first();

        Storage::disk('do_spaces')->delete('shopee/cover_img_shop/' . $imgshop->cover_img_shop);
        Storage::disk('do_spaces')->delete('shopee/shop/' . $imgshop->img_shop);

        DB::table('shops')->where('id', $shopID)->delete();

        DB::table('shop_list_products')->where('shop_id', $shopID)->delete();

        DB::table('categorys_shop')->where('shop_id', $shopID)->delete();

        return response()->json([
            'success' => 'Delete Shop successfully!',
        ], 201);
    }

    // ----------------------------------ฟังก์ชันเ เปลี่ยนสถานะ เปิด/ปิด ของร้านค้า create by อั้นเอง----------------------------
    public function changeStatusShop(Request $request)
    {
        $shopID = $request['shopID'];
        DB::table('shops')->where('id', $shopID)->update([
            'status' => $request['newStatus'],
        ]);
        return response()->json([
            'success' => 'Change Status Shop successfully!',
        ], 201);
    }

    //-------------------- ดึงข้อมูล Product ของร้านค้านั้นๆ -------------------------
    public function getListProduct($shopid)
    {
        $shopID = $shopid;
        $list_products = DB::table('shop_list_products')->select('*')->where('shop_id', $shopID)->get();
        return response()->json([
            'list_products' => $list_products,
            'success' => 'Get List Product Shop successfully!',
        ], 201);
    }

    //-------------------- ดึงข้อมูล หมวดหมู่ ของร้านค้านั้นๆ -------------------------
    public function getCategoryShop($shopid)
    {
        $shopID = $shopid;
        $category_shop = DB::table('categorys_shop')->select('category_name')->where('shop_id', $shopID)->pluck('category_name')->toArray();
        return response()->json([
            'category_shop' => $category_shop,
            'success' => 'Get Category Shop successfully!',
        ], 201);
    }
}
