<?php

namespace App\Http\Controllers;

use App\Models\DataAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use simplehtmldom\HtmlWeb;

class DefaultController extends Controller
{
    public function index(){

        $dataAgents = DataAgent::all();


        return view('index',['agents'=>$dataAgents]);
    }
}
