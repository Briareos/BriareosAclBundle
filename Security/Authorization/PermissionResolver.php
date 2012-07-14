<?php

namespace Briareos\AclBundle\Security\Authorization;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Briareos\AclBundle\Entity\AclSubjectInterface;
use Briareos\AclBundle\Entity\AclRole;
use Briareos\AclBundle\Entity\AclPermission;

class PermissionResolver
{
    private $em;

    /**
     * @var \Briareos\AclBundle\Entity\AclRoleRepository
     */
    private $repository;

    private $subjectPermissions = array();

    private $rolePermissions = array();

    private $superAdminRole;

    public function __construct(EntityManager $em, $class, $superAdminRole)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository($class);
        $this->superAdminRole = $superAdminRole;
    }

    public function getPermissions(TokenInterface $token)
    {
        /** @var $subject AclSubjectInterface */
        $subject = $token->getUser();
        $subjectId = 0;
        if ($subject instanceof AclSubjectInterface) {
            $subjectId = $subject->getId();
        }
        if (!isset($this->subjectPermissions[$subjectId])) {
            $this->subjectPermissions[$subjectId] = array();
            if ($subjectId) {
                /** @var $subjectRole AclRole */
                foreach ($subject->getAclRoles() as $subjectRole) {
                    $subjectRoles[$subjectRole->getId()] = $subjectRole;
                }
                /** @var $authenticatedRole AclRole */
                $authenticatedRole = $this->repository->findOneBy(array('internalRole' => AclRole::AUTHENTICATED_USER));
                $subjectRoles[$authenticatedRole->getId()] = $authenticatedRole;
            } else {
                /** @var $anonymousRole AclRole */
                $anonymousRole = $this->repository->findOneBy(array('internalRole' => AclRole::ANONYMOUS_USER));
                $subjectRoles = array($anonymousRole->getId() => $anonymousRole);
            }
            foreach ($subjectRoles as $subjectRole) {
                $this->subjectPermissions[$subjectId] += $this->getRolePermissions($subjectRole);
            }
        }
        return $this->subjectPermissions[$subjectId];
    }

    public function getRolePermissions(AclRole $role)
    {
        if (!isset($this->rolePermissions[$role->getId()])) {
            $this->rolePermissions[$role->getId()] = array();
            if ($role->getInternalRole() === AclRole::ADMINISTRATOR) {
                $this->rolePermissions[$role->getId()][$this->superAdminRole] = true;
            }
            /** @var $permission AclPermission */
            foreach ($role->getPermissions() as $permission) {
                $this->rolePermissions[$role->getId()][$permission->getName()] = true;
            }
        }
        return $this->rolePermissions[$role->getId()];
    }
}