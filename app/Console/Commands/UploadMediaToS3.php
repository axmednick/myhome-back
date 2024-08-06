<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadMediaToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-media-to-s3';

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
        // Get all announcements with media
        $announcements = Announcement::with('media')->get();

        foreach ($announcements as $announcement) {
            foreach ($announcement->media as $media) {
                // Get the file name and path
                $fileName = $media->file_name;
                $filePath = $media->getPath(); // getPath returns relative path

                // Define the full paths for original, thumb, and watermarked
                $originalPath = $filePath;
                $thumbPath = $media->getPath('thumb');
                $watermarkedPath =  $media->getPath('watermarked');

                // Create full paths by combining storage path
                $baseDir = 'public/'; // Adjust if your base directory is different
                $originalFullPath = storage_path("app/{$baseDir}{$originalPath}");
                $thumbFullPath = storage_path("app/{$baseDir}{$thumbPath}");
                $watermarkedFullPath = storage_path("app/{$baseDir}{$watermarkedPath}");

                // Array of paths to check
                $pathsToCheck = [$originalFullPath, $thumbFullPath, $watermarkedFullPath];

                foreach ($pathsToCheck as $path) {
                    if (file_exists($path)) {
                        // Define S3 path, preserving the directory structure
                        $s3Path = 'media/' . str_replace(storage_path('app/public/'), '', $path);

                        // Upload to S3
                        Storage::disk('s3')->put($s3Path, file_get_contents($path));

                        // Update media model to use S3
                        $media->update([
                            'disk' => 's3',
                            'custom_properties' => array_merge($media->custom_properties, [
                                's3_path' => $s3Path,
                            ]),
                        ]);

                        // Optionally, you can delete local file after upload
                        // unlink($path);
                    } else {
                        $this->error("File not found: $path");
                    }
                }
            }
        }

        $this->info('All media files have been uploaded to S3 and media library records updated.');
    }




}
