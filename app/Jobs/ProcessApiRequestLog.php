<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ApiRequest;
use App\Models\DailyStatistic;

class ProcessApiRequestLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ipAddress;
    protected $date;

    public function __construct($ipAddress, $date)
    {
        $this->ipAddress = $ipAddress;
        $this->date = $date;
    }

    public function handle()
    {
        // 1. IP-yə görə günlük hostu qeyd etmək
        $existingRequest = ApiRequest::firstOrCreate([
            'ip_address' => $this->ipAddress,
            'date' => $this->date,
        ]);

        // 2. Gündəlik statistik məlumatları yeniləmək
        $dailyStats = DailyStatistic::firstOrCreate([
            'date' => $this->date,
        ]);

        // Unique host əlavə etmək
        if ($existingRequest->wasRecentlyCreated) {
            $dailyStats->increment('unique_hosts');
        }

        // Hit sayını artırmaq
        $dailyStats->increment('hits');
    }
}
