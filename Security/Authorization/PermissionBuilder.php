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
        $permissionNames = array();
        $permissionDomains = array();
        /** @var $permissionContainer PermissionContainerInterface */
        foreach ($this->permissionContainers as $permissionContainer) {
            $permissionDomains += $permissionContainer->getPermissions();
            foreach ($permissionDomains as $permissionDomain => $permissions) {
                foreach ($permissions as $index => $permission) {
                    if (is_numeric($index)) {
                        $permissionNames[$permission] = $permissionDomain;
                    } else {
                        $permissionNames[$index] = $permissionDomain;
                    }
                }
            }
        }

        $qb = $this->em->createQueryBuilder();
        $qb->from($this->repository->getClassName(), 'p', 'p.name');
        $qb->select('p');
        $permissionEntities = $qb->getQuery()->execute();

        foreach ($permissionEntities as $permissionName => $permissionEntity) {
            if (!isset($permissionNames[$permissionName])) {
                $this->em->remove($permissionEntity);
            }
        }

        foreach ($permissionNames as $permissionName => $permissionDomain) {
            if (!isset($permissionEntities[$permissionName])) {
                $newPermission = new Permission();
                $newPermission->setName($permissionName);
                $newPermission->setDomain($permissionDomain);
                if (isset($permissionDomains[$permissionDomain][$permissionName])) {
                    $permissionSecure = $permissionDomains[$permissionDomain][$permissionName];
                } else {
                    $permissionSecure = false;
                }
                $newPermission->setSecure($permissionSecure);
                /** @var $violations \Symfony\Component\Validator\ConstraintViolationList */
                $violations = $this->validator->validate($newPermission);
                if ($violations->count()) {
                    throw new \Exception(sprintf('Violation during permission rebuilding: %s', $violations->getIterator()->current()));
                }
                $this->em->persist($newPermission);
            }
        }

        $this->em->flush();
    }

    public function isOptional()
    {
        return false;
    }
}