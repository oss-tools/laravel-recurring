<?php

namespace Webfactor\Package\Test;

use PHPUnit\Framework\TestCase;
use Webfactor\Package\PackageClass;

class PackageTest extends TestCase
{
    /** @test */
    public function returns_hello_world_string()
    {
        $package = new PackageClass();

        $this->assertEquals('Hello World', $package->helloWorld());
    }
}
