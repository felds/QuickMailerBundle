<?php

namespace Felds\QuickMailerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FeldsQuickMailerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DependencyInjection\QuickMailerExtension();
    }
}
