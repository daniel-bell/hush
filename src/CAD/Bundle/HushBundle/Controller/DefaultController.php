<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CAD\Bundle\HushBundle\Entity\Users;
use CAD\Bundle\HushBundle\Form\UsersType;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect($this->generateUrl('chat'));
        }

    	$entity = new Users();
        $register_form   = $this->createRegisterForm($entity);

        return $this->render('HushBundle:Default:index.html.twig', array('register_form' => $register_form->createView()));
    }

    private function createRegisterForm(Users $entity)
    {
        $form = $this->createForm(new UsersType(), $entity, array(
            'action' => $this->generateUrl('users_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Register'));

        return $form;
    }
}
