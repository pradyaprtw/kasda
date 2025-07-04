<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenerimaController extends Controller
{
    public function index(){
        return view('display-penerima');
    }
}
