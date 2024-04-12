<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\TemporaryFile;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function temporaryFile(Request $request){

        $temporaryFile=TemporaryFile::create([]);

        $temporaryFile->addMediaFromRequest('file')->toMediaCollection('image');
        return response()->json([
            'id'=>$temporaryFile->id,
            'url'=>$temporaryFile->getFirstMediaUrl('image')
        ]);
    }
}
