<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;

class MoveMediaToS3 extends Command
{
    protected $signature = 'media:move-to-s3';
    protected $description = 'Move media files of Announcements to S3';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $announcements = Announcement::all();

        foreach ($announcements as $announcement) {
            $mediaItems = $announcement->getMedia('image'); // Belirli medya koleksiyonunu alıyoruz

            foreach ($mediaItems as $media) {
                $this->moveMediaToS3($media);
            }
        }

        $this->info('All media files of Announcements have been moved to S3.');
    }

    protected function moveMediaToS3(Media $media)
    {
        // Orijinal dosyayı taşı
        $this->moveFile($media->getPath(), $media->getPathRelativeToRoot(), $media->disk);

        // Dönüştürülmüş dosyaları taşı
        if (!empty($media->generatedConversions) && is_array($media->generatedConversions)) {
            foreach ($media->generatedConversions as $conversion => $status) {
                if ($status) {
                    $conversionPath = $media->getPath($conversion);
                    $relativeConversionPath = $media->getPathRelativeToRoot($conversion);

                    // Dönüştürülmüş dosyanın mevcut olup olmadığını kontrol et
                    if (Storage::disk($media->disk)->exists($relativeConversionPath)) {
                        $this->moveFile($conversionPath, $relativeConversionPath, $media->disk);
                    } else {
                        $this->error("Conversion file not found: {$relativeConversionPath}");
                    }
                }
            }
        }

        // Media modelindeki disk alanı güncelleniyor
        $media->disk = 's3';
        $media->save();
    }

    protected function moveFile($localPath, $relativePath, $disk)
    {
        // Mevcut diskten dosya var mı kontrol et
        if (Storage::disk($disk)->exists($relativePath)) {
            $fileContents = Storage::disk($disk)->get($relativePath);

            // Dosya S3'e yükleniyor
            if ($fileContents !== null) {
                Storage::disk('s3')->put($relativePath, $fileContents);

                // Mevcut dosya siliniyor
                Storage::disk($disk)->delete($relativePath);
            } else {
                $this->error("File contents are null for: {$relativePath}");
            }
        } else {
            $this->error("File not found on disk: {$relativePath}");
        }
    }
}
