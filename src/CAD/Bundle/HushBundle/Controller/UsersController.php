<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CAD\Bundle\HushBundle\Entity\Users;
use CAD\Bundle\HushBundle\Form\UsersType;

/**
 * Users controller.
 *
 * @Route("/users")
 */
class UsersController extends Controller
{
    /**
     * Creates a new Users entity.
     *
     * @Route("/new", name="users_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $flash = $this->get('braincrafted_bootstrap.flash');

        $entity = new Users();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Get the password encoder from the security.yml
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);

            // Generate a random salt and hash the password with it
            $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $password = $encoder->encodePassword($entity->getPassword(), $salt);

            // Replace plaintext password and blank salt
            $entity->setPassword($password);
            $entity->setSalt($salt);

            $em->persist($entity);
            $em->flush();

            $flash->success('Your account has been created, you many now log in.');

            return $this->redirect($this->generateUrl('hush_homepage'));
        }

        $validator = $this->get('validator');
        $validation_errors = $validator->validate($entity);

        if (count($validation_errors) > 0) {
            foreach ($validation_errors as $val_error) {
                $flash->error($val_error->getMessage());;
            }
        }

        return $this->redirect($this->generateUrl('hush_homepage'));
    }

    /**
     * Creates a form to create a Users entity.
     *
     * @param Users $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Users $entity)
    {
        $form = $this->createForm(new UsersType(), $entity, array(
            'action' => $this->generateUrl('users_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Register', 'attr' => array('class' => 'flat-button',)));

        return $form;
    }

    /**
     * Get the current user as a JSON response
     *
     * @Route("/me", name="users_me")
     * @Method("GET")
     * @Template()
     */
    public function meAction()
    {
        $me = $this->get('security.context')->getToken()->getUser()->getId();
        $response = null;

        if ($me != null) {
            $response = new JsonResponse();
            $response->setData($me);
        }

        return $response;
    }
}
