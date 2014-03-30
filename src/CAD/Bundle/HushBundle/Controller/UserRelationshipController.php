<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CAD\Bundle\HushBundle\Entity\UserRelationship;
use CAD\Bundle\HushBundle\Form\UserRelationshipType;

/**
 * UserRelationship controller.
 *
 * @Route("/user_relationship")
 */
class UserRelationshipController extends Controller
{

    /**
     * Lists all UserRelationship entities.
     *
     * @Route("/", name="user_relationship")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('HushBundle:UserRelationship')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new UserRelationship entity.
     *
     * @Route("/", name="user_relationship_create")
     * @Method("POST")
     * @Template("HushBundle:UserRelationship:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new UserRelationship();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_relationship_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a UserRelationship entity.
    *
    * @param UserRelationship $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(UserRelationship $entity)
    {
        $form = $this->createForm(new UserRelationshipType(), $entity, array(
            'action' => $this->generateUrl('user_relationship_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserRelationship entity.
     *
     * @Route("/new", name="user_relationship_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new UserRelationship();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a UserRelationship entity.
     *
     * @Route("/{id}", name="user_relationship_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:UserRelationship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserRelationship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UserRelationship entity.
     *
     * @Route("/{id}/edit", name="user_relationship_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:UserRelationship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserRelationship entity.');
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
    * Creates a form to edit a UserRelationship entity.
    *
    * @param UserRelationship $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserRelationship $entity)
    {
        $form = $this->createForm(new UserRelationshipType(), $entity, array(
            'action' => $this->generateUrl('user_relationship_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserRelationship entity.
     *
     * @Route("/{id}", name="user_relationship_update")
     * @Method("PUT")
     * @Template("HushBundle:UserRelationship:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:UserRelationship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserRelationship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('user_relationship_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a UserRelationship entity.
     *
     * @Route("/{id}", name="user_relationship_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('HushBundle:UserRelationship')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserRelationship entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user_relationship'));
    }

    /**
     * Creates a form to delete a UserRelationship entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_relationship_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
