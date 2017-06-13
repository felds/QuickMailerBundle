<?php

namespace Felds/QuickMailerBundle/Model;

interface QuickMailableInterface
{
    public function getName(): string
    public function getEmail(): string
}
