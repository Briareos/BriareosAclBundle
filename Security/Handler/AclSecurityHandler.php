<?php

namespace Briareos\AclBundle\Security\Handler;

use Sonata\AdminBundle\Security\Handler\RoleSecurityHandler;
use Sonata\AdminBundle\Admin\AdminInterface;

class AclSecurityHandler extends RoleSecurityHandler
{
    /**
     * Get a sprintf template to get the role
     *
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     *
     * @return string
     */
    function getBaseRole(AdminInterface $admin)
    {
        return 'admin.%s';
    }
}