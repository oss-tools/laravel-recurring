<?php

namespace BlessingDube\Recurring\Models;

use Illuminate\Database\Eloquent\Model;

class Recurring extends Model
{
    protected $guarded = [];

    protected $dates = ['start_date', 'end_date'];

    public function recurring()
    {
        return $this->morphTo('recurring');
    }
}