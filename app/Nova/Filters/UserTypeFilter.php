<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class UserTypeFilter extends Filter
{
    /**
     * The filter's name.
     *
     * @var string
     */
    public $name = 'User Type';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('user_type', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'User' => 'user',
            'Agent' => 'agent',
        ];
    }
}
