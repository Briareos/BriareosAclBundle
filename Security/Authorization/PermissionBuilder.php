<?php

namespace Briareos\AclBundle\Security\Authorization;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator;

class PermissionBuilder
{
    private $em;

    private $repository;

    private $validator;

    private $permissionContainers = array();

    public function __construct(EntityManager $em, $class, Validator $validator)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->validator = $validator;
    }

    public function addPermissionContainer(PermissionContainerInterface $permissionContainer)
    {
        $this->permissionContainers[] = $permissionContainer;
    }

    public function buildPermissions()
    {
        $permissions = array();
        /** @var $permissionContainer PermissionContainerInterface */
        foreach ($this->permissionContainers as $permissionContainer) {
            $permissions += $permissionContainer->getPermissions();
        }
        $aclPermissions = $this->buildContainerPermissions($permissions);
        foreach ($aclPermissions as $aclPermission) {
            $this->em->persist($aclPermission);
        }
        $this->em->flush();
    }

    public function buildContainerPermissions(array $permissions, $parent = null, $parentNames = array())
    {
        $generatedPermissions = array();
        $leftPermission = null;
        foreach ($permissions as $permissionName => $permissionConfiguration) {
            $aclPermissionClass = $this->repository->getClassName();
            /** @var $aclPermission \Briareos\AclBundle\Entity\AclPermission */
            $aclPermission = new $aclPermissionClass();
            $aclPermission->setName(implode('.', array_merge($parentNames, (array)$permissionName)));
            if (!empty($permissionConfiguration['description'])) {
                $aclPermission->setDescription($permissionConfiguration['description']);
            }
            if (!empty($permissionConfiguration['secure'])) {
                $aclPermission->setSecure($permissionConfiguration['secure']);
            }
            $generatedPermissions[$aclPermission->getName()] = $aclPermission;
            if (isset($permissionConfiguration['children']) && is_array($permissionConfiguration['children']) && count($permissionConfiguration['children'])) {
                $generatedPermissions += $this->buildContainerPermissions($permissionConfiguration['children'], $aclPermission, array_merge($parentNames, (array)$permissionName));
            }

            if ($parent !== null) {
                $aclPermission->setParent($parent);
            }

            // To keep it ordered.
            if ($leftPermission !== null) {
                $aclPermission->setLft($leftPermission);
            }
            $leftPermission = $aclPermission;
        }
        return $generatedPermissions;
    }

    public function isOptional()
    {
        return false;
    }
}