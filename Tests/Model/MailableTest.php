<?php

use Felds\QuickMailerBundle\Model\Mailable;
use PHPUnit\Framework\TestCase;

class MailableTest extends TestCase
{
    /** @var Mailable */
    private $sut;

    protected function setUp()
    {
        $this->sut = new Mailable('Name of the Person', 'email@example.com');
    }

    function test_get_name()
    {
        $this->assertSame('Name of the Person', $this->sut->getName());
    }

    function test_get_email()
    {
        $this->assertSame('email@example.com', $this->sut->getEmail());
    }
}
