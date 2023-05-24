<?php

namespace App\Http\Controllers;

use App\Models\ownershop;
use Carbon\Carbon;
use App\Models\chats;
use App\Models\orders;
use App\Models\addresses;
use App\Models\order_details;
use App\Models\product_option;
use App\Models\transections;
use Illuminate\Http\Request;
use App\Models\category;
use App\Models\product;
use App\Models\ownershop_settings;
use App\Models\bankaccount;
use App\Models\shop;
use App\Models\carts;
use App\Models\total_orders;
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

    public function getCategory($id)
    {

        $cat_id = DB::table('shop_list_products')->join('products', 'products.id', '=', 'shop_list_products.product_id')->where('shop_id', '=', $id)->pluck('products.category')->toArray();
        $objs = category::select('cat_name', 'image', 'id')->whereIn('id', $cat_id)->where('status', 1)->get();

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
            ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->selectRaw('products.*,SUM(order_details.num) AS total_sales')
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

        $objs = DB::table('products')->select('*')
            ->orderBy('id', 'DESC')
            ->get();

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
                ->selectRaw('shops.id,shops.name_shop')
                ->orderByRaw('MAX(carts.created_at) DESC')
                ->groupBy('shops.id', 'name_shop')
                ->whereIn('carts.id', $carts_id)
                ->get()
                ->map(function ($item) use ($carts_id) {
                    $item->product = DB::table('carts')
                        ->join('shops', 'carts.shop_id', '=', 'shops.id')
                        ->join('shop_list_products', 'shop_list_products.shop_id', '=', 'carts.shop_id')
                        ->join('products', 'products.id', '=', 'carts.product_id')
                        ->leftjoin('product_options', 'product_options.id', '=', 'carts.product_options_id')
                        ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'carts.product_suboptions_id')
                        ->select([
                            DB::raw('DISTINCT carts.id'),
                            'carts.product_id',
                            'shops.id AS shops_id',
                            'products.name_product' => 'name_product',
                            'products.detail_product' => 'detail_product',
                            'products.price AS price_type_1',
                            'products.type AS type_product',
                            'products.price_sales' => 'price_sales',
                            'products.img_product' => 'img_product',
                            'products.option1' => 'option1',
                            'products.option2' => 'option2',
                            'carts.num' => 'num',
                            'carts.product_suboptions_id as option2Id',
                            'carts.product_options_id as option1Id',
                            'product_options.op_name' => 'op_name',
                            'product_options.price AS price_type_2',
                            'product_suboptions.sub_op_name' => 'sub_op_name',
                            'product_suboptions.price AS price_type_3',
                        ])
                        ->whereIn('carts.id', $carts_id)
                        ->where('shops.id', '=', $item->id)
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
                ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
                ->groupBy('products.id')
                ->selectRaw('products.*,products.id AS product_id,SUM(order_details.num) AS total_sales')
                ->where('shop_list_products.shop_id', '=', $shop_id)
                ->where('products.id', '=', $product_id)
                ->where('products.active', '=', 1)
                ->get();
        } else {
            $objs = DB::table('products')
                ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
                ->groupBy('products.id')
                ->selectRaw('products.*,products.id AS product_id, SUM(order_details.num) AS total_sales')
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
        $total_order = DB::table('total_orders')->where('user_id', '=', $request->owner_shop_id)->first();
        if ($request->product_id !== null) {
            $order = new orders();
            $order->user_id = $request->user_id;
            $order->address_id = $request->address_id;
            $order->owner_shop_id = $request->owner_shop_id;
            $order->order_detail_id = 0;
            $order->num = $request->num;
            $order->price = $request->total;
            $order->discount = $request->discount;
            $order->status = $request->status;
            $order->invoice_id = $request->invoice_id;
            $order->type_payment = $request->type_payment;
            $order->save();

            if ($total_order) {
                $total = total_orders::where('user_id', $request->owner_shop_id)->first();
                $total->total_num = intval($total_order->total_num) + intval($request->num);
                $total->total_price = intval($total_order->total_price) + intval($request->total);
                $total->save();
            } else {
                $total = new total_orders();
                $total->user_id = (int)$request->owner_shop_id;
                $total->total_num = (int)$request->num;
                $total->total_price = (int)$request->total;
                $total->save();
            }

            $order_detail = new order_details();
            $order_detail->product_id = $request->product_id;
            $order_detail->user_id = $request->user_id;
            $order_detail->shop_id = $request->shop_id;
            $order_detail->order_id = $order->id;
            $order_detail->option1 = $request->option1;
            $order_detail->option2 = $request->option2;
            $order_detail->num = $request->num;
            $order_detail->type_payment = $request->type_payment;
            if ($request->price_sales >= 1) {
                $price = $request->price - (($request->price * $request->price_sales) / 100);
                $order_detail->price = $price;
            } else {
                $order_detail->price = $request->price;
            }
            $order_detail->save();

            return response()->json([
                'order' => $order
            ], 201);
        } else {
            $products = json_decode($request->products, true);
            $order = new orders();
            $order->user_id = $request->user_id;
            $order->address_id = $request->address_id;
            $order->owner_shop_id = $request->owner_shop_id;
            $order->order_detail_id = 0;
            $order->num = $request->num;
            $order->price = $request->total;
            $order->discount = $request->discount;
            $order->status = $request->status;
            $order->invoice_id = $request->invoice_id;
            $order->type_payment = $request->type_payment;
            $order->save();

            foreach ($products as $index => $item) {
                foreach ($item['product'] as $subIndex => $subItem) {
                    $order_detail = new order_details();
                    $order_detail->product_id = $subItem['product_id'];
                    $order_detail->user_id = $request->user_id;
                    $order_detail->order_id = $order->id;
                    $order_detail->shop_id = $subItem['shops_id'];
                    $order_detail->option1 = $subItem['option1Id'];
                    $order_detail->option2 = $subItem['option2Id'];
                    if ($subItem['type_product'] == 1) {
                        if ($subItem['price_sales'] >= 1) {
                            $price = $subItem['price_type_1'] - (($subItem['price_type_1'] * $subItem['price_sales']) / 100);
                            $order_detail->price = $price;
                        } else {
                            $order_detail->price = $subItem['price_type_1'];
                        }
                    } else if ($subItem['type_product'] == 2) {
                        if ($subItem['price_sales'] >= 1) {
                            $price = $subItem['price_type_2'] - (($subItem['price_type_2'] * $subItem['price_sales']) / 100);
                            $order_detail->price = $price;
                        } else {
                            $order_detail->price = $subItem['price_type_2'];
                        }
                    } else if ($subItem['type_product'] == 3) {
                        if ($subItem['price_sales'] >= 1) {
                            $price = $subItem['price_type_3'] - (($subItem['price_type_3'] * $subItem['price_sales']) / 100);
                            $order_detail->price = $price;
                        } else {
                            $order_detail->price = $subItem['price_type_3'];
                        }
                    }
                    $order_detail->num = $subItem['num'];
                    $order_detail->type_payment = $request->type_payment;
                    $order_detail->save();
                }
            }

            if ($total_order) {
                $total = total_orders::where('user_id', $request->owner_shop_id)->first();
                $total->total_num = intval($total_order->total_num) + intval($request->num);
                $total->total_price = intval($total_order->total_price) + intval($request->total);
                $total->save();
            } else {
                $total = new total_orders();
                $total->user_id = (int)$request->owner_shop_id;
                $total->total_num = (int)$request->num;
                $total->total_price = (int)$request->total;
                $total->save();
            }
            return response()->json([
                'order' => $order
            ], 201);
        }
    }

    // ดึงข้อมูล order
    public function getOrder(Request $request)
    {
        $order = DB::table('orders')->where('user_id', '=', $request->user_id)->where('id', '=', $request->order_id)->first();


        return response()->json([
            'order' => $order
        ], 201);
    }

    // ดึงข้อมูล order ทั้งหมด
    public function getAllOrder(Request $request)
    {
        $user_id = $request->user_id;
        $owner_shop_id = $request->owner_shop_id;

        $orders = DB::table('orders')
            ->where('orders.user_id', '=', $user_id)
            ->where('orders.owner_shop_id', '=', $owner_shop_id)
            ->orderBy('orders.updated_at', 'desc')
            ->select([
                'orders.status',
                'orders.price',
                'orders.num',
                'orders.discount',
                'orders.id'
            ])
            ->get()
            ->map(function ($item) {
                $item->shops = DB::table('order_details')
                    ->leftJoin('shops', 'shops.id', '=', 'order_details.shop_id')
                    ->groupBy('order_details.shop_id')
                    ->select([
                        'order_details.shop_id',
                        'shops.name_shop',
                        /* 'products.name_product',
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
                        'product_suboptions.price as sub_op_price', */
                    ])
                    ->where('order_details.order_id', '=', $item->id)
                    /* ->orderBy('order_details.updated_at', 'desc') */
                    ->get()
                    ->map(function ($subItem) use ($item) {
                        $subItem->products = DB::table('order_details')
                            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
                            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
                            ->select([
                                'order_details.id',
                                'products.name_product',
                                'products.detail_product',
                                'products.img_product',
                                'products.type',
                                'products.price',
                                'order_details.num',
                                'products.price_sales',
                                'products.option1',
                                'products.option2',
                                'product_options.op_name',
                                'product_options.price as op_price',
                                'product_suboptions.sub_op_name',
                                'product_suboptions.price as sub_op_price',
                            ])
                            ->where('order_details.order_id', '=', $item->id)
                            ->where('order_details.shop_id', '=', $subItem->shop_id)
                            ->get();

                        return $subItem;
                    });

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

    // แก้ไขรูปผู้ใช้
    public function editAvatar(Request $request)
    {

        $file = $request->file('avatar');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $image = Image::make($file->getRealPath());
        $image->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->stream();
        Storage::disk('do_spaces')->put('shopee/avatar/' . $file->hashName(), $image, 'public');
        $filePaths = $file->hashName();
        DB::table('users')->where('id', '=', $request->user_id)->update([
            'avatar' => $filePaths,
        ]);
        $user = DB::table('users')->where('id', '=', $request->user_id)->first();

        return response()->json([
            'user' => $user,
        ], 201);
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

            $products = product::select('id', 'img_product', 'name_product', 'cost', 'price', 'maker', 'created_at', 'stock', 'active')
                ->orderBy('id', 'desc')
                ->get();
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
            // ลบข้อมูลใน product ตาม ID
            product::where('id', '=', $request->id)->delete();
            // ลบข้อมูลที่ร้านค้าใช้ Product_ID นี้
            DB::table('shop_list_products')->where('product_id', $request->id)->delete();
            // ลบรูป Product ID นี้ออก
            DB::table('product_images')->where('product_id', $request->id)->delete();

            // ดึง ID ของ product option ออกมา เพื่อจะนำ เอา ID ไปลบข้อมูลกับตาราง Sub Option
            $product_option_id = DB::table('product_options')->select('id')->where('product_id', $request->id)->get();
            foreach ($product_option_id as $key => $pro_op_id) {
                DB::table('product_suboptions')->where('op_id', $pro_op_id->id)->delete();
            }

            // ลบ option ของ Product ID  นี้ออก
            DB::table('product_options')->where('product_id', $request->id)->delete();

            // $products = product::all();
            return response()->json([
                'success' => 'Delete Product Successfully!',
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

        $files = $request->file('file');
        $filePaths = null;
        $product_id = 0;
        foreach ($files as $index => $file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('do_spaces')->put('shopee/products/' . $file->hashName(), $image, 'public');
            $filePaths = $file->hashName();
            $product->img_product = $filePaths;
            $product->save();
            $product_id = product::select('id')->orderBy('created_at', 'desc')->first();

            if ($request->file('image')) {
                $images = $request->file('image');
                foreach ($images as $index => $img) {
                    $filename = time() . '.' . $img->getClientOriginalExtension();
                    $image = Image::make($img->getRealPath());
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->stream();
                    Storage::disk('do_spaces')->put('shopee/products/' . $img->hashName(), $image, 'public');
                    $filePaths = $img->hashName();
                    DB::table('product_images')->insert([
                        'image' => $filePaths,
                        'product_id' => $product_id['id'],
                        'status' => 0,
                    ]);
                }
            }
        }

        $subProductImg = DB::table('product_images')->select('id', 'image')->where('product_id', $product_id['id'])->get();
        return response()->json([
            'success' => 'Add product successfully!',
            'subProductImg' => $subProductImg,
            'productID' => $product_id['id'],
        ], 201);
    }

    public function addOptionProduct(Request $request)
    {

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

        $files = $request->file('file');
        $filePaths = null;
        $product_id = 0;
        foreach ($files as $index => $file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('do_spaces')->put('shopee/products/' . $file->hashName(), $image, 'public');
            $filePaths = $file->hashName();
            $product->img_product = $filePaths;
            $product->save();
            $product_id = product::select('id')->orderBy('created_at', 'desc')->first();

            if ($request->file('image')) {
                $images = $request->file('image');
                foreach ($images as $index => $img) {
                    $filename = time() . '.' . $img->getClientOriginalExtension();
                    $image = Image::make($img->getRealPath());
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->stream();
                    Storage::disk('do_spaces')->put('shopee/products/' . $img->hashName(), $image, 'public');
                    $filePaths = $img->hashName();
                    DB::table('product_images')->insert([
                        'image' => $filePaths,
                        'product_id' => $product_id['id'],
                        'status' => 0,
                    ]);
                }
            }
        }
        $proID = $product_id['id'];
        $option1 = null;
        $option2 = null;
        $filePaths2 = '';
        $id_image_option = "";
        $dataOption = json_decode($request->dataOption, true);
        $type = 1;
        if ($request->option1 != 'ตัวเลือกที่ 1' && $request->option2 == 'ตัวเลือกที่ 2') {
            $option1 = $request->option1;
            $type = 2;
        }
        if ($request->option1 != 'ตัวเลือกที่ 1' && $request->option2 != 'ตัวเลือกที่ 2') {
            $option1 = $request->option1;
            $option2 = $request->option2;
            $type = 3;
        }
        DB::table('products')->where('id', $proID)->update([
            'option1' => $option1,
            'option2' => $option2,
            'type' => $type,
        ]);
        foreach ($dataOption as $item) {
            $status_option = 1;

            if ($item->file('indexImageOption')) {
                $images = $item->file('indexImageOption');
                foreach ($images as $index => $img) {
                    $filename = time() . '.' . $img->getClientOriginalExtension();
                    $image = Image::make($img->getRealPath());
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->stream();
                    Storage::disk('do_spaces')->put('shopee/products/' . $img->hashName(), $image, 'public');
                    $filePaths2 = $img->hashName();
                    $id_image_option = DB::table('product_images')->insertGetId([
                        'image' => $filePaths2,
                        'product_id' => $product_id['id'],
                        'status' => 0,
                    ]);
                    if ($item['statusOption'] != true || $item['statusOption'] != 'true') {
                        $status_option = 0;
                    }
                    // $pro_option = new product_option;
                    // $pro_option->product_id = $proID;
                    // $pro_option->img_id = $id_image_option;
                    // $pro_option->op_name = $item['nameOption'];
                    // $pro_option->img_name = $filePaths2;
                    // $pro_option->price = $item['priceOption'];
                    // $pro_option->stock = $item['stockOption'];
                    // $pro_option->sku = $item['skuOption'];
                    // $pro_option->status = $status_option;
                    // $pro_option->save();
                    $id_image_suboption = DB::table('product_options')->insertGetId([
                        'product_id' => $proID,
                        'img_id' => $id_image_option,
                        'op_name' => $item['nameOption'],
                        'img_name' => $filePaths2,
                        'price' => $item['priceOption'],
                        'stock' => $item['stockOption'],
                        'sku' =>  $item['skuOption'],
                        'status' => $status_option,
                    ]);

                    foreach ($item['subOption'] as $subItem) {
                        $status_suboption = 1;
                        if ($subItem['statusSubOption'] != true || $subItem['statusSubOption'] != 'true') {
                            $status_suboption = 0;
                        }
                        DB::table('product_suboptions')->insert([
                            'op_id' => $id_image_suboption,
                            'sub_op_name' => $subItem['nameSubOption'],
                            'price' => $subItem['priceSubOption'],
                            'stock' => $subItem['stockSubOption'],
                            'sku' => $subItem['skuSubOption'],
                            'status' => $status_suboption,
                        ]);
                    }
                }
            }

            // $img_product = DB::table('product_images')->select('image')->where('id', $item['indexImageOption'])->first();



        }

        return response()->json([
            'success' => 'Add option product successfully!',
        ], 201);
    }

    // แก้ไขข้อมูลสินค้า
    public function editProduct(Request $request, $id)
    {
        $checkActive = DB::table('products')->select('active')->where('id', $id)->first();
        if ($checkActive->active == 0) {
            DB::table('products')->where('id', $id)->update([
                "name_product" => $request->name_product,
                "detail_product" => $request->detail_product,
                "category" => $request->categoryId,
                "sku" => $request->sku,
                "cost" => $request->cost,
                "price" => $request->price,
                "price_sales" => $request->price_sales,
                "stock" => $request->stock,
                "weight" => $request->weight,
                "width_product" => $request->width,
                "height_product" => $request->height,
                "length_product" => $request->length,
                "active" => 1,
            ]);
        } else {
            DB::table('products')->where('id', $id)->update([
                "name_product" => $request->name_product,
                "detail_product" => $request->detail_product,
                "category" => $request->categoryId,
                "sku" => $request->sku,
                "cost" => $request->cost,
                "price" => $request->price,
                "price_sales" => $request->price_sales,
                "stock" => $request->stock,
                "weight" => $request->weight,
                "width_product" => $request->width,
                "height_product" => $request->height,
                "length_product" => $request->length,
            ]);

            if ($request->file('file')) {
                $images = $request->file('file');
                foreach ($images as $index => $img) {
                    $filename = time() . '.' . $img->getClientOriginalExtension();
                    $image = Image::make($img->getRealPath());
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->stream();
                    Storage::disk('do_spaces')->put('shopee/products/' . $img->hashName(), $image, 'public');
                    $filePaths = $img->hashName();
                    DB::table('products')->where('id', $id)->update([
                        'img_product' => $filePaths,
                    ]);
                }
            }

            if ($request->file('image')) {
                $images = $request->file('image');
                foreach ($images as $index => $img) {
                    $filename = time() . '.' . $img->getClientOriginalExtension();
                    $image = Image::make($img->getRealPath());
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->stream();
                    Storage::disk('do_spaces')->put('shopee/products/' . $img->hashName(), $image, 'public');
                    $filePaths = $img->hashName();
                    DB::table('product_images')->insert([
                        'image' => $filePaths,
                        'product_id' => $id,
                        'status' => 0,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => 'updated product successfully',
        ], 201);
    }

    public function deleteImgProduct(Request $request, $id)
    {
        if ($request->img_name) {
            Storage::disk('do_spaces')->delete('shopee/products/' . $request->img_name);
            DB::table('products')->where('id', $id)->update([
                'img_product' => null,
            ]);
            return response()->json([
                'success' => 'delete image product successfully',
            ], 201);
        }
    }

    public function deleteImgSubProduct(Request $request, $id)
    {
        if ($request->img_name && $id) {
            Storage::disk('do_spaces')->delete('shopee/products/' . $request->img_name);
            DB::table('product_images')->where('id', $id)->delete();
            return response()->json([
                'success' => 'delete image sub product successfully',
            ], 201);
        }
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
            ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->selectRaw('products.*,SUM(order_details.num) AS total_sales')
            ->where('shop_list_products.shop_id', '=', $id)
            ->whereRaw("CONCAT(products.name_product, products.detail_product) LIKE ?", ["%$search%"])
            ->where('products.active', '=', 1)
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
        ])->orWhere('id', '=', $request->input('cart_id'))
            ->first();
        if ($cartItem !== null) {
            if ($request->input('num') !== null) {
                $sum = $cartItem->num + $request->input('num');
            } else {
                $sum = $request->input('number');
            }

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
            ->where('shops.user_code', '=', $request->user_code)
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
                        'carts.shop_id',
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
        try {
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

            return response()->json([
                'status' => 'success',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
            ], 201);
        }
    }

    // แก้ไขที่อยู่
    public function editAddress(Request $request)
    {
        try {
            if ($request->default == 1) {
                DB::table('addresses')->where('user_id', '=', $request->user_id)->update([
                    'default' => 0
                ]);
            }
            DB::table('addresses')
                ->where('id', '=', $request->address_id)
                ->update([
                    'name' => $request->name,
                    'tel' => $request->tel,
                    'address' => $request->address,
                    'sub_district' => $request->subDistrict,
                    'district' => $request->district,
                    'province' => $request->province,
                    'postcode' => $request->postcode,
                    'default' => $request->default,
                ]);

            return response()->json([
                'status' => 'success',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
            ], 201);
        }
    }

    // ลบที่อยู่
    public function deleteAddress(Request $request)
    {
        DB::table('addresses')->where('id', '=', $request->address_id)->delete();
        $count = DB::table('addresses')->where('user_id', '=', $request->user_id)->where('default', '=', 1)->count();

        if ($count == 0) {
            DB::table('addresses')->where('user_id', '=', $request->user_id)->orderBy('updated_at', 'desc')->take(1)->update(['default' => 1]);
        }
        $address = DB::table('addresses')->where('user_id', '=', $request->user_id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'address' => $address,
        ], 201);
    }

    // ตั้งค่าที่อยู่เริ่มต้น
    public function setDefaultAddress(Request $request)
    {
        DB::table('addresses')->where('user_id', '=', $request->user_id)->update([
            'default' => 0
        ]);
        DB::table('addresses')->where('id', '=', $request->id)->update(['default' => 1]);
        $address = DB::table('addresses')->where('user_id', '=', $request->user_id)->orderBy('updated_at', 'desc')->get();
        return response()->json([
            'address' => $address,
        ], 201);
    }

    // ดึงข้อมูลที่อยู่
    public function getAddress(Request $request)
    {
        // ดึงสำหรับแก้ไข
        if ($request->address_id !== null) {
            $address = DB::table('addresses')->where('id', '=', $request->address_id)->first();

            return response()->json([
                'address' => $address,
            ], 201);
        }
        //ดึงสำหรับแสดงหน้า order
        $address = DB::table('addresses')->where('user_id', '=', $request->user_id)->where('default', '=', 1)->first();

        return response()->json([
            'address' => $address,
        ], 201);
    }

    // ดึงข้อมูลที่อยู่ทั้งหมด
    public function getAllAddress(Request $request)
    {
        $address = DB::table('addresses')->where('user_id', '=', $request->user_id)->orderBy('updated_at', 'desc')->get();

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

    // ดึงข้อมูลแชท
    public function getMessage(Request $request)
    {
        if ($request->type == 'customer') {
            DB::table('chats')->where('recived_id', '=', $request->user_id)->where('shop_id', '=', $request->shop_id)->update([
                'status' => 1,
            ]);
        } else if ($request->type == 'shop') {
            DB::table('chats')->where('sender_id', '=', $request->user_id)->where('shop_id', '=', $request->shop_id)->update([
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
            ], 201);
        }

        $message = DB::table('chats')
            ->join('users', 'users.id', '=', 'chats.user_id')
            ->join('shops', 'shops.id', '=', 'chats.shop_id')
            ->select('chats.*', 'users.avatar', 'shops.img_shop')
            ->where(function ($query) use ($request) {
                $query->where('chats.sender_id', $request->user_id)
                    ->orWhere('chats.recived_id', $request->user_id);
            })
            ->where('chats.shop_id', $request->shop_id)
            ->orderBy('chats.created_at', 'asc')
            ->get();

        return response()->json([
            'message' => $message,
        ], 201);
    }

    // ส่งข้อความ
    public function sendMessage(Request $request)
    {
        $objs = new chats();
        $objs->user_id = $request->user_id;
        $objs->shop_id = $request->shop_id;
        $objs->sender_id = $request->sender_id;
        $objs->recived_id = $request->recived_id;
        $objs->message = $request->message;
        $objs->img_message = $request->img_message;
        $objs->save();

        $message = DB::table('chats')
            ->join('users', 'users.id', '=', 'chats.user_id')
            ->join('shops', 'shops.id', '=', 'chats.shop_id')
            ->where('chats.id', '=', $objs->id)
            ->select([
                'chats.*',
                'users.avatar',
                'shops.img_shop',
            ])
            ->get();

        return response()->json([
            'message' => $message,
        ], 201);
    }

    // ดึงข้อมูลธนาคาร
    public function getBankaccount(Request $request)
    {
        if ($request->id !== null) {
            $banks = DB::table('bankaccounts')->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')->where('bankaccounts.id', '=', $request->id)->where('bankaccounts.is_active', '=', 1)->where('bankaccounts.type_account', '=', 'eBank')->first();

            return response()->json([
                'banks' => $banks,
            ], 201);
        }
        if ($request->type == "setting") {
            $banks = DB::table('bankaccounts')->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')->where('bankaccounts.user_id', '=', $request->user_id)->where('bankaccounts.type_account', '=', 'eBank')->select([
                'bankaccounts.*',
                'banks.name_bank',
                'banks.icon_bank_circle',
            ])->get();
            return response()->json([
                'banks' => $banks,
            ], 201);
        }
        $banks = DB::table('bankaccounts')->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')->where('bankaccounts.user_id', '=', $request->user_id)->where('bankaccounts.is_active', '=', 1)->where('bankaccounts.type_account', '=', 'eBank')->get();

        return response()->json([
            'banks' => $banks,
        ], 201);
    }

    public function getActiveCOD(Request $request)
    {
        $objs = DB::table('bankaccounts')->where('user_id', '=', $request->uid)->where('type_account', '=', 'COD')->first();

        return response()->json([
            'account' => $objs,
        ], 201);
    }

    //เพิ่มบัญชีธนาคาร
    public function addBankAccount(Request $request)
    {
        $file = $request->file;
        $filePaths = null;
        if ($file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('do_spaces')->put('shopee/QR_code/' . $file->hashName(), $image, 'public');
            $filePaths = $file->hashName();
        }


        $objs = new bankaccount();
        $objs->user_id = $request->uid;
        $objs->bank_id = $request->bank_id;
        $objs->bankaccount_name = $request->bankaccount_name;
        $objs->bankaccount_number = $request->bankaccount_number;
        $objs->QR_code = $filePaths;
        $objs->is_active = 1;
        $objs->type_deposit = $request->type_deposit;
        $objs->branch = $request->branch;
        $objs->type_account = $request->type_account;
        $objs->save();
        return response()->json([
            'status' => 'success',
        ], 201);
    }

    public function setActiveBankacc(Request $request)
    {

        DB::table('bankaccounts')->where('id', '=', $request->bankacc_id)->update([
            'is_active' => $request->checked,
        ]);
        if ($request->type == "COD") {
            $banks = DB::table('bankaccounts')->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')->where('bankaccounts.user_id', '=', $request->user_id)->where('bankaccounts.type_account', '=', 'eBank')->select([
                'bankaccounts.*',
                'banks.name_bank',
                'banks.icon_bank_circle',
            ])->first();
        } else {
            $banks = DB::table('bankaccounts')->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')->where('bankaccounts.user_id', '=', $request->user_id)->where('bankaccounts.type_account', '=', 'eBank')->select([
                'bankaccounts.*',
                'banks.name_bank',
                'banks.icon_bank_circle',
            ])->get();
        }

        return response()->json([
            'banks' => $banks,
        ], 201);
    }

    public function deleteBankaccount(Request $request)
    {
        DB::table('bankaccounts')->where('id', '=', $request->accId)->delete();

        $banks = DB::table('bankaccounts')->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')->where('bankaccounts.user_id', '=', $request->user_id)->where('bankaccounts.type_account', '=', 'eBank')->select([
            'bankaccounts.*',
            'banks.name_bank',
            'banks.icon_bank_circle',
        ])->get();

        return response()->json([
            'banks' => $banks,
        ], 201);
    }

    public function getAllBanks()
    {
        $objs = DB::table('banks')->get();

        return response()->json([
            'banks' => $objs,
        ], 201);
    }

    public function updateBankaccount(Request $request)
    {
        $file = $request->file;
        if ($file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('do_spaces')->put('shopee/QR_code/' . $file->hashName(), $image, 'public');
            $filePaths = $file->hashName();
            DB::table('bankaccounts')->where('id', '=', $request->bId)->update([
                'bank_id' => $request->bank_id,
                'bankaccount_name' => $request->bankaccount_name,
                'bankaccount_number' => $request->bankaccount_number,
                'branch' => $request->branch,
                'type_deposit' => $request->type_deposit,
                'QR_code' => $filePaths,
            ]);
        } else {
            DB::table('bankaccounts')->where('id', '=', $request->bId)->update([
                'bank_id' => $request->bank_id,
                'bankaccount_name' => $request->bankaccount_name,
                'bankaccount_number' => $request->bankaccount_number,
                'branch' => $request->branch,
                'type_deposit' => $request->type_deposit,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ], 201);
    }

    public function getOwnerSetting(Request $request)
    {
        $objs = Db::table('ownershop_settings')->where('user_code', '=', $request->user_code)->first();

        return response()->json([
            'setting' => $objs,
        ], 201);
    }

    public function countNoti($id)
    {
        $objs = DB::table('orders')->where('owner_shop_id', '=', $id)->count();

        return response()->json([
            'count' => $objs,
        ], 201);
    }

    //เปิดปิด แจ้งเตือนหลังบ้าน
    public function settingNoti(Request $request)
    {

        $objs = ownershop_settings::where('user_code', $request->user_code)->first();
        if ($objs) {
            $objs->setting = $request->setting;
            $objs->save();
        }

        return response()->json([
            'setting' => $objs,
        ], 201);
    }

    // ดึงข้อมูลแชทสำหรับแม่ค้า
    public function getUserChats(Request $request)
    {
        $objs = DB::table('chats as c1')
            ->join('users', 'users.id', '=', 'c1.user_id')
            ->where('c1.shop_id', '=', $request->shop_id)
            ->where(function ($query) {
                $query->whereRaw('c1.created_at = (SELECT MAX(created_at) FROM chats as c2 WHERE c2.user_id = c1.user_id and c2.shop_id = c1.shop_id)');
            })

            ->orderBy('c1.created_at', 'desc')
            ->get();

        return response()->json([
            'users' => $objs
        ], 201);
    }

    // ดึงข้อมูลรายงาย
    public function getReports(Request $request)
    {
        $search = $request->search;
        $startDate = Carbon::createFromTimestamp($request->startDate);
        $endDate = Carbon::createFromTimestamp($request->endDate);
        $reports = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->leftjoin('users', 'users.id', '=', 'order_details.user_id')
            ->leftjoin('addresses', 'addresses.id', '=', 'orders.address_id')
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->orderBy('order_details.created_at', 'desc')
            ->select([
                'order_details.*',
                'orders.invoice_id',
                'shops.name_shop',
                'shops.url_shop',
                'users.id AS uid',
                'users.name AS customer_name',
                'addresses.province',
                'addresses.tel',
                'addresses.name',
                'products.name_product',
                'products.type',
                'order_details.price',
                'products.sku AS sku_type_1',
                'product_options.sku AS sku_type_2',
                'product_suboptions.sku AS sku_type_3',
            ])
            ->where(function ($query) use ($search) {
                $query->where('shops.name_shop', 'like', '%' . $search . '%')
                    ->orWhere('products.name_product', 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere('addresses.province', 'like', '%' . $search . '%')
                    ->orWhere('products.sku', 'like', '%' . $search . '%')
                    ->orWhere('orders.invoice_id', 'like', '%' . $search . '%')
                    ->orWhere('addresses.tel', 'like', '%' . $search . '%');
            })
            ->where('order_details.created_at', '>=', $startDate)->where('order_details.created_at', '<=', $endDate)
            ->paginate($request->itemsPerPage);

        /* $total_num = DB::table('') */

        return response()->json([
            'reports' => $reports,
        ], 201);
    }

    public function total_orders(Request $request)
    {
        $sub_admin = DB::table('sub_admins')->where('sub_admin', '=', $request->uid)->first();
        if ($sub_admin) {
            $permission = json_decode($sub_admin->permission);

            if ($permission->set_permission_report) {
                $total = DB::table('sub_admins')
                    ->leftJoin('total_orders', 'total_orders.user_id', '=', 'sub_admins.owner_admin')
                    ->where('sub_admins.sub_admin', '=', $request->uid)
                    ->select([
                        'total_orders.*',
                    ])
                    ->first();

                return response()->json([
                    'total' => $total,
                ], 201);
            } else {
                return response()->json([
                    'status' => 'not permission',
                ]);
            }
        } else {
            $total = DB::table('total_orders')
                ->where('user_id', '=', $request->uid)
                ->first();
            if ($total) {
                return response()->json([
                    'total' => $total,
                ], 201);
            } else {
                return response()->json([
                    'status' => 'not permission',
                ]);
            }
        }
    }

    public function dashboard(Request $request)
    {
        $startDatePie = Carbon::createFromTimestamp($request->startDatePie);
        $endDatePie = Carbon::createFromTimestamp($request->endDatePie);
        $startDateBar = Carbon::createFromTimestamp($request->startDateBar);
        $endDateBar = Carbon::createFromTimestamp($request->endDateBar);
        $data_table = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->groupBy('products.name_product', 'shops.name_shop')
            ->selectRaw('products.name_product,shops.name_shop,SUM(order_details.price*order_details.num) AS total_price')
            ->where('shops.user_code', '=', $request->uid)
            ->orderBy('total_price', 'desc')
            ->get();

        $data_chart_pie_products = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->groupBy('products.name_product', 'shops.name_shop')
            ->selectRaw('products.name_product, SUM(order_details.num) AS total_num')
            ->where('shops.user_code', '=', $request->uid)
            ->where('order_details.created_at', '>=', $startDatePie)->where('order_details.created_at', '<=', $endDatePie)
            ->orderBy('total_num', 'desc')
            ->limit(5)
            ->get();

        $data_chart_pie_shops = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->groupBy('shops.name_shop')
            ->selectRaw('shops.name_shop, SUM(order_details.num) AS total_num')
            ->where('shops.user_code', '=', $request->uid)
            ->where('order_details.created_at', '>=', $startDatePie)->where('order_details.created_at', '<=', $endDatePie)
            ->orderBy('total_num', 'desc')
            ->limit(5)
            ->get();

        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        $chartData = [];
        $data_chart_line = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->groupBy(DB::raw('DATE_FORMAT(order_details.created_at, "%M")'))
            ->selectRaw('DATE_FORMAT(order_details.created_at, "%M") AS month, SUM(order_details.price*order_details.num) AS total_price')
            ->where('shops.user_code', '=', $request->uid)
            ->get();
        $monthData = $data_chart_line->keyBy('month')->toArray(); // Convert the query result to an array, keyed by month

        // Loop through the months array to create the chart data
        foreach ($months as $month) {
            $totalPrice = isset($monthData[$month]) ? $monthData[$month]->total_price : 0; // Check if there's data for this month
            $chartData[] = ['month' => $month,    'total_price' => $totalPrice,];
        }

        $data_chart_bar = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->groupBy(DB::raw('DATE_FORMAT(order_details.created_at, "%Y-%M-%D")'), 'shops.name_shop')
            ->selectRaw('DATE_FORMAT(order_details.created_at, "%Y-%M-%D") AS month,shops.name_shop, SUM(order_details.num) AS total_num')
            ->where('shops.user_code', '=', $request->uid)
            ->where('order_details.created_at', '>=', $startDateBar)->where('order_details.created_at', '<=', $endDateBar)
            ->orderBy(DB::raw('DATE_FORMAT(order_details.created_at, "%Y-%M-%D")'), 'asc')
            ->get();

        $data_shop_bar = DB::table('order_details')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->groupBy('shops.name_shop')
            ->selectRaw('shops.name_shop, SUM(order_details.num) AS total_num')
            ->where('shops.user_code', '=', $request->uid)
            ->limit(5)
            ->get();

        // Group the results by month and then by shop name
        $data_chart_bar_grouped = $data_chart_bar->groupBy('month')->map(function ($monthData) {
            return [
                'month' => $monthData->first()->month,
                'data' => $monthData->groupBy('name_shop')->map(function ($shopData) {
                    return [
                        'name_shop' => $shopData->first()->name_shop,
                        'total_num' => $shopData->sum('total_num'),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $all_stock = DB::table('products')
            ->leftjoin('shop_list_products', 'shop_list_products.product_id', '=', 'products.id')
            ->leftJoin('shops', 'shops.id', '=', 'shop_list_products.shop_id')
            ->selectRaw('SUM(products.stock) AS allStock')
            ->where('shops.user_code', '=', $request->uid)
            ->get();

        $total_sales = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->selectRaw('SUM(order_details.num) AS total_sales')
            ->where('shops.user_code', '=', $request->uid)
            ->get();

        $total_delivery = DB::table('orders')
            ->selectRaw('SUM(CASE WHEN orders.status = "จัดส่งสำเร็จ" THEN orders.num ELSE 0 END) AS sum_sent, SUM(CASE WHEN orders.status != "จัดส่งสำเร็จ" THEN orders.num ELSE 0 END) AS sum_not_sent')
            ->where('orders.owner_shop_id', '=', $request->uid)
            ->get();

        $total_payment = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('shops', 'shops.id', '=', 'order_details.shop_id')
            ->selectRaw('SUM(CASE WHEN order_details.type_payment = "โอนเงิน" THEN order_details.num ELSE 0 END) AS sum_payment, SUM(CASE WHEN order_details.type_payment = "เก็บเงินปลายทาง" THEN order_details.num ELSE 0 END) AS sum_cash_on_delivery')
            ->where('shops.user_code', '=', $request->uid)
            ->get();
        return response()->json([
            'data_table' => $data_table,
            'data_chart_pie_products' => $data_chart_pie_products,
            'data_chart_pie_shops' => $data_chart_pie_shops,
            'data_chart_line' => $chartData,
            'data_chart_bar' => $data_chart_bar_grouped,
            'all_stock' => $all_stock,
            'total_sales' => $total_sales,
            'total_delivery' => $total_delivery,
            'total_payment' => $total_payment,
            'data_shop_bar' => $data_shop_bar,
        ], 201);
    }

    // profile user
    public function getOwnershops(Request $request)
    {
        $obj = DB::table('ownershops')->where('user_code', '=', $request->user_code)->first();

        return response()->json([
            'owner_shop' => $obj,
        ], 201);
    }

    public function updateOwnerShop(Request $request)
    {

        $objs = ownershop::where('user_code', $request->user_code)->first();

        if ($objs) {
            $objs->fname = $request->fname;
            $objs->lname = $request->lname;
            $objs->gender = $request->gender;
            $objs->address = $request->address;
            $objs->sub_district = $request->sub_district;
            $objs->district = $request->district;
            $objs->county = $request->county;
            $objs->zip_code = $request->zip_code;
            $objs->phone = $request->phone;
            $objs->email = $request->email;
            $objs->facebook = $request->facebook;
            $objs->line = $request->line;
            $objs->instagram = $request->instagram;
            $objs->twitter = $request->twitter;
            $objs->tiktok = $request->tiktok;
            $objs->youtube = $request->youtube;
            $objs->user_id = $request->user_id;
            $objs->status = 1;
            $objs->update();

            return response()->json([
                'status' => 'succes',
            ], 201);
        } else {
            $obj = new ownershop();
            $obj->fname = $request->fname;
            $obj->lname = $request->lname;
            $obj->gender = $request->gender;
            $obj->address = $request->address;
            $obj->sub_district = $request->sub_district;
            $obj->district = $request->district;
            $obj->county = $request->county;
            $obj->zip_code = $request->zip_code;
            $obj->phone = $request->phone;
            $obj->email = $request->email;
            $obj->facebook = $request->facebook;
            $obj->line = $request->line;
            $obj->instagram = $request->instagram;
            $obj->twitter = $request->twitter;
            $obj->tiktok = $request->tiktok;
            $obj->youtube = $request->youtube;
            $obj->user_code = $request->user_code;
            $obj->user_id = $request->user_id;
            $obj->status = 1;
            $obj->save();

            return response()->json([
                'status' => 'succes',
            ], 201);
        }
    }
    // ดึงจำนวน invoice
    public function count_orders(Request $request)
    {
        $count = DB::table('orders')->where('created_at', '>=', date('Y-m-d H:i:s', $request->startDate / 1000))->where('created_at', '<=', date('Y-m-d H:i:s', $request->endDate / 1000))->count();

        return response()->json([
            'count' => $count,
        ], 201);
    }

    // ดึงข้อมูลรายละเอียดลูกค้า
    public function detail_customer(Request $request)
    {
        $customer = DB::table('users')
            ->leftjoin('addresses', 'addresses.user_id', '=', 'users.id')
            ->where('users.id', '=', $request->uid)
            ->where('addresses.default', '=', 1)
            ->select([
                'users.id AS uid',
                'users.name AS user_name',
                'users.email',
                'users.created_at',
                'users.updated_at',
                'users.avatar',
                'addresses.address',
                'addresses.district',
                'addresses.sub_district',
                'addresses.province',
                'addresses.tel',
                'addresses.postcode',
            ])
            ->get();

        $orders = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftjoin('product_options', 'product_options.id', '=', 'order_details.option1')
            ->leftjoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
            ->where('order_details.user_id', '=', $request->uid)
            ->orderBy('order_details.created_at', 'desc')
            ->select([
                'order_details.created_at',
                'order_details.num',
                'orders.invoice_id',
                'orders.status',
                'products.name_product',
                'products.sku',
                'products.type',
                'products.img_product',
                'products.price AS price_type_1',
                'product_options.price AS price_type_2',
                'product_options.img_name AS img_pro_option',
                'product_suboptions.price AS price_type_3',
            ])
            ->get();

        return response()->json([
            'customer' => $customer,
            'orders' => $orders,
        ], 201);
    }
    public function search_users_chats(Request $request)
    {
        $objs = DB::table('chats as c1')
            ->join('users', 'users.id', '=', 'c1.user_id')
            ->where('c1.shop_id', '=', $request->shop_id)
            ->where('name', 'LIKE', '%' . $request->name . '%')
            ->where(function ($query) {
                $query->whereRaw('c1.created_at = (SELECT MAX(created_at) FROM chats as c2 WHERE c2.user_id = c1.user_id)');
            })
            ->orderBy('c1.created_at', 'desc')
            ->get();

        return response()->json([
            'users' => $objs
        ], 201);
    }

    public function confirm_payment(Request $request)
    {

        $files = $request->file('file');
        $filePaths = null;
        foreach ($files as $index => $file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('do_spaces')->put('shopee/slip/' . $file->hashName(), $image, 'public');
            $filePaths = $file->hashName();

            $transections = new transections();
            $transections->slip = $filePaths;
            $transections->date = $request->date;
            $transections->time = $request->time;
            $transections->order_id = $request->order_id;
            $transections->status = $request->status;
            $transections->bankaccount_id = $request->bankaccount_id;
            $transections->save();

            return response()->json([
                'status' => 'success',
            ], 201);
        }
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
            $length = 4;

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }

            $ran_num = rand(100000000, 999999999);

            DB::table('shops')->insert([
                'name_shop' => $request['nameShop'],
                'detail_shop' => $request['detailShop'],
                'img_shop' => $filePaths,
                'cover_img_shop' => $filePaths2,
                'code_shop' => $ran_num,
                'url_shop' => $randomString . '' . $ran_num,
                'theme' => $request->themeShop,
                'status' => 1,
                'created_at' =>  date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            // รับค่า ID ของแถวที่เพิ่งถูกเพิ่มล่าสุดเข้าไปในตาราง users
            $lastInsertId = DB::getPdo()->lastInsertId();

            if ($request->input('selectID')) {
                $selectedProducts = $request->input('selectID');
                if (is_array($selectedProducts)) {
                    foreach ($selectedProducts as $productId) {
                        DB::table('shop_list_products')->insert([
                            'product_id' => $productId,
                            'shop_id' => $lastInsertId,
                            'created_at' =>  date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            // if ($request->input('category')) {
            //     $InputCategory = $request->input('category');
            //     if (is_array($InputCategory)) {
            //         foreach ($InputCategory as $category) {
            //             DB::table('categorys_shop')->insert([
            //                 'category_name' => $category,
            //                 'shop_id' => $lastInsertId,
            //                 'created_at' =>  date('Y-m-d H:i:s'),
            //                 'updated_at' => date('Y-m-d H:i:s'),
            //             ]);
            //         }
            //     }
            // }


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

    public function addCategory(Request $request)
    {
        if ($request->input('category')) {
            $InputCategory = $request->input('category');
            if (is_array($InputCategory)) {
                foreach ($InputCategory as $category) {
                    DB::table('categories')->insert([
                        'cat_name' => $category,
                        'user_id' => $request->userID,
                        'status' => 1,
                        'created_at' =>  date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            return response()->json([
                'success' => 'Create Category Shop successfully!',
            ], 201);
        }
    }

    public function EditCategory(Request $request)
    {
        // dd($request->category);
        try {
            if ($request->category) {
                foreach ($request->category as $category) {
                    DB::table('categories')->where("id", $category['id'])->update([
                        'cat_name' => $category['cat_name'],
                    ]);
                }
                return response()->json([
                    'success' => 'Update Category Shop successfully!',
                ], 201);
            }
        } catch (\Throwable $th) {
            dd($th);
        }

        // if ($request->category) {
        //     $InputCategory = $request->category;
        //     if (is_array($InputCategory) || is_object($InputCategory)) {
        //         foreach ($InputCategory as $category) {
        //             DB::table('categories')->where('id', $category->id)->update([
        //                 'cat_name' => $category->cat_name,
        //             ]);
        //         }
        //     }
        //     return response()->json([
        //         'success' => 'Update Category Shop successfully!',
        //     ], 201);
        // }
    }

    public function deleteCategory($id)
    {
        DB::table('products')->where('category', $id)->update([
            'active' => 0,
        ]);
        DB::table('categories')->where('id', $id)->delete();

        return response()->json([
            'success' => 'Update Category Shop successfully!',
        ], 201);
    }

    public function getSearchProduct(Request $request)
    {
        $search = $request->query('search');

        if ($search != 'null') {
            $products = product::when($search, function ($query, $search) {
                return $query->where('name_product', 'like', '%' . $search . '%');
            })->get();
        } else {
            $products = DB::table('products')->select('*')
                ->orderBy('id', 'DESC')
                ->get();
        }

        return response()->json([
            'product' => $products,
        ], 201);
    }

    public function deleteOptionProduct($id)
    {
        if ($id) {
            DB::table('product_options')->where('id', $id)->delete();
            return response()->json([
                'success' => "Delete option product successfully.",
            ], 201);
        }
    }

    public function deleteSubOptionProduct($id)
    {
        if ($id) {
            DB::table('product_suboptions')->where('id', $id)->delete();
            return response()->json([
                'success' => "Delete sub option product successfully.",
            ], 201);
        }
    }

    public function editOptionProduct(Request $request)
    {
        $proID = $request->productID;
        $option = null;
        $sub_option = null;
        // $dataOption = json_decode($request->dataOption, true);
        if ($request->option1 !== 'ตัวเลือกที่ 1') {
            $option = $request->option1;
        }
        if ($request->option2 !== 'ตัวเลือกที่ 2') {
            $sub_option = $request->option2;
        }
        DB::table('products')->where('id', $proID)->update([
            'option1' => $option,
            'option2' => $sub_option,
        ]);

        foreach ($request->dataOption as $item) {
            if (array_key_exists('id', $item) && $item['id']) {
                DB::table('product_options')->where('id', $item['id'])->update([
                    'product_id' => $proID,
                    'img_id' => $item['img_id'],
                    'img_name' => $item['img_name'],
                    'op_name' => $item['op_name'],
                    'price' => $item['price'],
                    'sku' => $item['sku'],
                    'status' => $item['status'],
                    'stock' => $item['stock'],
                ]);

                foreach ($item['allOption2'] as $subItem) {
                    if (array_key_exists('id', $subItem) && $subItem['id']) {
                        DB::table('product_suboptions')->where('id', $subItem['id'])->update([
                            'op_id' => $item['id'],
                            'sub_op_name' => $subItem['sub_op_name'],
                            'price' => $subItem['price'],
                            'stock' => $subItem['stock'],
                            'sku' => $subItem['sku'],
                            'status' => $subItem['status'],
                        ]);
                    } else {
                        DB::table('product_suboptions')->insert([
                            'op_id' => $item['id'],
                            'sub_op_name' => $subItem['sub_op_name'],
                            'price' => $subItem['price'],
                            'stock' => $subItem['stock'],
                            'sku' => $subItem['sku'],
                            'status' => $subItem['status'],
                        ]);
                    }
                }
            } else {
                $img_product = DB::table('product_images')->select('image')->where('id', $item['indexImageOption'])->first();
                $id_proOp = DB::table('product_options')->insertGetId([
                    'product_id' => $proID,
                    'img_id' => $item['indexImageOption'],
                    'img_name' => $img_product->image,
                    'op_name' => $item['op_name'],
                    'price' => $item['price'],
                    'sku' => $item['sku'],
                    'status' => $item['status'],
                    'stock' => $item['stock'],
                ]);

                foreach ($item['allOption2'] as $subItem) {
                    DB::table('product_suboptions')->insert([
                        'op_id' => $id_proOp,
                        'sub_op_name' => $subItem['sub_op_name'],
                        'price' => $subItem['price'],
                        'stock' => $subItem['stock'],
                        'sku' => $subItem['sku'],
                        'status' => $subItem['status'],
                    ]);
                }
            }
        }

        return response()->json([
            'success' => "Updated option product successfully.",
        ], 201);
    }

    public function deleteTitleSubOptionProduct($productId)
    {
        $id_subOption = DB::table('product_options')->select('id')->where('product_id', $productId)->get();
        DB::table('products')->where('id', $productId)->update(['option2' => null]);
        foreach ($id_subOption as $Idsuboption) {
            DB::table('product_suboptions')->where('op_id', $Idsuboption->id)->delete();
        }
        return response()->json([
            'success' => "Delete sub option product successfully.",
        ], 201);
    }

    public function deleteTitleOptionProduct($productId)
    {
        DB::table('products')->where('id', $productId)->update(['option1' => null, 'option2' => null]);
        $id_subOption = DB::table('product_options')->select('id')->where('product_id', $productId)->get();
        DB::table('product_options')->where('product_id', $productId)->delete();
        foreach ($id_subOption as $Idsuboption) {
            DB::table('product_suboptions')->where('op_id', $Idsuboption->id)->delete();
        }
        return response()->json([
            'success' => "Delete sub option product successfully.",
        ], 201);
    }

    public function getOrders(Request $request)
    {
        // $orders2 = DB::table('orders')
        //     ->leftjoin('order_details', 'orders.id', '=', 'order_details.order_id')
        //     ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
        //     ->leftjoin('addresses', 'addresses.id', '=', 'orders.address_id')
        //     ->leftjoin('transections', 'transections.order_id', '=', 'orders.id')
        //     ->leftjoin('bankaccounts', 'bankaccounts.id', '=', 'transections.bankaccount_id')
        //     ->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')
        //     ->orderBy('orders.id', 'DESC')
        //     ->select(
        //         'orders.id as ID',
        //         'orders.invoice_id as orderId',
        //         DB::raw('GROUP_CONCAT(products.img_product) as imageThumbnail'),
        //         'addresses.name as receiverName',
        //         'addresses.province as address',
        //         'addresses.tel as phoneNumber',
        //         DB::raw('GROUP_CONCAT(orders.num) as quantity'),
        //         DB::raw('GROUP_CONCAT(orders.price) as amount'),
        //         'banks.icon_bank as bankThumbnail',
        //         'orders.created_at as createAt',
        //         'orders.status as status'
        //     )
        //     ->groupBy('orders.id', 'orders.invoice_id', 'addresses.name', 'addresses.province', 'addresses.tel', 'banks.icon_bank', 'orders.created_at', 'orders.status')
        //     ->get();
        $search = $request->search;
        $searchDate = $request->searchDate;
        $orders2 = DB::table('orders')
            ->leftJoin('addresses', 'addresses.id', '=', 'orders.address_id')
            ->leftJoin('transections', 'transections.order_id', '=', 'orders.id')
            ->leftJoin('bankaccounts', 'bankaccounts.id', '=', 'transections.bankaccount_id')
            ->leftjoin('banks', 'banks.id', '=', 'bankaccounts.bank_id')
            ->orderBy('orders.id', 'DESC')
            ->select([
                'orders.id as ID',
                'orders.invoice_id as orderId',
                'addresses.name as receiverName',
                'addresses.address as address',
                'addresses.district as district',
                'addresses.sub_district as sub_district',
                'addresses.province as province',
                'addresses.postcode as postcode',
                'addresses.tel as phoneNumber',
                'orders.num as quantity',
                'orders.price as amount',
                'banks.icon_bank as bankThumbnail',
                'banks.name_bank as nameBank',
                'orders.created_at as createAt',
                'orders.updated_at as updateAt',
                'orders.status as status',
                'orders.type_payment as typePaymentOrder',
                'transections.slip as slipPayment',
                'bankaccounts.bankaccount_number as accountNumber',
                'transections.date as dateSlipPayment',
                'transections.time as timeSlipPayment',
            ])
            ->where('orders.status', $request->navbarTab)
            ->where(function ($query) use ($search, $searchDate) {
                if (!empty($searchDate) && empty($search)) {
                    $query->whereDate('orders.created_at', $searchDate);
                } elseif (empty($searchDate) && !empty($search)) {
                    $query->where(function ($query) use ($search) {
                        $query->orWhere('orders.invoice_id', 'like', '%' . $search . '%')
                            ->orWhere('addresses.name', 'like', '%' . $search . '%')
                            ->orWhere('addresses.address', 'like', '%' . $search . '%')
                            ->orWhere('addresses.tel', 'like', '%' . $search . '%')
                            ->orWhere('orders.price', 'like', '%' . $search . '%');
                    });
                } elseif (!empty($searchDate) && !empty($search)) {
                    $query->where(function ($query) use ($search, $searchDate) {
                        $query->orWhere('orders.invoice_id', 'like', '%' . $search . '%')
                            ->orWhere('addresses.name', 'like', '%' . $search . '%')
                            ->orWhere('addresses.address', 'like', '%' . $search . '%')
                            ->orWhere('addresses.tel', 'like', '%' . $search . '%')
                            ->orWhere('orders.price', 'like', '%' . $search . '%');
                    })->whereDate('orders.created_at', $searchDate);
                }
            })
            ->paginate($request->numShowItems);
        foreach ($orders2 as $value) {
            $data = DB::table('order_details')
                ->leftJoin('products', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('product_options', 'product_options.id', '=', 'order_details.option1')
                ->leftJoin('product_suboptions', 'product_suboptions.id', '=', 'order_details.option2')
                ->orderBy('order_details.id', 'desc')
                ->select([
                    'order_details.id as orderDetailID',
                    'products.img_product as imgProduct',
                    'product_options.img_name as imgProductOption',
                    'order_details.option1 as option1',
                    'order_details.option2 as option2',
                    'products.name_product as nameProduct',
                    'order_details.num as num',
                    'order_details.price as price',
                    'products.price as priceProduct',
                    'product_options.price as priceProductOption1',
                    'product_suboptions.price as priceProductOption2',
                ])
                ->where('order_details.order_id', $value->ID)
                ->get();
            $value->orderDetails = $data;
        }
        // ราคารวมและจำนวนของ order ทั้งหมด ของแต่ละสถานะ
        $total_Amount = DB::table('orders')->where('status', $request->navbarTab)->sum('price');
        $total_Num = DB::table('orders')->where('status', $request->navbarTab)->sum('num');

        // จำนวน order แต่ละสถานะ
        $count_status1 = DB::table('orders')->where('status', 'ตรวจสอบคำสั่งซื้อ')->count();
        $count_status2 = DB::table('orders')->where('status', 'กำลังแพ็ค')->count();
        $count_status3 = DB::table('orders')->where('status', 'พร้อมส่ง')->count();
        $count_status4 = DB::table('orders')->where('status', 'จัดส่งสำเร็จ')->count();
        $count_status5 = DB::table('orders')->where('status', 'ส่งสำเร็จ')->count();
        $count_status6 = DB::table('orders')->where('status', 'ตีกลับ')->count();
        $count_status7 = DB::table('orders')->where('status', 'ยกเลิก')->count();
        return response()->json([
            'orders' => $orders2,
            'count_status1' => $count_status1,
            'count_status2' => $count_status2,
            'count_status3' => $count_status3,
            'count_status4' => $count_status4,
            'count_status5' => $count_status5,
            'count_status6' => $count_status6,
            'count_status7' => $count_status7,
            'total_Amount' => $total_Amount,
            'total_Num' => $total_Num,
        ], 201);
    }

    public function setStatusOrders(Request $request)
    {
        DB::table('orders')->where('id', $request->id)->update([
            'status' => $request->status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json([
            'success' => 'Set status successfully',
        ], 201);
    }

    public function setStatusOrdersMulti(Request $request)
    {
        if ($request->ids) {
            foreach ($request->ids as $ID) {
                DB::table('orders')->where('id', $ID)->update([
                    'status' => $request->status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        return response()->json([
            'success' => 'Set status successfully',
        ], 201);
    }

    public function hookSellPang(Request $request)
    {
        try {
            $message = trim($request->getContent());
            if ($message) {
                $json_obj = json_decode($message);
                foreach ($json_obj->items as $item) {
                    if ($item->status == "501") {
                        DB::table('orders')->where('tracking', $item->barcode)->update([
                            'status' => 'ส่งสำเร็จ'
                        ]);
                    } else if ($item->status == "203" || $item->status == "104") {
                        DB::table('orders')->where('tracking', $item->barcode)->update([
                            'status' => 'ตีกลับ'
                        ]);
                    } else if ($item->status == "209" || $item->status == "210") {
                        DB::table('orders')->where('tracking', $item->barcode)->update([
                            'status' => 'ยกเลิก'
                        ]);
                    }
                }
                return response(['message' => 'success data'], 400);
            }
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 422);
        }
    }

    public function addTrackingOrder(Request $request)
    {
        if ($request->orderId && $request->tracking) {
            DB::table('orders')->where('id', $request->orderId)->update([
                'tracking' => $request->tracking,
                'shipping' => $request->shipping,
                'status' => 'จัดส่งสำเร็จ',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $thaipost_token = DB::table('thaipost_token')->select('*')->first();
            if ($thaipost_token === null) {

                $mainToken = env('MAIN_TOKEN_THAIPOST');

                $url = "https://trackwebhook.thailandpost.co.th/post/api/v1/authenticate/token";

                $response = file_get_contents($url, false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/json\r\nAuthorization: Token $mainToken\r\n"
                    ]
                ]));

                // แปลง response เป็นรูปแบบของออบเจ็กต์
                $responseData = json_decode($response);

                // เก็บข้อมูลลงในฐานข้อมูล
                if ($responseData !== null) {
                    DB::table('thaipost_token')->insert([
                        'token' => $responseData->token,
                        'expire' => $responseData->expire
                    ]);

                    $url_insert_track = "https://trackwebhook.thailandpost.co.th/post/api/v1/hook";

                    $body = json_encode([
                        "status" => "all",
                        "language" => "TH",
                        "barcode" => [
                            $request->tracking,
                        ],
                        "req_previous_status" => true
                    ]);

                    $response_insert_track = file_get_contents($url_insert_track, false, stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => "Content-type: application/json\r\nAuthorization: Token $responseData->token\r\n",
                            'content' => $body
                        ]
                    ]));

                    // แปลง response เป็นรูปแบบของออบเจ็กต์
                    $responseDataInsertTrack = json_decode($response_insert_track);

                    if ($responseDataInsertTrack->message === 'successful') {
                        return response()->json([
                            'success' => 'Insert tracking successfully',
                        ], 201);
                    }
                }
            } else {
                $now = \Carbon\Carbon::now();
                if ($now->greaterThan(\Carbon\Carbon::parse($thaipost_token->expire))) {
                    // หมดอายุแล้ว
                    $mainToken = env('MAIN_TOKEN_THAIPOST');

                    $url = "https://trackwebhook.thailandpost.co.th/post/api/v1/authenticate/token";

                    $response = file_get_contents($url, false, stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => "Content-type: application/json\r\nAuthorization: Token $mainToken\r\n"
                        ]
                    ]));

                    // แปลง response เป็นรูปแบบของออบเจ็กต์
                    $responseData = json_decode($response);

                    if ($responseData !== null) {
                        DB::table('thaipost_token')->where('id', $thaipost_token->id)->update([
                            'token' => $responseData->token,
                            'expire' => $responseData->expire
                        ]);

                        $url_insert_track = "https://trackwebhook.thailandpost.co.th/post/api/v1/hook";

                        $body = json_encode([
                            "status" => "all",
                            "language" => "TH",
                            "barcode" => [
                                $request->tracking,
                            ],
                            "req_previous_status" => true
                        ]);

                        $response_insert_track = file_get_contents($url_insert_track, false, stream_context_create([
                            'http' => [
                                'method' => 'POST',
                                'header' => "Content-type: application/json\r\nAuthorization: Token $responseData->token\r\n",
                                'content' => $body
                            ]
                        ]));

                        // แปลง response เป็นรูปแบบของออบเจ็กต์
                        $responseDataInsertTrack = json_decode($response_insert_track);

                        if ($responseDataInsertTrack->message === 'successful') {
                            return response()->json([
                                'success' => 'Insert tracking successfully',
                            ], 201);
                        }
                    }
                } else {
                    // ยังไม่หมดอายุ
                    $url_insert_track = "https://trackwebhook.thailandpost.co.th/post/api/v1/hook";

                    $body = json_encode([
                        "status" => "all",
                        "language" => "TH",
                        "barcode" => [
                            $request->tracking,
                        ],
                        "req_previous_status" => true
                    ]);

                    $response_insert_track = file_get_contents($url_insert_track, false, stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => "Content-type: application/json\r\nAuthorization: Token $thaipost_token->token\r\n",
                            'content' => $body
                        ]
                    ]));

                    // แปลง response เป็นรูปแบบของออบเจ็กต์
                    $responseDataInsertTrack = json_decode($response_insert_track);

                    if ($responseDataInsertTrack->message === 'successful') {
                        return response()->json([
                            'success' => 'Insert tracking successfully',
                        ], 201);
                    }
                }
            }
        } else {
            return response()->json([
                'error' => 'Insert tracking not successfully',
            ], 500);
        }
    }

    public function addTrackingOrderKerry(Request $request)
    {
        if ($request->orderId && $request->tracking) {
            DB::table('orders')->where('id', $request->orderId)->update([
                'tracking' => $request->tracking,
                'shipping' => $request->shipping,
                'status' => 'จัดส่งสำเร็จ',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return response()->json([
                'success' => 'Insert tracking successfully',
            ], 201);
        } else {
            return response()->json([
                'error' => 'Insert tracking not successfully',
            ], 500);
        }
    }

    public function addTrackingOrderFlash(Request $request)
    {
        if ($request->orderId && $request->tracking) {
            DB::table('orders')->where('id', $request->orderId)->update([
                'tracking' => $request->tracking,
                'shipping' => $request->shipping,
                'status' => 'จัดส่งสำเร็จ',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return response()->json([
                'success' => 'Insert tracking successfully',
            ], 201);
        } else {
            return response()->json([
                'error' => 'Insert tracking not successfully',
            ], 500);
        }
    }
}
