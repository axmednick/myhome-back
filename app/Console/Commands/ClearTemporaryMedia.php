<?php

namespace App\Console\Commands;

use App\Models\TemporaryFile;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ClearTemporaryMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-temporary-media';

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
        $expiredTemporaryFiles = TemporaryFile::where('created_at', '<', Carbon::now()->subHour())->get();

        foreach ($expiredTemporaryFiles as $file) {
            $file->clearMediaCollection('image'); // S3-dən silir
            $file->delete();
        }
    }
}
