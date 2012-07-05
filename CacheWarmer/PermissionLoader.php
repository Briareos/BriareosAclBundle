<?php

namespace Briareos\AclBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Briareos\AclBundle\Security\Authorization\PermissionBuilder;

class PermissionLoader implements CacheWarmerInterface
{
    private $permissionBuilder;

    public function __construct(PermissionBuilder $permissionBuilder)
    {
        $this->permissionBuilder = $permissionBuilder;
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return Boolean true if the warmer is optional, false otherwise
     */
    function isOptional()
    {
        return false;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    function warmUp($cacheDir)
    {
        /** @var $permissionBuilder \Briareos\AclBundle\Security\Authorization\PermissionBuilder */
        $this->permissionBuilder->buildPermissions();
    }

}