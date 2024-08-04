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
            $mediaItems = $announcement->getMedia(); // Announcement modeline ait medya dosyalarını alıyoruz

            foreach ($mediaItems as $media) {
                // Mevcut diskten dosya alınıyor
                $filePath = $media->getPath();
                $fileContents = Storage::disk($media->disk)->get($media->getPathRelativeToRoot());

                // Dosya S3'e yükleniyor
                Storage::disk('s3')->put($media->getPathRelativeToRoot(), $fileContents);

                // Mevcut dosya siliniyor
                Storage::disk($media->disk)->delete($media->getPathRelativeToRoot());

                // Media modelindeki disk alanı güncelleniyor
                $media->disk = 's3';
                $media->save();
            }
        }

        $this->info('All media files of Announcements have been moved to S3.');
    }
}
