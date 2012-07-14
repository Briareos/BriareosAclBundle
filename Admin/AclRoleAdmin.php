<?php

namespace Briareos\AclBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AclRoleAdmin extends Admin implements ContainerAwareInterface
{
    private $container;

    //protected $baseRouteName = 'role_admin';

    //protected $baseRoutePattern = 'role';

    protected $formOptions = array(
        'validation_groups' => array('admin'),
    );

    public function configureFormFields(FormMapper $form)
    {
        $form
            ->with('admin.role.role')
            ->add('name', null, array(
            'label' => 'admin.form.role.name',
        ));
        if ($this->isGranted('edit.permissions')) {
            $form->add('permissions', 'role_permissions', array(
                'label' => 'admin.form.role.permissions',
                'property' => 'name',
                'translation_domain' => 'permissions',
                'group_by' => 'domain',
                'expanded' => true,
                'required' => false,
            ));
        }
        $form->end();
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name');
    }

    public function configureShowFields(ShowMapper $filter)
    {
        $filter
            ->add('name');
    }

    public function toString($object)
    {
        /** @var $object \Briareos\AclBundle\Entity\AclRole */
        return $object->getName();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


}