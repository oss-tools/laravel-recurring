<?php

namespace OSSTools\Recurring\Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OSSTools\Recurring\Contracts\IsRecurring;
use OSSTools\Recurring\Traits\RecurringTrait;
use PHPUnit\Framework\TestCase;

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
    use SoftDeletes;

    public function getRecurringOptions()
    {
        return [
            'start_date' => 'starts_at',
            'end_date' => 'ends_at',
        ];
    }
}
