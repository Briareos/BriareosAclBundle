<?php

namespace Briareos\AclBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class RolePermissionsType extends AbstractType
{
    private $em;
    private $class;
    /**
     * @var \Briareos\AclBundle\Entity\AclPermissionRepository
     */
    private $repository;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $class;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'role_permissions';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function buildView(FormViewInterface $view, FormInterface $form, array $options)
    {
        $nodes = $this->repository
            ->createQueryBuilder('node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery()
            ->getArrayResult();
        $tree = $this->repository->buildTree($nodes);

        $view->addVars(array(
            'tree' => $tree,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => $this->class,
            'multiple' => true,
        ));
    }


}