<?php

namespace App\Nova\Actions;

use App\Models\Subscription;
use Carbon\Carbon;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

class RenewSubscription extends Action
{
    /**
     * The name of the action.
     *
     * @var string
     */
    public $name = 'Renew Subscription';

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $subscription) {
            // Yeni bitmə tarixini əlavə olunan günlərlə artırırıq
            $newEndDate = Carbon::parse($subscription->end_date)->addDays($fields->extend_days);

            $subscription->update([
                'end_date' => $newEndDate,
                'is_active' => $fields->is_active, // `is_active` dəyərini action formdan alırıq
            ]);
        }

        return Action::message('Subscription successfully renewed!');
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
            Number::make('Extend Days', 'extend_days')
                ->rules('required', 'integer', 'min:1')
                ->default(30),

            Boolean::make('Activate Subscription', 'is_active')
                ->default(true)
        ];
    }
}
