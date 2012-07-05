<?php

namespace Briareos\AclBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Briareos\AclBundle\DependencyInjection\Compiler\BuildPermissionsPass;

class BriareosAclBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BuildPermissionsPass());
    }
}
