<?php
namespace Povs\ListerTwigBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class PovsListerTwigExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
        $configResolverDef = $container->getDefinition('.povs_lister.twig.configuration_resolver');
        $configResolverDef->setArgument(0, $configs);
    }
}