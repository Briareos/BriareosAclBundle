<?php

namespace Briareos\AclBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Briareos\AclBundle\Security\Authorization\PermissionResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionVoter implements VoterInterface
{
    private $roleResolver;

    public function __construct(PermissionResolver $roleResolver)
    {
        $this->roleResolver = $roleResolver;
    }

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return Boolean true if this Voter supports the attribute, false otherwise
     */
    function supportsAttribute($attribute)
    {
        $ignoreAttributes = array(
            'IS_AUTHENTICATED_ANONYMOUSLY',
            'IS_AUTHENTICATED_REMEMBERED',
            'IS_AUTHENTICATED_FULLY',
        );
        return !in_array($attribute, $ignoreAttributes);
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return true if this Voter can process the class
     */
    function supportsClass($class)
    {
        return true;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object         $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return integer either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute)) {
                $roles = $this->roleResolver->getPermissions($token);
                if (isset($roles[$attribute])) {
                    return self::ACCESS_GRANTED;
                }
                return self::ACCESS_DENIED;
            }
        }
        return self::ACCESS_ABSTAIN;
    }


}