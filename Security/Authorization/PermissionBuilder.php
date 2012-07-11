<?php

namespace Briareos\AclBundle\Security\Authorization;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator;

class PermissionBuilder
{
    private $em;
    /**
     * @var \Briareos\AclBundle\Entity\AclPermissionRepository
     */
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
            $permissions = array_merge_recursive($permissions, $permissionContainer->getPermissions());
        }
        $aclPermissions = $this->buildContainerPermissions($permissions);
        $existingAclPermissions = $this->repository->getIndexedByName();
        $newAclPermissions = array_diff_key($aclPermissions, $existingAclPermissions);
        $deprecatedAclPermissions = array_diff_key($existingAclPermissions, $aclPermissions);
        /** @var $newAclPermission \Briareos\AclBundle\Entity\AclPermission */
        foreach ($newAclPermissions as $newAclPermission) {
            if (($newAclPermission->getParent() !== null) && isset($existingAclPermissions[$newAclPermission->getParent()->getName()])) {
                $newAclPermission->setParent($existingAclPermissions[$newAclPermission->getParent()->getName()]);
            }
            if (($newAclPermission->getLft() !== null) && isset($existingAclPermissions[$newAclPermission->getLft()->getName()])) {
                $newAclPermission->setParent($existingAclPermissions[$newAclPermission->getParent()->getName()]);
            }
            $this->em->persist($newAclPermission);
        }
        /** @var $deprecatedAclPermission \Briareos\AclBundle\Entity\AclPermission */
        foreach ($deprecatedAclPermissions as $deprecatedAclPermission) {
            $this->em->remove($deprecatedAclPermission);
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
            if (isset($permissionConfiguration['__children']) && is_array($permissionConfiguration['__children']) && count($permissionConfiguration['__children'])) {
                $generatedPermissions += $this->buildContainerPermissions($permissionConfiguration['__children'], $aclPermission, array_merge($parentNames, (array)$permissionName));
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