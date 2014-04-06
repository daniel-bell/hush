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
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use CAD\Bundle\HushBundle\Helpers\RelationshipChecker;

/**
 * UserRelationship controller.
 *
 * @Route("/user_relationship")
 * @ExclusionPolicy("none")
 *
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
        // Check if the user is logged in, if not 403
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getManager();
            $curr_user = $this->get('security.context')->getToken()->getUser();

            $query = $em->createQuery('SELECT rel from HushBundle:UserRelationship rel WHERE :user_id MEMBER OF rel.users');
            $query->setParameter('user_id', $curr_user);
            $entities = $query->getResult();

            $serializer = $this->container->get('serializer');
            $json_content = $serializer->serialize($entities, 'json');

            $response = new JsonResponse();
            $response->setContent(utf8_decode($json_content));
        } else {
            $response = new Response(
                '403 - Access Forbidden',
                Response::HTTP_FORBIDDEN,
                array('content-type' => 'text/html')
            );
        }

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

        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $params = $request->request->get("json_str");
            $params = stripslashes($params);
            $relation_params = json_decode(trim($params, '"'));
            unset($params);
            $em = $this->getDoctrine()->getManager();

            // Check that there's a valid relationship type
            $relationship_type = 'FRIEND_REQUEST';
            $target_username = $relation_params->target_username;

            $source_user = $this->get('security.context')->getToken()->getUser();
            $target_user = $em->getRepository('CAD\Bundle\HushBundle\Entity\Users')->findOneBy(array('username' => $target_username));

            if (!empty($target_user)) {
                $checker_service = $this->get('relationship_checker');

                if (!$checker_service->inRelationship($source_user, $target_user)) {
                    $new_rel = new UserRelationship();
                    $new_rel->setCreatorUser($source_user);

                    $new_rel->setCreatorUserKey("creatorkey");
                    $new_rel->setTargetUserKey("unset");

                    $new_rel->setRelationshipType($relationship_type);
                    $new_rel->setRelationshipKey("default");

                    $new_rel->addUser($source_user);
                    $new_rel->addUser($target_user);

                    $em->persist($new_rel);
                    $em->flush();

                    // Everything is golden, respond with 200
                    $response = new Response(
                        'Friend request sent',
                        Response::HTTP_OK,
                        array('content-type' => 'text/plain')
                    );
                } // We're already friends with the target
                else {
                    $response = new Response(
                        'Already friends with this user: ' . $target_username,
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        array('content-type' => 'text/plain')
                    );
                }
            } // No user found with that username
            else {
                $response = new Response(
                    'Error',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    array('content-type' => 'text/plain')
                );
            }
        } // User not logged in
        else {
            $response = new Response(
                'You are not logged in',
                Response::HTTP_FORBIDDEN,
                array('content-type' => 'text/plain')
            );
        }

        return $response;
    }

    /**
     * Confirms a UserRelationship entity.
     *
     * @Route("/confirm/{id}", name="user_relationship_confirm")
     * @Method("POST")
     */
    public function confirmAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HushBundle:UserRelationship')->find($id);
        $curr_user = $this->get('security.context')->getToken()->getUser();

        if ($entity) {
            if ($entity->getRelationshipType() == "FRIEND_REQUEST") {
                if (in_array($curr_user, $entity->getUsers())) {
                    if ($curr_user != $entity->getCreator()) {
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
}