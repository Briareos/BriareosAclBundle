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

    private $repository;

    private $userPermissions = array();

    private $rolePermissions = array();

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository($class);
    }

    public function getPermissions(TokenInterface $token)
    {
        /** @var $user AclSubjectInterface */
        $user = $token->getUser();
        $userId = 0;
        if ($user instanceof AclSubjectInterface) {
            $userId = $user->getId();
        }
        if (!isset($this->userPermissions[$userId])) {
            $this->userPermissions[$userId] = array();
            if ($userId) {
                /** @var $userRole AclRole */
                foreach ($user->getUserRoles() as $userRole) {
                    $userRoles[$userRole->getId()] = $userRole;
                }
                /** @var $authenticatedUserRole AclRole */
                $authenticatedUserRole = $this->em->getRepository('Briareos\AclBundle\Entity\AclRole')->findOneBy(array('internalRole' => Role::AUTHENTICATED_USER));
                $userRoles[$authenticatedUserRole->getId()] = $authenticatedUserRole;
            } else {
                /** @var $anonymousRole AclRole */
                $anonymousRole = $this->em->getRepository('Briareos\AclBundle\Entity\AclRole')->findOneBy(array('internalRole' => Role::ANONYMOUS_USER));
                $userRoles = array($anonymousRole->getId() => $anonymousRole);
            }
            foreach ($userRoles as $userRole) {
                $this->userPermissions[$userId] += $this->getRolePermissions($userRole);
            }
        }
        return $this->userPermissions[$userId];
    }

    public function getRolePermissions(AclRole $role)
    {
        if (!isset($this->rolePermissions[$role->getId()])) {
            $this->rolePermissions[$role->getId()] = array();
            if ($role->getInternalRole() === Role::ADMINISTRATOR) {
                $this->rolePermissions[$role->getId()]['ROLE_SUPER_ADMIN'] = true;
            }
            foreach ($role->getPermissions() as $permission) {
                $this->rolePermissions[$role->getId()][$permission->getName()] = true;
            }
        }
        return $this->rolePermissions[$role->getId()];
    }
}