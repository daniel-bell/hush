<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Get all messages
     */
    private function getAll() {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('HushBundle:Messages')->findAll();

        return $entities;
    }

    /**
     * Lists all Messages entities as a JSON array
     *
     * @Route(".json", name="messages_json")
     * @Method("GET")
     * @Template()
     */
    public function indexJsonAction()
    {

        $entities = $this->getAll();

        $response = new JsonResponse();
        $response->setData($entities);

        return $response;

    }

    /**
     * Lists all Messages entities.
     *
     * @Route("/", name="messages")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {

        $entities = $this->getAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Messages entity.
     *
     * @Route("/send", name="messages_create")
     * @Method("POST")
     */
    public function sendAction(Request $request)
    {
        try{
                $entity = new Messages();

                // Grab the json_str from the POST request
                $params = $request->request->get("json_str");
                $params = stripslashes($params);
                $message_params = json_decode(trim($params, '"'));
                unset($params);

                $entity->setMessageContent($message_params->messageContent);

                $date = $message_params->sentTime->date;
                $time = $message_params->sentTime->time;

                // Create a date from a mangled set of strings
                $entity->setSentTime(new \DateTime(
                  $date->year . '-' .
                  $date->month . '-' .
                  $date->day . ' ' .
                  $time->hour . ':' .
                  $time->minute));

                $em = $this->getDoctrine()->getManager();

                $source_user = $this->get('security.context')->getToken()->getUser();
                $target_user = $em->getRepository('CAD\Bundle\HushBundle\Entity\Users')->findOneBy(array('id' => $message_params->targetUser));

                // Error if the target user does not exist
                if($target_user != null){
                    // Check that the source and target user are in a relationship
                    $checker_service = $this->get('relationship_checker');
                    if($checker_service->inRelationship($source_user, $target_user)){
                        $relationship = $checker_service->getRelationships($source_user, $target_user);

                        $entity->setTargetUser($target_user);
                        $entity->setsendUser($source_user);
                        $entity->setMessageKey($message_params->messageKey);
                        $entity->setRelationship($relationship[0]);

                        $relationship[0]->addMessage($entity);

                        $em->persist($entity);
                        $em->flush();

                        // Everything worked, 200 response
                        $response = new Response(
                            'Message Sent',
                            Response::HTTP_OK,
                            array('content-type' => 'text/plain')
                        );
                    }
                    else{
                        $response = new Response(
                            'You can\'t send a message to someone you\'re not friends with: ' . $target_user->getUsername(),
                            Response::HTTP_INTERNAL_SERVER_ERROR,
                            array('content-type' => 'text/plain')
                        );
                    }
                }
                else{
                    $response = new Response(
                        'Sending message failed, target user does not exist',
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        array('content-type' => 'text/plain')
                    );
                }
            }
            catch(Exception $ex){
                // Response 500 with the exception error
                // Temporary until exceptions are indentified and caught
                $response = new Response(
                    'Error saving message: ' . $ex->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    array('content-type' => 'text/plain')
                );
            }

        return $response;
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
     * Get the latest messages sent by a user
     *
     * @Route("/inbox/{id}", name="messages_inbox")
     * @Method("GET")
     */
    public function inboxAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $curr_user = $this->get('security.context')->getToken()->getUser();
        $from_user = $em->getRepository('CAD\Bundle\HushBundle\Entity\Users')->findOneBy(array('id' => $id));

        if(!$from_user){
            $response = new Response(
                'No relationship',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                array('content-type' => 'text/html'));
            return $response;
        }

        $checker_service = $this->get('relationship_checker');
        $relationship = $checker_service->getRelationships($curr_user, $from_user);

        if(empty($relationship)){
            $response = new Response(
                'No relationship with user',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                array('content-type' => 'text/html'));
            return $response;
        }

        $messages = $em->getRepository('CAD\Bundle\HushBundle\Entity\Messages')->findBy(array('relationship' => $relationship[0]));

        $response = new JsonResponse();
        $response->setData($messages);

        return $response;
    }

    /**
     * Get item with ID
     */
    private function getItem($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:Messages')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Messages entity.');
        }

        return $entity;
    }

    /**
     * Finds and displays a Messages entity in JSON
     *
     * @Route("/{id}.json", name="messages_show_json")
     * @Method("GET")
     * @Template()
     */
    public function showJsonAction($id) 
    { 
        $entity = $this->getItem($id);

        $response = new JsonResponse();
        $response->setData($entity);

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

        $entity = $this->getItem($id);
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
