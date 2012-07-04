<?php

namespace Briareos\AclBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DisableRoleHierarchyVoterPass implements CompilerPassInterface
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
        if (!$def = $container->getDefinition('security.access.decision_manager')) {
            return;
        }
        $args = $def->getArguments();
        $changed = false;
        foreach ($args[0] as $index => $manager) {
            if($manager->__toString() === 'security.access.role_hierarchy_voter') {
                unset($args[0][$index]);
                $changed = true;
            }
        }
        if($changed) {
            $args[0] = array_values($args[0]);
            $def->setArguments($args);
        }
    }

}