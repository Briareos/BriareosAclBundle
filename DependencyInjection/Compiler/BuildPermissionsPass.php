<?php

namespace Briareos\AclBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildPermissionsPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('permission_builder')) {
            return;
        }

        /** @var $definition \Symfony\Component\DependencyInjection\DefinitionDecorator */
        $definition = $container->getDefinition('permission_builder');

        foreach ($container->findTaggedServiceIds('security.permission_container') as $id => $attributes) {
            $definition->addMethodCall('addPermissionContainer', array(new Reference($id)));
        }
    }

}