<?php

namespace BlessingDube\Recurring\Test;

use BlessingDube\Recurring\Contracts\IsRecurring;
use BlessingDube\Recurring\Traits\RecurringTrait;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;

class RecurringTest extends TestCase
{
    public function test_can_instantiate_recurring_model()
    {
        $instance = new TestModel();

        $this->assertTrue($instance instanceof IsRecurring);
    }

    public function test_model_has_recurring_relation()
    {
        $instance = new TestModel();

        $this->assertTrue(method_exists($instance, 'recurring'));
    }
}

class TestModel extends Model implements IsRecurring
{
    use RecurringTrait;

    public function getRecurringOptions()
    {
        return [
            'start_date' => 'starts_at',
            'end_date' => 'ends_at',
        ];
    }
}
