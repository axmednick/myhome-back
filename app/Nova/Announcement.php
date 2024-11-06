<?php

namespace App\Nova;

use App\Models\AnnouncementMetroStation;
use App\Models\AnnouncementSupply;

use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Announcement extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Announcement>
     */
    public static $model = \App\Models\Announcement::class;

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
            Boolean::make('Status'),
/*

            Select::make('Announcement Type', 'announcement_type_id')
                ->options(\App\Models\AnnouncementType::all()->pluck('name', 'id'))
                ->displayUsingLabels()
                ->sortable(),

            Select::make('Property Type', 'property_type_id')
                ->options(\App\Models\PropertyType::pluck('name', 'id'))
                ->displayUsingLabels()
                ->sortable(),

            Select::make('Apartment Type', 'apartment_type_id')
                ->options(\App\Models\ApartmentType::pluck('name', 'id'))
                ->displayUsingLabels()
                ->nullable(),

            Number::make('Area (sqm)', 'area')
                ->sortable()
                ->nullable(),

            Number::make('House Area (sqm)', 'house_area')
                ->nullable()
                ->sortable(),

            Number::make('Room Count', 'room_count')
                ->nullable()
                ->sortable(),

            Number::make('Floor', 'floor')
                ->nullable()
                ->sortable(),

            Number::make('Floor Count', 'floor_count')
                ->nullable()
                ->sortable(),

            Textarea::make('Description')
                ->nullable(),

            Number::make('Price')
                ->sortable(),

            Boolean::make('Is Repaired', 'is_repaired')
                ->sortable(),

            Select::make('Document', 'document_id')
                ->options(\App\Models\PropertyDocument::pluck('name', 'id'))
                ->displayUsingLabels()
                ->nullable(),

            Boolean::make('Credit Possible', 'credit_possible')
                ->sortable(),

            Boolean::make('In Credit', 'in_credit')
                ->sortable(),

            BelongsTo::make('User', 'user', User::class)
                ->searchable(),

            Text::make('Latitude', 'lat')
                ->nullable(),

            Text::make('Longitude', 'lng')
                ->nullable(),

            Text::make('Address', 'address')
                ->nullable(),

            BelongsToMany::make('Client Types for Rent', 'clientTypesForRent', ClientTypeForRent::class),



            Image::make('Media', 'media')->disk('public')*/
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
