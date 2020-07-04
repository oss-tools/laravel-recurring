<?php

namespace BlessingDube\Recurring\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Recurring
 * @package BlessingDube\Recurring\Models
 */
class Recurring extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $dates = ['start_date', 'end_date'];


    /**
     * @var string[]
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d H:i:s',
        'end_date' => 'date:Y-m-d H:i:s',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function recurring()
    {
        return $this->morphTo('recurring');
    }

    /**
     * @param $query
     * @param  array  $options
     * @return array
     */
    public function scopeAsParent($query, $options = [])
    {
        if ((count($options) === 1)) {
            $string = 'recurring.'.$options[0];

            $query = $query->with($string);
        } elseif (count($options) > 1) {
            $string = 'recurring';

            foreach ($options as $option) {
                $string .= '.'.$option;
            }

            $query = $query->with($string);
        }

        $model = $query->first();

        return $this->formatAsParent($model->recurring, $model);
    }

    /**
     * @param  Model|null  $parent
     * @param  Recurring|null  $recurring
     * @return array
     */
    public function formatAsParent(Model $parent = null, Recurring $recurring = null)
    {
        if (!$parent || !$recurring) {
            $parent = $this->recurring()->with([
                'recurring' => function ($r) {
                    $r->where('recurrings.id', self::getKey());
                }
            ])->first();

            $recurring = $parent->recurring[0];
        }

        $parent->start_date = $recurring->start_date;
        $parent->end_date = $recurring->end_date;

        $parent->created_at = $recurring->created_at;
        $parent->updated_at = $recurring->updated_at;

        return $parent->toArray();
    }
}
