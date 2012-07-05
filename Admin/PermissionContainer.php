<?php

namespace Briareos\AclBundle\Admin;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

class PermissionContainer implements PermissionContainerInterface
{
    public function getPermissions()
    {
        return array(
            'acl_role' => array(
                'children' => array(
                    'list' => array(),
                    'create' => array(),
                    'edit' => array(
                        'children' => array(
                            'description' => array(),
                            'permissions' => array(
                                'secure' => true,
                            ),
                        ),
                    ),
                    'delete' => array(),
                ),
            ),
        );
    }

}