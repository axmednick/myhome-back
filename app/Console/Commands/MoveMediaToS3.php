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
            $mediaItems = $announcement->getMedia('image');

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
        foreach ($media->getGeneratedConversions() as $conversion => $status) {
            if ($status) {
                $conversionPath = $media->getPath($conversion);
                $relativeConversionPath = $media->getPathRelativeToRoot($conversion);
                $this->moveFile($conversionPath, $relativeConversionPath, $media->disk);
            }
        }

        // Media modelindeki disk alanı güncelleniyor
        $media->disk = 's3';
        $media->save();
    }

    protected function moveFile($localPath, $relativePath, $disk)
    {
        // Dosyanın mevcut olup olmadığını kontrol et
        if (Storage::disk($disk)->exists($relativePath)) {
            $fileContents = Storage::disk($disk)->get($relativePath);

            // Dosya S3'e yükleniyor
            Storage::disk('s3')->put($relativePath, $fileContents);

            // Mevcut dosya siliniyor
            Storage::disk($disk)->delete($relativePath);

            $this->info("File successfully moved to S3: {$relativePath}");
        } else {
            $this->error("File not found on disk: {$relativePath}");
        }
    }
}
