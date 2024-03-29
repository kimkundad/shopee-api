<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\shop;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ownershop;
use App\Models\shop_list_product;
use App\Models\User;


class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $objs = DB::table('shops')->select(
            'shops.*',
            'shops.id as id_q',
            'shops.status as status1',
            'ownershops.*'
            )
            ->leftjoin('ownershops', 'ownershops.user_code',  'shops.user_code')
            ->paginate(15);

            $objs->setPath('');

        return view('admin.shops.index', compact('objs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $ownershop = ownershop::all();
        $data['ownershop'] = $ownershop;
        $data['method'] = "post";
        $data['url'] = url('admin/shops');
        return view('admin.shops.create', $data);
    }

    public function api_add_product_shops(Request $request){

       $count = shop_list_product::where('product_id', $request->pro_id)
                ->where('shop_id', $request->shop_id)
                ->count();

        if($count > 0){

            shop_list_product::where('product_id', $request->pro_id)
                ->where('shop_id', $request->shop_id)
                ->delete();

        }else{

           $objs = new shop_list_product();
           $objs->product_id = $request['pro_id'];
           $objs->shop_id = $request['shop_id'];
           $objs->save();

        }

        return response()->json([
            'data' => [
              'success' => 'success',
            ]
          ]);

    }

    public function api_post_status_shops(Request $request){

        $user = shop::findOrFail($request->user_id);

              if($user->status == 1){
                  $user->status = 0;
              } else {
                  $user->status = 1;
              }


      return response()->json([
      'data' => [
        'success' => $user->save(),
      ]
    ]);

     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'img_shop' => 'required',
            'cover_img_shop' => 'required',
            'user_code' => 'required',
            'name_shop' => 'required',
            'detail_shop' => 'required'
           ]);


           $image = $request->file('img_shop');

           $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
           $img = Image::make($image->getRealPath());
           $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
           });
           $img->stream();
           Storage::disk('do_spaces')->put('shopee/shop/'.$image->hashName(), $img, 'public');



           $image2 = $request->file('cover_img_shop');

           $input['imagename'] = time().'.'.$image2->getClientOriginalExtension();
           $img2 = Image::make($image2->getRealPath());
           $img2->resize(550, 300, function ($constraint2) {
            $constraint2->aspectRatio();
           });
           $img2->stream();
           Storage::disk('do_spaces')->put('shopee/cover_img_shop/'.$image2->hashName(), $img2, 'public');


           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

            $length = 4;

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
            
            $user = ownershop::where('user_code', $request['user_code'])->first();

           $ran_num = rand(100000000,999999999);

           $objs = new shop();
           $objs->name_shop = $request['name_shop'];
           $objs->detail_shop = $request['detail_shop'];
           $objs->user_code = $request['user_code'];
           $objs->user_id = $user->user_id;
           $objs->code_shop = $ran_num;
           $objs->url_shop = $randomString.''.$ran_num;
           $objs->img_shop = $image->hashName();
           $objs->cover_img_shop = $image2->hashName();
           $objs->status = $status;
           $objs->save();

           return redirect(url('admin/shops'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function add_product_to_shop($id)
    {
        //

        $shop = shop::find($id);

        $array = array();

        $mix_pro = DB::table('shop_list_products')->select(
            'shop_list_products.product_id'
            )
            ->leftjoin('shops', 'shops.id',  'shop_list_products.shop_id')
            ->join('products', 'products.id',  'shop_list_products.product_id')
            ->where('shop_list_products.shop_id', $id)
            ->get();

            foreach($mix_pro as $u){
                $array[] = $u->product_id;
            }

          //  dd($mix_pro);

        $objs = DB::table('products')->select(
            'products.*',
            'products.id as id_q',
            'products.active as active',
            'categories.*',
            'ownershops.*'
            )
            ->leftjoin('categories', 'categories.id',  'products.category')
            ->leftjoin('ownershops', 'ownershops.user_code',  'products.user_code')
            ->where('products.user_code', $shop->user_code)
            ->wherenotin('products.id', $array)
            ->paginate(15);

            $objs->setPath('');

            $shop_id = $id;
        
        return view('admin.shops.pro_shop', compact('objs', 'shop_id'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $ownershop = ownershop::all();
        $data['ownershop'] = $ownershop;
        $objs = shop::find($id);
        $url = url('admin/shops/'.$id);
        $method = "put";
        $item = $objs;
        $pro_id = $id;
        $shop_id = $id;

        $array = array();

        $mix_pro = DB::table('shop_list_products')->select(
            'shop_list_products.product_id'
            )
            ->leftjoin('shops', 'shops.id',  'shop_list_products.shop_id')
            ->join('products', 'products.id',  'shop_list_products.product_id')
            ->where('shop_list_products.shop_id', $id)
            ->get();

            foreach($mix_pro as $u){
                $array[] = $u->product_id;
            }

          //  dd($mix_pro);

        $pro = DB::table('products')->select(
            'products.*',
            'products.id as id_q',
            'products.active as active',
            'categories.*',
            'ownershops.*'
            )
            ->leftjoin('categories', 'categories.id',  'products.category')
            ->leftjoin('ownershops', 'ownershops.user_code',  'products.user_code')
            ->where('products.user_code', $objs->user_code)
            ->wherein('products.id', $array)
            ->paginate(15);

            $pro->setPath('');
            $data['pro'] = $pro;


        return view('admin.shops.edit', compact('pro', 'ownershop', 'item', 'url', 'pro_id', 'shop_id', 'method'));

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
        //

        $this->validate($request, [
            'user_code' => 'required',
            'name_shop' => 'required',
            'detail_shop' => 'required'
           ]);

           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

           $image = $request->file('img_shop');
           $image2 = $request->file('cover_img_shop');

           $img_old = DB::table('shops')
          ->where('id', $id)
          ->first();

           if($image !== NULL){

            if(isset($img_old)){
             
                $storage = Storage::disk('do_spaces');
                $storage->delete('shopee/shop/' . $img_old->img_shop, 'public');
            }

            $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
            $img = Image::make($image->getRealPath());
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->stream();
            Storage::disk('do_spaces')->put('shopee/shop/'.$image->hashName(), $img, 'public');

            $objs = shop::find($id);
           $objs->img_shop = $image->hashName();
           $objs->save();

           }

           if($image2 !== NULL){

            if(isset($img_old)){
             
                $storage = Storage::disk('do_spaces');
                $storage->delete('shopee/cover_img_shop/' . $img_old->cover_img_shop, 'public');
            }

            $input['imagename'] = time().'.'.$image2->getClientOriginalExtension();
           $img2 = Image::make($image2->getRealPath());
           $img2->resize(550, 300, function ($constraint2) {
            $constraint2->aspectRatio();
           });
           $img2->stream();
           Storage::disk('do_spaces')->put('shopee/cover_img_shop/'.$image2->hashName(), $img2, 'public');

           $objs = shop::find($id);
           $objs->cover_img_shop = $image2->hashName();
           $objs->save();

           }


           $user = ownershop::where('user_code', $request['user_code'])->first();

           $objs = shop::find($id);
           $objs->name_shop = $request['name_shop'];
           $objs->detail_shop = $request['detail_shop'];
           $objs->user_code = $request['user_code'];
           $objs->user_id = $user->user_id;
           $objs->status = $status;
           $objs->save();


           return redirect(url('admin/shops/'.$id.'/edit'))->with('edit_success','คุณทำการเพิ่มอสังหา สำเร็จ');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function del_shops($id)
    {
        //
        $img_old = DB::table('shops')
        ->where('id', $id)
        ->first();

        if(isset($img_old)){
           
          $storage = Storage::disk('do_spaces');
          $storage->delete('shopee/shop/' . $img_old->img_shop, 'public');
          $storage->delete('shopee/cover_img_shop/' . $img_old->cover_img_shop, 'public');
      }

        $obj = shop::find($id);
        $obj->delete();

        return redirect(url('admin/shops/'))->with('del_success','คุณทำการลบอสังหา สำเร็จ');
    }
}
