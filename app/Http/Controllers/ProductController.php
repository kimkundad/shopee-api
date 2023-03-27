<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\category;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Response;
use App\Models\ownershop;
use Illuminate\Support\Facades\Storage;
use App\Models\product_image;
use App\Models\product_option;
use App\Models\product_suboption;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
        $objs = DB::table('products')->select(
            'products.*',
            'products.id as id_q',
            'products.active as active',
            'categories.*',
            'ownershops.*'
            )
            ->leftjoin('categories', 'categories.id',  'products.category')
            ->leftjoin('ownershops', 'ownershops.user_code',  'products.user_code')
            ->paginate(15);

            $objs->setPath('');
        $data['objs'] = $objs;

        
        return view('admin.products.index', compact('objs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $cat = category::all();
        $data['cat'] = $cat;
        $ownershop = ownershop::all();
        $data['ownershop'] = $ownershop;
        $data['method'] = "post";
        $data['url'] = url('admin/products');
        return view('admin.products.create', $data);
    }


    public function api_post_status_products(Request $request){

        $user = product::findOrFail($request->user_id);

              if($user->active == 1){
                  $user->active = 0;
              } else {
                  $user->active = 1;
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
            'img_product' => 'required',
            'user_code' => 'required',
            'category' => 'required',
            'name_product' => 'required',
            'detail_product' => 'required',
            'price' => 'required',
            'price_sales' => 'required',
            'cost' => 'required',
            'stock' => 'required',
            'weight' => 'required',
            'sku' => 'required'
           ]);

           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

           $image = $request->file('img_product');

           $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
           $img = Image::make($image->getRealPath());
           $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
           });
           $img->stream();
           Storage::disk('do_spaces')->put('shopee/products/'.$image->hashName(), $img, 'public');

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
           $objs->img_product = $image->hashName();
           $objs->active = $status;
           $objs->save();

           return redirect(url('admin/products'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        $sub = DB::table('product_suboptions')->select(
            'product_suboptions.*',
            'product_suboptions.id as id_q',
            'product_suboptions.price as pricex',
            'product_suboptions.stock as stockx',
            'product_suboptions.sku as skux',
            'product_suboptions.status as statusx',
            'product_options.*'
            )
            ->leftjoin('product_options', 'product_options.id',  'product_suboptions.op_id')
            ->get();

        $data['sub'] = $sub;


        $option = product_option::where('product_id', $id)->get();
        $data['option'] = $option;

        $cat = category::all();
        $data['cat'] = $cat;
        $ownershop = ownershop::all();
        $data['ownershop'] = $ownershop;

        $img = product_image::where('product_id', $id)->get();
        $data['img'] = $img;

        $objs = product::find($id);
        $data['url'] = url('admin/products/'.$id);
        $data['method'] = "put";
        $data['item'] = $objs;
        $data['pro_id'] = $id;
        return view('admin.products.edit', $data);
    }


    public function upload_img_product(Request $request, $id){
       
        $gallary = $request->file('file');

        $this->validate($request, [
             'file' => 'required|max:8048'
         ]);

           $input['imagename'] = time().'.'.$gallary->getClientOriginalExtension();
           $img = Image::make($gallary->getRealPath());
           $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
           });
           $img->stream();
           Storage::disk('do_spaces')->put('shopee/products/'.$gallary->hashName(), $img, 'public');

            $admins[] = [
                'image' => $gallary->hashName(),
                'product_id' => $id
            ];
          
          product_image::insert($admins);
        
          return Response::json(array('success' => true, 'message' => 'Successfully uploaded file.'), 200);

    }

    public function image_del($id){

        $objs = DB::table('product_images')
            ->where('id', $id)
            ->first();

          $pid = $objs->product_id;

            if(isset($objs->image)){
             
                $storage = Storage::disk('do_spaces');
                $storage->delete('shopee/products/' . $objs->image, 'public');
            }

        $obj = product_image::find($id);
        $obj->delete();

        return redirect(url('admin/products/'.$pid.'/edit'))->with('edit_success','คุณทำการเพิ่มอสังหา สำเร็จ');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function post_sup_option1(Request $request, $id){


        //id_product
        $this->validate($request, [
            'price' => 'required',
            'stock' => 'required',
            'op_name' => 'required',
            'sku' => 'required'
           ]);

        $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            } 

           $objs = new product_suboption();
           $objs->op_id = $id;
           $objs->price = $request['price'];
           $objs->stock = $request['stock'];
           $objs->sku = $request['sku'];
           $objs->sub_op_name = $request['op_name'];
           $objs->status = $status;
           $objs->save();

           return redirect(url('admin/products/'.$request['id_product'].'/edit'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');

    }


    public function post_option1(Request $request, $id){

        $type_product = $request['type_product'];

        if($type_product == 1){

        }
        if($type_product == 2){

            $this->validate($request, [
                'img_option1' => 'required',
                'price' => 'required',
                'op_name' => 'required',
                'stock' => 'required',
                'type_product' => 'required',
                'sku' => 'required'
               ]);

        }
        if($type_product == 3){

            $this->validate($request, [
                'img_option1' => 'required'
               ]);

        }

            $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

           $img = product_image::where('id', $request['img_option1'])->first();

           $objs = new product_option();
           $objs->product_id = $id;
           $objs->img_id = $request['img_option1'];
           $objs->img_name = $img->image;
           $objs->price = $request['price'];
           $objs->stock = $request['stock'];
           $objs->sku = $request['sku'];
           $objs->op_name = $request['op_name'];
           $objs->status = $status;
           $objs->save();

           return redirect(url('admin/products/'.$id.'/edit'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');

     }

    public function update(Request $request, $id)
    {
        //

        $this->validate($request, [
            'user_code' => 'required',
            'category' => 'required',
            'name_product' => 'required',
            'detail_product' => 'required',
            'price' => 'required',
            'price_sales' => 'required',
            'cost' => 'required',
            'stock' => 'required',
            'weight' => 'required',
            'sku' => 'required'
           ]);

           $image = $request->file('img_product');

           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

            if($image == NULL){

                $objs = product::find($id);
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
           $objs->type = $request['type'];
           $objs->option1 = $request['option1'];
           $objs->option2 = $request['option2'];
           $objs->active = $status;
           $objs->save();

            }else{

          $img_old = DB::table('products')
          ->where('id', $id)
          ->first();

          if(isset($img_old)){
             
            $storage = Storage::disk('do_spaces');
            $storage->delete('shopee/products/' . $img_old->img_product, 'public');
        }
          

           $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
           $img = Image::make($image->getRealPath());
           $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
           });
           $img->stream();
           Storage::disk('do_spaces')->put('shopee/products/'.$image->hashName(), $img, 'public');


           $objs = product::find($id);
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
           $objs->img_product = $image->hashName();
           $objs->type = $request['type'];
           $objs->option1 = $request['option1'];
           $objs->option2 = $request['option2'];
           $objs->active = $status;
           $objs->save();

            }

            return redirect(url('admin/products/'.$id.'/edit'))->with('edit_success','คุณทำการเพิ่มอสังหา สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function del_products($id)
    {
        //

        $img_old = DB::table('products')
        ->where('id', $id)
        ->first();

        if(isset($img_old)){
           
          $storage = Storage::disk('do_spaces');
          $storage->delete('shopee/products/' . $img_old->img_product, 'public');
      }

      $img_g = DB::table('product_images')
        ->where('product_id', $id)
        ->get();

        if(isset($img_g)){
            foreach($img_g as $u){

                $storage = Storage::disk('do_spaces');
                $storage->delete('shopee/products/' . $u->image, 'public');

            }
        }

        $obj = product::find($id);
        $obj->delete();

        return redirect(url('admin/products/'))->with('del_success','คุณทำการลบอสังหา สำเร็จ');


    }
}
