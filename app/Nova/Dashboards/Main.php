<?php

namespace App\Nova\Dashboards;

use App\Models\Announcement;

use App\Nova\Metrics\TodayAnnouncements;
use Coroowicaksono\ChartJsIntegration\AreaChart;
use Coroowicaksono\ChartJsIntegration\LineChart;
use DB;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            (new AreaChart())
                ->title('Announcement Count by Day')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->series([
                    [
                        'label' => 'Daily Announcements',
                        'backgroundColor' => '#f7a35c',
                        'data' => $this->getAnnouncementData(), // This method will retrieve the count data
                    ],
                ])
                ->options([
                    'xaxis' => [
                        'categories' => $this->getAnnouncementDates(), // Dates array for the x-axis
                    ],
                ])
                ->width('2/3'),
           new  TodayAnnouncements(),

            (new LineChart())
                ->title('Daily Host and Hit Counts')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])

                ->series([
                    [
                        'label' => 'Unique Hosts',
                        'borderColor' => '#f7a35c',
                        'data' => $this->getHostCounts(), // Bu metod "unique_hosts" dəyərlərini gətirir
                    ],
                    [
                        'label' => 'Hits',
                        'borderColor' => '#90ed7d',
                        'data' => $this->getHitCounts(), // Bu metod "hits" dəyərlərini gətirir
                    ],
                ])
                ->options([
                    'xaxis' => [
                        'categories' => $this->getDateCategories(), // Tarixləri x oxunda göstərmək üçün
                    ],
                ])
                ->width('full')


        ];
    }

    protected function getAnnouncementData()
    {
        return Announcement::selectRaw('COUNT(*) as count')
            ->groupBy(DB::raw("DATE(created_at)"))
            ->pluck('count')
            ->toArray();
    }

    protected function getAnnouncementDates()
    {
        return Announcement::selectRaw('DATE(created_at) as date')
            ->groupBy(DB::raw("DATE(created_at)"))
            ->pluck('date')
            ->toArray();
    }

    protected function getHostCounts()
    {
        return \App\Models\DailyStatistic::orderBy('date', 'asc')
            ->pluck('unique_hosts')
            ->toArray();
    }

    protected function getHitCounts()
    {
        return \App\Models\DailyStatistic::orderBy('date', 'asc')
            ->pluck('hits')
            ->toArray();
    }

    protected function getDateCategories()
    {
        return \App\Models\DailyStatistic::orderBy('date', 'asc')
            ->pluck('date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            })
            ->toArray();
    }


}
