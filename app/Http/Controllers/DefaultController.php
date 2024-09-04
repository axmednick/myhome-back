<?php

namespace App\Http\Controllers;

use App\Helpers\TelegramHelper;
use App\Models\DataAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mail;
use simplehtmldom\HtmlWeb;

class DefaultController extends Controller
{
    public function index(){


        TelegramHelper::sendMessage(' created a new announcement: ' );



    }
}
