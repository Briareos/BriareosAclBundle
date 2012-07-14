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
                    'briareosaclbundle_aclrole' => array(
                        '__children' => array(
                            'create' => array(),
                            'list' => array(),
                            'edit' => array(
                                '__children' => array(
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