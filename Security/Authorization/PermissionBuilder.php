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
        $deprecatedAclPermissions = array_diff_key($existingAclPermissions, $aclPermissions);
        /** @var $deprecatedAclPermission \Briareos\AclBundle\Entity\AclPermission */
        foreach ($deprecatedAclPermissions as $deprecatedAclPermission) {
            $this->em->remove($deprecatedAclPermission);
        }
        foreach ($aclPermissions as $aclPermission) {
            $this->em->persist($aclPermission);
        }
        $this->em->flush();
        $rootAclPermissions = $this->repository->getRootNodes();
        foreach ($rootAclPermissions as $rootAclPermission) {
            $this->repository->reorder($rootAclPermission, 'weight');
            $this->em->persist($rootAclPermission);
        }
        $this->em->flush();
    }

    public function buildContainerPermissions(array $permissions, $parent = null, $parentNames = array())
    {
        $generatedPermissions = array();
        $weight = 0;
        foreach ($permissions as $permissionName => $permissionConfiguration) {
            $name = implode('.', array_merge($parentNames, (array)$permissionName));
            $aclPermissionClass = $this->repository->getClassName();
            /** @var $aclPermission \Briareos\AclBundle\Entity\AclPermission */
            $aclPermission = $this->repository->findOneBy(array('name' => $name));
            if (!$aclPermission) {
                $aclPermission = new $aclPermissionClass();
                $aclPermission->setName($name);
            }
            if (isset($permissionConfiguration['weight'])) {
                $aclPermission->setWeight($permissionConfiguration['weight']);
            } else {
                $weight += 10;
                $aclPermission->setWeight($weight);
            }
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
        }
        return $generatedPermissions;
    }

    public function isOptional()
    {
        return false;
    }
}