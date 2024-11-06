<?php

namespace App\Nova\Metrics;

use App\Models\Announcement;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class UserPerAnnouncement extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function calculate(NovaRequest $request)
    {
        // Retrieve the top 10 users by announcement count
        $topUsers = Announcement::query()
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->mapWithKeys(function ($item) {
                // Retrieve the user's name or fallback to 'Unknown User'
                $userName = optional(\App\Models\User::find($item->user_id))->name ?? 'Unknown User';
                return [$userName => $item->count];
            });

        // Return the result as a PartitionResult
        return $this->result($topUsers->toArray());
    }


    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'user-per-announcement';
    }
}
