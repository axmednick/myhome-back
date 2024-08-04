<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MoveMediaToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
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
        foreach ($media->generatedConversions as $conversion => $status) {
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
        // Mevcut diskten dosya alınıyor
        $fileContents = Storage::disk($disk)->get($relativePath);

        // Dosya S3'e yükleniyor
        Storage::disk('s3')->put($relativePath, $fileContents);

        // Mevcut dosya siliniyor
        Storage::disk($disk)->delete($relativePath);
    }
}
