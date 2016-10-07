<?php

namespace Chteuchteu\CorporateVCardsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class CorporateVCardsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter($this->getAlias().'.config', $config['config']);
        $container->setParameter($this->getAlias().'.default', $config['default']);
        $container->setParameter($this->getAlias().'.profiles', $config['profiles']);
    }
}
