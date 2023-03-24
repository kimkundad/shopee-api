<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ownershop;
use App\Models\User;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OwnershopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $objs = ownershop::paginate(30);

        $objs->setPath('');
        $data['objs'] = $objs;
        return view('admin.ownershop.index', compact('objs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $user = User::all();
        $data['user'] = $user;
        $data['method'] = "post";
        $data['url'] = url('admin/ownershop');
        return view('admin.ownershop.create', $data);
    }


    public function api_post_status_ownershop(Request $request){

        $user = ownershop::findOrFail($request->user_id);

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
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required',
            'user_id' => 'required',
            'email' => 'required'
           ]);

           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

           $ran_num = rand(1000000,9999999);

           $objs = new ownershop();
           $objs->fname = $request['fname'];
           $objs->lname = $request['lname'];
           $objs->phone = $request['phone'];
           $objs->user_id = $request['user_id'];
           $objs->email = $request['email'];
           $objs->user_code = $ran_num;
           $objs->status = $status;
           $objs->save();

           return redirect(url('admin/ownershop'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');
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
        $user = User::all();
        $data['user'] = $user;
        $objs = ownershop::find($id);
        $data['url'] = url('admin/ownershop/'.$id);
        $data['method'] = "put";
        $data['item'] = $objs;
        $data['pro_id'] = $id;
        return view('admin.ownershop.edit', $data);
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
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required',
            'user_id' => 'required',
            'email' => 'required'
           ]);

           $status = 0;
            if(isset($request['status'])){
                if($request['status'] == 1){
                    $status = 1;
                }
            }

           $objs = ownershop::find($id);
           $objs->fname = $request['fname'];
           $objs->lname = $request['lname'];
           $objs->phone = $request['phone'];
           $objs->user_id = $request['user_id'];
           $objs->email = $request['email'];
           $objs->status = $status;
           $objs->save();


           return redirect(url('admin/ownershop/'.$id.'/edit'))->with('edit_success','คุณทำการเพิ่มอสังหา สำเร็จ');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function del_ownershop($id)
    {
        //
        $obj = ownershop::find($id);
        $obj->delete();

        return redirect(url('admin/ownershop/'))->with('del_success','คุณทำการลบอสังหา สำเร็จ');
    }
}
