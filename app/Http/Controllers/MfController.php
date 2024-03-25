<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mf;
class MfController extends Controller
{
    public function index(){
        $mfs = Mf::all();
       return view('mf-list', compact('mfs'));
    }
}
