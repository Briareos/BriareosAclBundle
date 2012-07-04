<?php

namespace Briareos\AclBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Briareos\AclBundle\Entity\AclRole
 */
class AclRole
{
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
     * @var integer $left
     */
    private $left;

    /**
     * @var integer $right
     */
    private $right;

    /**
     * @var integer $root
     */
    private $root;

    /**
     * @var integer $level
     */
    private $level;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $children;

    /**
     * @var Briareos\AclBundle\Entity\AclRole
     */
    private $parent;

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
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set left
     *
     * @param integer $left
     * @return AclRole
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }

    /**
     * Get left
     *
     * @return integer 
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set right
     *
     * @param integer $right
     * @return AclRole
     */
    public function setRight($right)
    {
        $this->right = $right;
        return $this;
    }

    /**
     * Get right
     *
     * @return integer 
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return AclRole
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return AclRole
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Add children
     *
     * @param Briareos\AclBundle\Entity\AclRole $children
     * @return AclRole
     */
    public function addAclRole(\Briareos\AclBundle\Entity\AclRole $children)
    {
        $this->children[] = $children;
        return $this;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param Briareos\AclBundle\Entity\AclRole $parent
     * @return AclRole
     */
    public function setParent(\Briareos\AclBundle\Entity\AclRole $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return Briareos\AclBundle\Entity\AclRole 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add permissions
     *
     * @param Briareos\AclBundle\Entity\AclPermission $permissions
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
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add subjects
     *
     * @param App\UserBundle\Entity\User $subjects
     * @return AclRole
     */
    public function addUser(\App\UserBundle\Entity\User $subjects)
    {
        $this->subjects[] = $subjects;
        return $this;
    }

    /**
     * Get subjects
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubjects()
    {
        return $this->subjects;
    }
}