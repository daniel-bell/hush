<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use CAD\Bundle\HushBundle\Entity\Users;
use CAD\Bundle\HushBundle\Form\UsersType;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        if($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect($this->generateUrl('chat'));
        }

    	$entity = new Users();
        $register_form   = $this->createRegisterForm($entity);

        return $this->render('HushBundle:Default:index.html.twig', array(
            'register_form' => $register_form->createView(),
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'=> $error));
    }

    private function createRegisterForm(Users $entity)
    {
        $form = $this->createForm(new UsersType(), $entity, array(
            'action' => $this->generateUrl('users_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Register', 'attr' => array('class' => 'flat-button flat-button-green')));

        return $form;
    }
}
