<?php

namespace App\Nova\Actions;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Package;
use Carbon\Carbon;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

class AssignPackageToSelectedUsers extends Action
{
    /**
     * The name of the action.
     *
     * @var string
     */
    public $name = 'Assign Package to Selected Users';

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        // Seçilmiş paketi tapırıq
        $package = Package::find($fields->package_id);
        if (!$package) {
            return Action::danger('Selected package not found.');
        }

        $count = 0;
        foreach ($models as $user) {
            // Mövcud Subscription varsa silirik
            Subscription::where('user_id', $user->id)->delete();

            // Yeni Subscription yaradılır
            Subscription::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($fields->duration_days),
                'is_active' => true,
            ]);
            $count++;
        }

        return Action::message("$count users have been assigned to package {$package->name}.");
    }

    /**
     * Get the fields available on the action.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Package', 'package_id')
                ->options(Package::all()->pluck('name', 'id')->toArray())
                ->rules('required'),

            Number::make('Duration (Days)', 'duration_days')
                ->rules('required', 'integer', 'min:1')
                ->default(30),
        ];
    }
}
