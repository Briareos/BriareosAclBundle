<?php

namespace Briareos\AclBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Briareos\AclBundle\Entity\AclRole
 */
class AclRole
{
    const ADMINISTRATOR = 1;
    const AUTHENTICATED_USER = 2;
    const ANONYMOUS_USER = 3;
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var integer $internalRole
     */
    private $internalRole;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $permissions;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $subjects;

    public function __construct()
    {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subjects = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AclRole
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AclRole
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set internalRole
     *
     * @param integer $internalRole
     * @return AclRole
     */
    public function setInternalRole($internalRole)
    {
        $this->internalRole = $internalRole;
        return $this;
    }

    /**
     * Get internalRole
     *
     * @return integer 
     */
    public function getInternalRole()
    {
        return $this->internalRole;
    }

    /**
     * Add permissions
     *
     * @param \Briareos\AclBundle\Entity\AclPermission $permissions
     * @return AclRole
     */
    public function addAclPermission(\Briareos\AclBundle\Entity\AclPermission $permissions)
    {
        $this->permissions[] = $permissions;
        return $this;
    }

    /**
     * Get permissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get subjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Add subjects
     *
     * @param AclSubjectInterface $subject
     * @return AclRole
     */
    public function addSubject(AclSubjectInterface $subject)
    {
        $this->subjects[] = $subject;
        return $this;
    }

    /**
     * Remove subjects
     *
     * @param AclSubjectInterface $subjects
     * @return AclRole
     */
    public function removeSubject(AclSubjectInterface $subjects)
    {
        $this->subjects->removeElement($subjects);
        return $this;
    }

    /**
     * Add permissions
     *
     * @param AclPermission $permissions
     * @return AclRole
     */
    public function addPermission(\Briareos\AclBundle\Entity\AclPermission $permissions)
    {
        $this->permissions[] = $permissions;
        return $this;
    }

    /**
     * Remove permissions
     *
     * @param \Briareos\AclBundle\Entity\AclPermission $permissions
     */
    public function removePermission(\Briareos\AclBundle\Entity\AclPermission $permissions)
    {
        $this->permissions->removeElement($permissions);
    }
}