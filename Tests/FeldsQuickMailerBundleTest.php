<?php

use Felds\QuickMailerBundle\FeldsQuickMailerBundle;
use PHPUnit\Framework\TestCase;

class FeldsQuickMailerBundleTest extends TestCase
{
    /**
     * @var FeldsQuickMailerBundle
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new FeldsQuickMailerBundle();
    }

    /**
     * @test
     */
    function test_it_is_instantiable()
    {
        $this->assertInstanceOf(FeldsQuickMailerBundle::class, $this->sut);
    }
}
