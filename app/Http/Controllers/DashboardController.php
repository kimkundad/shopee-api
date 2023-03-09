<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(){
      
        $data['sum'] = 1;
        return view('admin.dashboard.index', compact('data'));
    }
}
