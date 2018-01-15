<?php

use Felds\QuickMailerBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationTest extends TestCase
{
    /**
     * @param string $file
     * @param string $type
     * @param array  $services
     * @param bool   $skipEnvVars
     *
     * @return ContainerBuilder
     */
    private function loadContainerFromFile($file, $type, array $services = array(), $skipEnvVars = false)
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
        $container->registerExtension(new SwiftmailerExtension());
        $locator = new FileLocator(__DIR__.'/Fixtures/config/'.$type);
        switch ($type) {
            case 'xml':
                $loader = new XmlFileLoader($container, $locator);
                break;
            case 'yml':
                $loader = new YamlFileLoader($container, $locator);
                break;
            case 'php':
                $loader = new PhpFileLoader($container, $locator);
                break;
        }
        $loader->load($file.'.'.$type);
        $container->getCompilerPassConfig()->setOptimizationPasses(array(
            class_exists(ResolveChildDefinitionsPass::class) ? new ResolveChildDefinitionsPass() : new ResolveDefinitionTemplatesPass(),
        ));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
        return $container;
    }

}
