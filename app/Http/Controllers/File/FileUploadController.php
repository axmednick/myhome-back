<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Storage;

class FileUploadController extends Controller
{
    public function temporaryFile(Request $request){

        $temporaryFile=TemporaryFile::create([]);

        $temporaryFile->addMediaFromRequest('file')->toMediaCollection('image',config('media-library.disk_name'));
        return response()->json([
            'id'=>$temporaryFile->id,
            'url'=>$temporaryFile->getFirstMediaUrl('image')
        ]);
    }

    public function test(Request $request)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Dosya ismini ve uzantısını alma
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Dosyayı S3'e yükleme
            $path = Storage::disk('s3')->putFileAs('uploads', $file, $fileName);

            // Dosyanın başarıyla yüklenip yüklenmediğini kontrol etme
            if (Storage::disk('s3')->exists($path)) {
                // Dosyanın URL'sini alma
                $url = Storage::disk('s3')->url($path);
                return 'File successfully uploaded to S3! URL: ' . $url;
            } else {
                return 'Failed to upload file to S3.';
            }
        } else {
            return 'No file was uploaded.';
        }


    }
}
