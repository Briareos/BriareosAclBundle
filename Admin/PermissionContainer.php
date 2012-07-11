<?php

namespace Briareos\AclBundle\Admin;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

class PermissionContainer implements PermissionContainerInterface
{
    public function getPermissions()
    {
        return array(
            'admin' => array(
                '__children' => array(
                    'acl_role' => array(
                        '__children' => array(
                            'list' => array(),
                            'create' => array(),
                            'edit' => array(
                                '__children' => array(
                                    'description' => array(),
                                    'permissions' => array(
                                        'secure' => true,
                                    ),
                                ),
                            ),
                            'delete' => array(),
                        ),
                    ),
                ),
            ),
        );
    }

}