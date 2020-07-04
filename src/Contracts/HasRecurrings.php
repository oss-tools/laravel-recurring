<?php

namespace BlessingDube\Recurring\Contracts;

/**
 * Interface HasRecurrings
 * @package BlessingDube\Recurring\Contracts
 */
interface HasRecurrings
{
    /**
     * @return mixed
     */
    public function getRecurringOptions();
}
