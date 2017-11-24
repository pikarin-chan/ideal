<?php

namespace imc\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('username', 'filter_text')
            ->add('usernameCanonical', 'filter_text')
            ->add('email', 'filter_text')
            ->add('emailCanonical', 'filter_text')
            ->add('enabled', 'filter_boolean')
            ->add('salt', 'filter_text')
            ->add('password', 'filter_text')
            ->add('lastLogin', 'filter_date')
            ->add('locked', 'filter_boolean')
            ->add('expired', 'filter_boolean')
            ->add('expiresAt', 'filter_date')
            ->add('confirmationToken', 'filter_text')
            ->add('passwordRequestedAt', 'filter_date')
            ->add('roles', 'filter_text')
            ->add('credentialsExpired', 'filter_boolean')
            ->add('credentialsExpireAt', 'filter_date')
            ->add('loginCount', 'filter_number')
            ->add('firstLogin', 'filter_date')
            ->add('group', 'filter_entity', array('class' => 'imc\UserBundle\Entity\Group'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'imc\UserBundle\Entity\User',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_filter';
    }
}
