<?php

namespace CAD\Bundle\HushBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessagesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('messageKey')
            ->add('sentTime')
            ->add('messageContent')
            ->add('targetUser')
            ->add('sendUser')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CAD\Bundle\HushBundle\Entity\Messages'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cad_bundle_hushbundle_messages';
    }
}
