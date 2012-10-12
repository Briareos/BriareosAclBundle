<?php

namespace Briareos\AclBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class PermissionsRebuildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('briareos-acl:permissions:rebuild')
            ->setDefinition(array())
            ->setDescription('Rebuild permissions')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command rebuilds the permissions to reflect
the application's permissions
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Rebuilding the permissions');
        /** @var $permissionBuilder \Briareos\AclBundle\Security\Authorization\PermissionBuilder */
        $permissionBuilder = $this->getContainer()->get('briareos_acl.permission_builder');
        $permissionBuilder->buildPermissions();
    }


}