<?php

namespace Ens\JobeetBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Ens\JobeetBundle\Entity\Job;

class UserAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username')
            ->add('password', 'repeated', ['type' => 'password', 'required' => false,
                'first_options' => ['label' => 'Password (Leave it empty if do not want change it)'],
                'second_options' => ['label' => 'Repeat password', 'required' => false]])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username');
    }

    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username')
        ;
    }

    public function prePersist($newUser)
    {
        return $this->processPassword($newUser);
    }

    public function preUpdate($newUser)
    {
        return $this->processPassword($newUser);
    }

    protected function processPassword($newUser)
    {
        if (empty($newUser->getPassword())) {
            $em = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();
            $uow = $em->getUnitOfWork();
            $oldUser = $uow->getOriginalEntityData($newUser);
            $newUser->setPassword($oldUser->getPassword());
        } else {
            $encoder = $this->getConfigurationPool()->getContainer()->get('security.encoder_factory')->getEncoder($newUser);
            $newUser->setPassword($encoder->encodePassword($newUser->getPassword(), null));
        }
    }
}