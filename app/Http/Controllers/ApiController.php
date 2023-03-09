<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    //
    public function get_category_all(){

        $objs = category::select('cat_name','image')->where('status', 1)->get();

        return response()->json([
            'category' => $objs,
            'full_paht_img' => env('APP_URL').'/images/shopee/category/'
        ], 201);
        
    }
}
