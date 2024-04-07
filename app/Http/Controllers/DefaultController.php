<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use simplehtmldom\HtmlWeb;

class DefaultController extends Controller
{
    public function index(){

        return view('index');
    }
}
