<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $objs = category::paginate(30);

        $objs->setPath('');
        $data['objs'] = $objs;
        return view('admin.category.index', compact('objs'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['method'] = "post";
        $data['url'] = url('admin/category');
        return view('admin.category.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function api_post_status_category(Request $request){

        $user = category::findOrFail($request->user_id);

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


    public function store(Request $request)
    {
        //
   
           $this->validate($request, [
            'cat_name' => 'required',
            'image' => 'required'
           ]);
           
           $image = $request->file('image');

           $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
           $img = Image::make($image->getRealPath());
           $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
           });
           $img->stream();
           Storage::disk('do_spaces')->put('shopee/category/'.$image->hashName(), $img, 'public');


        $status = 0;
        if(isset($request['status'])){
            if($request['status'] == 1){
                $status = 1;
            }
        }
     
           $objs = new category();
           $objs->cat_name = $request['cat_name'];
           $objs->image = $image->hashName();
           $objs->status = $status;
           $objs->save();

           return redirect(url('admin/category'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');
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
        

        $objs = category::find($id);
        $data['url'] = url('admin/category/'.$id);
        $data['method'] = "put";
        $data['objs'] = $objs;
        return view('admin.category.edit', $data);
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
            'cat_name' => 'required'
           ]);
           
           $image = $request->file('image');

           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

           if($image == NULL){

           $objs = category::find($id);
           $objs->cat_name = $request['cat_name'];
           $objs->status = $status;
           $objs->save();

           }else{

            $img = DB::table('categories')
          ->where('id', $id)
          ->first();

          Storage::disk('do_spaces')->delete('shopee/category/'.$img->image);

          $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
          $img = Image::make($image->getRealPath());
          $img->resize(300, 300, function ($constraint) {
           $constraint->aspectRatio();
          });
          $img->stream();
          Storage::disk('do_spaces')->put('shopee/category/'.$image->hashName(), $img, 'public');

            
     
           $objs = category::find($id);
           $objs->cat_name = $request['cat_name'];
           $objs->image = $image->hashName();
           $objs->status = $status;
           $objs->save();

           }

           
           return redirect(url('admin/category/'.$id.'/edit'))->with('edit_success','คุณทำการเพิ่มอสังหา สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function del_cat($id)
    {
        //

        $objs = DB::table('categories')
            ->where('id', $id)
            ->first();

            Storage::disk('do_spaces')->delete('shopee/category/'.$objs->image);

           
        $obj = category::find($id);
        $obj->delete();

        return redirect(url('admin/category/'))->with('del_success','คุณทำการลบอสังหา สำเร็จ');
    }
}
