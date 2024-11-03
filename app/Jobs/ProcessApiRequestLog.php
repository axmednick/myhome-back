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
    protected $userAgent;

    public function __construct($ipAddress, $date, $userAgent)
    {
        $this->ipAddress = $ipAddress;
        $this->date = $date;
        $this->userAgent = $userAgent;
    }

    public function handle()
    {
        $existingRequest = ApiRequest::firstOrCreate([
            'ip_address' => $this->ipAddress,
            'date' => $this->date,
            'user_agent' => $this->userAgent,
        ]);

        $dailyStats = DailyStatistic::firstOrCreate(['date' => $this->date]);

        if ($existingRequest->wasRecentlyCreated) {
            $dailyStats->increment('unique_hosts');
        }

        $dailyStats->increment('hits');
    }
}
