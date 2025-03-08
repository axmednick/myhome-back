<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSql extends Command
{
    /**
     * Command imzası.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * Komanda təsviri.
     *
     * @var string
     */
    protected $description = 'SQL faylını oxuyub database-ə import edir';

    /**
     * Command-i icra edir.
     *
     * @return int
     */
    public function handle()
    {
        $file = public_path('db.sql');

        if (!File::exists($file)) {
            $this->error("Fayl tapılmadı: {$file}");
            return 1;
        }

        $sql = File::get($file);

        try {
            DB::unprepared($sql);
            $this->info("SQL faylı uğurla import edildi.");
        } catch (\Exception $e) {
            $this->error("Import zamanı xəta baş verdi: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
