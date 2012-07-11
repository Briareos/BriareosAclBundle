<?php

namespace Briareos\AclBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class AclPermissionRepository extends NestedTreeRepository
{
    public function getIndexedByName()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p');
        $qb->from($this->getClassName(), 'p', 'p.name');
        return $qb->getQuery()->execute();
    }
}