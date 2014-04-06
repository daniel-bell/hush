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
}
