<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite($id){
        $favorite = Favorite::where('announcement_id',$id)->where('user_id',auth('sanctum')->id())->first();
        if ($favorite){
            $favorite->delete();
        }else{
            Favorite::create([
                'user_id' =>auth('sanctum')->id(),
                'announcement_id' =>$id
            ]);
        }
    }
}
