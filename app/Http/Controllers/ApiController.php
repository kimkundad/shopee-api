<?php

namespace App\Http\Controllers;

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
        $product_id = $request->input('product_id');
        $shop_id = $request->input('shop_id');
        $objs = DB::table('shop_list_products')
            ->join('products', 'shop_list_products.product_id', '=', 'products.id')
            ->where('shop_list_products.shop_id', '=', $shop_id)
            ->where('products.id', '=', $product_id)
            ->where('products.active', '=', 1)
            ->get();
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

    // เพิ่มสินค้าหน้าคลังสินค้า
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

    // ดึงข้อมูลร้านค้า
    public function get_shop_name($id)
    {

        $objs = shop::where('code_shop', '=', $id)->get();

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
        ])->get();
        if($cartItem !== null){
            $sum = $cartItem + $request->input('num');
            DB::table('carts')->where('id','=', $cartItem->id)->update([
                'num' => $sum
            ]);
        }else {
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
            'status' => $request->input('user_id'),
        ], 201);
    }

    // ดึงข้อมูลในรถเข็นมาแสดง
    public function getAllCartItem($id)
    {
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
        ], 201);
    }

    // ลบสินค้าออกจากรถเข็น
    public function deleteItemCart($id){
        DB::table('carts')->where('id','=',$id)->delete();

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

    // ดึงข้อมูลของ users และ role ของ users ออกมาทั้งหมด
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

    // ฟังก์ชันสร้าง Sub-Admin
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

    // ฟังก์ชันลบข้อมูล Sub-Admin
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

    // ฟังก์ชัน filter ค้นหาข้อมูล Sub-admin จากวันที่สร้าง
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

    // ฟังก์ชันค้นหาข้อมูล Sub-Admin จากชื่อของ sub-admin
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
}
