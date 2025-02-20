<?php

namespace App\Nova;

use App\Nova\Actions\AssignPackageToAllAgents;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Actions\RenewSubscription;

class Subscription extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Subscription>
     */
    public static $model = \App\Models\Subscription::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User', 'user', User::class)
                ->nullable()
                ->searchable()
                ->hideFromIndex(),

            BelongsTo::make('Agency', 'agency', Agency::class)
                ->nullable()
                ->searchable()
                ->hideFromIndex(),

            BelongsTo::make('Package', 'package', Package::class)
                ->sortable()
                ->searchable(),

            DateTime::make('Start Date', 'start_date')
                ->sortable(),

            DateTime::make('End Date', 'end_date')
                ->sortable(),

            Boolean::make('Active', 'is_active'),


        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            new RenewSubscription(),
        ];
    }
}
