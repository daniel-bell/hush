<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CAD\Bundle\HushBundle\Entity\IpHistory;
use CAD\Bundle\HushBundle\Form\IpHistoryType;

/**
 * IpHistory controller.
 *
 * @Route("/ip_history")
 */
class IpHistoryController extends Controller
{

    /**
     * Lists all IpHistory entities.
     *
     * @Route("/", name="ip_history")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('HushBundle:IpHistory')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new IpHistory entity.
     *
     * @Route("/", name="ip_history_create")
     * @Method("POST")
     * @Template("HushBundle:IpHistory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new IpHistory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ip_history_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a IpHistory entity.
    *
    * @param IpHistory $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(IpHistory $entity)
    {
        $form = $this->createForm(new IpHistoryType(), $entity, array(
            'action' => $this->generateUrl('ip_history_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new IpHistory entity.
     *
     * @Route("/new", name="ip_history_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new IpHistory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a IpHistory entity.
     *
     * @Route("/{id}", name="ip_history_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:IpHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IpHistory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing IpHistory entity.
     *
     * @Route("/{id}/edit", name="ip_history_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:IpHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IpHistory entity.');
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
    * Creates a form to edit a IpHistory entity.
    *
    * @param IpHistory $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(IpHistory $entity)
    {
        $form = $this->createForm(new IpHistoryType(), $entity, array(
            'action' => $this->generateUrl('ip_history_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing IpHistory entity.
     *
     * @Route("/{id}", name="ip_history_update")
     * @Method("PUT")
     * @Template("HushBundle:IpHistory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:IpHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IpHistory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ip_history_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a IpHistory entity.
     *
     * @Route("/{id}", name="ip_history_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('HushBundle:IpHistory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find IpHistory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ip_history'));
    }

    /**
     * Creates a form to delete a IpHistory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ip_history_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
