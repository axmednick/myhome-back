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
    public function create(Request $request){

        $validate = Validator::make($request->all(),[
           'name'=>'required|min:1|max:50'
        ]);

        if ($validate->fails()){
            return response()->json($validate->errors()->all());
        }

        $link = Link::create([
            'user_id'=>auth('sanctum')->user()->id,
           'name'=>$request->name,
           'link'=>StringHelper::randomString()
        ]);

        return LinkResource::make($link);

    }

    public function update(Request $request,$id){

        $link = Link::findOrFail($id);


        $link->announcement_ids=json_encode($request->announcement_id);

        $link->save();

    }


    public function delete($id){
        $link = Link::findOrFail($id);

        if ($link->user_id==auth('sanctum')->id()){
            $link->delete();
        }
    }
}
