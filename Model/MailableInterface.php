<?php

namespace Felds\QuickMailerBundle\Model;

interface MailableInterface
{
    public function getName(): string;
    public function getEmail(): string;
}
