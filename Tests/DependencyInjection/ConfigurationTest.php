<?php

use Felds\QuickMailerBundle\DependencyInjection\QuickMailerExtension;
use Felds\QuickMailerBundle\Model\MailableInterface;
use Felds\QuickMailerBundle\QuickMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class QuickMailerExtensionTest extends TestCase
{
    function test_full_config()
    {
        $container = $this->loadContainerFromFile('full');

        $this->assertInstanceOf(MailableInterface::class, $container->get('quickmailer.config.from'));
        $this->assertInstanceOf(MailableInterface::class, $container->get('quickmailer.config.reply_to'));
        // @TODO use a NullMailer
        // $this->assertInstanceOf(QuickMailer::class, $container->get('quickmailer.welcome'));
    }

    /**
     * @param string $file
     * @param array  $services
     * @param bool   $skipEnvVars
     * @return ContainerBuilder
     */
    private function loadContainerFromFile($file, array $services = array(), $skipEnvVars = false)
    {
        $container = new ContainerBuilder();

        if ($skipEnvVars && !method_exists($container, 'resolveEnvPlaceholders')) {
            $this->markTestSkipped('Runtime environment variables has been introduced in the Dependency Injection version 3.2.');
        }

        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.cache_dir', '/tmp');

        foreach ($services as $id => $service) {
            $container->set($id, $service);
        }

        $container->registerExtension(new QuickMailerExtension());
        $locator = new FileLocator(__DIR__.'/Fixtures/config/');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file.'.yml');
        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->compile();

        return $container;
    }

}
