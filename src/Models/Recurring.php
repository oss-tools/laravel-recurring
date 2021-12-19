<?php

namespace OSSTools\Recurring\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Recurring.
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
        'start_date' => 'date:Y-m-d H:i',
        'end_date' => 'date:Y-m-d H:i',
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
            $query = $query->with($options[0]);
        } elseif (count($options) > 1) {
            $query = $query->with($options);
        }

        $model = $query->first();

        return $this->formatAsParent($model->recurring, $model);
    }

    /**
     * @param $query
     * @param  array  $options
     * @return mixed
     */
    public function scopeGetAsParent($query, $options = [])
    {
        if ((count($options) === 1)) {
            $query = $query->with($options[0]);
        } elseif (count($options) > 1) {
            $query = $query->with($options);
        }

        $models = $query->get();

        return $models->each(function ($model) {
            return $model->formatAsParent($model->recurring, $model);
        });
    }

    /**
     * @param  Model|null  $parent
     * @param  Recurring|null  $recurring
     * @return array
     */
    public function formatAsParent(Model $parent = null, self $recurring = null)
    {
        if (! $parent || ! $recurring) {
            $parent = $this->recurring()->with([
                'recurring' => function ($r) {
                    $r->where('recurrings.id', self::getKey());
                },
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
