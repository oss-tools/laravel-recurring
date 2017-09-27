<?php

namespace Webfactor\Package;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Webfactor\Package\PackageClass
 */
class PackageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'package';
    }
}
