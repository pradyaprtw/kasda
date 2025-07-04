<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstansiController extends Controller
{
    public function index(){
        return view('display-instansi');
    }
}
