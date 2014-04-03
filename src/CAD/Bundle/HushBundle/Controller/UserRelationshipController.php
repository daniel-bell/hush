<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContext;
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
        $qb = $em->createQueryBuilder();

        $curr_user = $this->get('security.context')->getToken()->getUser();

        $query = $qb->createQuery('SELECT rel from HushBundle:UserRelationship rel WHERE :user_id MEMBER OF rel.users');
        $query->setParameter('user_id', $curr_user->getId());
        $entities = $query->getResult();

        $response = new JsonResponse();
        $response->setData($entities);

        return $response;
    }

    public function indexJsonAction()
    {

        $entities = $this->getAll();

        $response = new JsonResponse();
        $response->setData($entities);

        return $response;
    }
    /**
     * Creates a new UserRelationship entity.
     *
     * @Route("/new", name="user_relationship_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $new_rel = new UserRelationship();
        
        $params = $request->request->get("json_str");
        $params = stripslashes($params);
        $relation_params = json_decode(trim($params, '"'));
        unset($params);
        $em = $this->getDoctrine()->getManager();

        // Check that there's a valid relationship type
        $relationship_type = $relation_params->type;
        if($relationship_type == 'FRIEND_REQUEST' || $relationship_type != 'BLOCK'){
            $target_username = $relation_params->target_user;
            $target_user = $em->getRepository('CAD\Bundle\HushBundle\Entity\Users')->findBy(array('username' => $target_username));
            

            if(!empty($target_user)){
                $source_user = $this->get('security.context')->getToken()->getUser();

                $new_rul->setCreator($source_user);

                $new_rel->addUser($source_user);
                $new_rel->addUser($target_user);

                $new_rel->setCreatorKey("creatorkey");
                $new_rel->setTargetKey("unset");

                $new_rel->setRelationshipType($relationship_type);

                $new_rel->persist($entity);
                $new_rel->flush();

                // Everything is golden, respond with 200
                $response = new Response(
                    'Content',
                    Response::HTTP_OK,
                    array('content-type' => 'text/html')
                );
            }
        }

        // Something has gone wrong, respond with error
        $response = new Response(
            'Content',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            array('content-type' => 'text/html')
        );
    }

    /**
     * Creates a new UserRelationship entity.
     *
     * @Route("/confirm/{id}", name="user_relationship_create")
     * @Method("POST")
     */
    public function confirmAction($id){
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:UserRelationship')->find($id);
        $curr_user = $this->get('security.context')->getToken()->getUser();

        if($entity){
            if($entity->getRelationshipType() == "FRIEND_REQUEST"){
                if(in_array($curr_user, $entity->getUsers())){
                    if($curr_user != $entity->getCreator()){
                        $entity->setRelationshipType("FRIEND_CONFIRMED");
                        $em->flush();
                    }
                }
            } 
        }

        // Something has gone wrong, respond with error
        $response = new Response(
            'Content',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            array('content-type' => 'text/html')
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
