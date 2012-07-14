<?php

namespace Briareos\AclBundle\Security\Handler;

use Sonata\AdminBundle\Security\Handler\RoleSecurityHandler;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AclSecurityHandler extends RoleSecurityHandler
{
    private $bundles;

    public function __construct(SecurityContextInterface $securityContext, array $superAdminRoles, KernelInterface $kernel)
    {
        parent::__construct($securityContext, $superAdminRoles);
        $this->bundles = $kernel->getBundles();
    }

    /**
     * Get a sprintf template to get the role
     *
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     *
     * @return string
     */
    function getBaseRole(AdminInterface $admin)
    {
        $bundleName = '';
        /** @var $bundle \Symfony\Component\HttpKernel\Bundle\BundleInterface */
        foreach ($this->bundles as $bundle) {
            if (strpos($admin->getClass(), $bundle->getNamespace()) === 0) {
                $bundleName = $bundle->getName();
                break;
            }
        }
        $entityName = strtolower(substr($admin->getClass(), strrpos($admin->getClass(), '\\') + 1));
        return strtolower(sprintf('admin.%s_%s.', $bundleName, $entityName)) . '%s';
    }
}