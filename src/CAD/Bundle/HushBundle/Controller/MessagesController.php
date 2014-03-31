<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CAD\Bundle\HushBundle\Entity\Messages;
use CAD\Bundle\HushBundle\Form\MessagesType;

/**
 * Messages controller.
 *
 * @Route("/messages")
 */
class MessagesController extends Controller
{

    /**
     * Lists all Messages entities.
     *
     * @Route("/", name="messages")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('HushBundle:Messages')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Messages entity.
     *
     * @Route("/", name="messages_create")
     * @Method("POST")
     * @Template("HushBundle:Messages:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Messages();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('messages_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Messages entity.
    *
    * @param Messages $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Messages $entity)
    {
        $form = $this->createForm(new MessagesType(), $entity, array(
            'action' => $this->generateUrl('messages_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Messages entity.
     *
     * @Route("/new", name="messages_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Messages();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Get the latest messages sent to a user
     *
     * @Route("/{userId}/latest.json", name="get_latest_message")
     * @Method("GET")
     * @return JSON response of the latest id
     */
    public function getLatestMessages($userId)
    {

      $limit = 5;

      $em = $this->getDoctrine()->getManager();
      $entities = $em->getRepository('HushBundle:Messages')->findBy(
        array('sendUser' => $userId), 
        array('sentTime' => 'DESC'),
        $limit
      );

      $response = new Response();
      $response->setContent(json_encode($entities));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }

    /**
     * Finds and displays a Messages entity.
     *
     * @Route("/{id}", name="messages_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:Messages')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Messages entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Messages entity.
     *
     * @Route("/{id}/edit", name="messages_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:Messages')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Messages entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Messages entity.
    *
    * @param Messages $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Messages $entity)
    {
        $form = $this->createForm(new MessagesType(), $entity, array(
            'action' => $this->generateUrl('messages_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Messages entity.
     *
     * @Route("/{id}", name="messages_update")
     * @Method("PUT")
     * @Template("HushBundle:Messages:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:Messages')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Messages entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('messages_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Messages entity.
     *
     * @Route("/{id}", name="messages_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('HushBundle:Messages')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Messages entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('messages'));
    }

    /**
     * Creates a form to delete a Messages entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('messages_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

}
