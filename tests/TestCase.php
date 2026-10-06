<?php

namespace Pharaonic\Slugify\Tests;

use Pharaonic\Slugify\Slugify;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Slugify::resetRules();
        Slugify::useTransliterator(null);
    }

    protected function tearDown(): void
    {
        Slugify::resetRules();
        Slugify::useTransliterator(null);

        parent::tearDown();
    }
}
