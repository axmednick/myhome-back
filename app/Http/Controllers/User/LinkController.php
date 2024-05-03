<?php

namespace App\Http\Controllers\User;

use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{

    public function index(){

        $links = Link::where('user_id',auth('sanctum')->id())->get();

        return LinkResource::collection($links);
    }

    public function generate(Request $request){

        return response()->json(['link'=>'https://myhome.az/link/'.StringHelper::randomString()]);
    }

    public function store(Request $request){

        $validate = Validator::make($request->all(),[
            'name'=>'required|min:1|max:50',
            'link'=>'required'
        ]);


        $link = Link::create([
            'user_id'=>auth('sanctum')->id(),
            'name'=>$request->name,
            'link'=>$request->link,
            'announcement_ids'=>json_encode($request->announcement_ids,true)
        ]);

        return LinkResource::make($link);



    }


    public function delete($id){
        $link = Link::findOrFail($id);

        if ($link->user_id==auth('sanctum')->id()){
            $link->delete();
        }
    }
}
