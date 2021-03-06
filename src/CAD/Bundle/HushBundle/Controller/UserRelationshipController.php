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
use CAD\Bundle\HushBundle\Helpers\RelationshipChecker;

/**
 * UserRelationship controller.
 *
 * @Route("/user_relationship")
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
        $em = $this->getDoctrine()->getManager();
        $curr_user = $this->get('security.context')->getToken()->getUser();

        $query = $em->createQuery('SELECT rel from HushBundle:UserRelationship rel WHERE :user_id MEMBER OF rel.users');
        $query->setParameter('user_id', $curr_user);
        $entities = $query->getResult();

        $serializer = $this->container->get('serializer');
        $json_content = $serializer->serialize($entities, 'json');

        $response = new JsonResponse();
        $response->setContent(utf8_decode($json_content));

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
        $response_code = Response::HTTP_INTERNAL_SERVER_ERROR;

        try {
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

                $created_relationships = $checker_service->getCreatedRelationships($source_user);
                $request_count = 0;
                foreach ($created_relationships as $rel) {
                    if ($rel->getRelationshipType() == "FRIEND_REQUEST") {
                        $request_count += 1;
                    }
                }

                if ($request_count < 11) {
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
                        $response_text = 'Friend request sent';
                        $response_code = Response::HTTP_OK;
                    } // We're already friends with the target
                    else {
                        $response_text = 'Already friends with user';
                    }
                } else {
                    $response_text = 'You cannot make more than 10 friendship requests at once.';
                }
            } // No user found with that username
            else {
                $response_text = 'Already friends with user';
            }
        } catch (Exception $ex) {
            $response_text = 'Error creating relationship: ' . $ex->getMessage();
        }

        $response = new Response(
            $response_text,
            $response_code,
            array('content-type' => 'text/plain')
        );
        return $response;
    }

    /**
     * Confirms a UserRelationship entity.
     *
     * @Route("/confirm/{id}", name="user_relationship_confirm")
     * @Method("GET")
     */
    public function confirmAction($id)
    {
        $response_code = Response::HTTP_INTERNAL_SERVER_ERROR;

        $em = $this->getDoctrine()->getManager();
        $source_user = $this->get('security.context')->getToken()->getUser();
        $target_user = $em->getRepository('CAD\Bundle\HushBundle\Entity\Users')->findOneBy(array('id' => $id));

        if (!empty($target_user)) {
            $checker_service = $this->get('relationship_checker');

            if ($checker_service->inRelationship($source_user, $target_user)) {
                $relationships = $checker_service->getRelationships($source_user, $target_user);

                if ($relationships[0]->getRelationshipType() == "FRIEND_REQUEST") {
                    if ($relationships[0]->getCreatorUser()->getId() != $source_user->getId()) {
                        $response_code = Response::HTTP_OK;
                        $response_text = "Friendship accepted";
                        $relationships[0]->setRelationshipType("FRIEND");
                        $em->flush();
                    } else {
                        $response_text = 'You cannot accept friend requests that you create.';
                    }
                } else {
                    if ($relationships[0]->getRelationshipType() == "FRIEND") {
                        $response_text = 'You are already friends with this user.';
                    } else {
                        $response_text = 'Error accepting relationship.';
                    }
                }
            } else {
                $response_text = 'Error accepting relationship.';
            }
        } else {
            $response_text = 'Error accepting relationship.';
        }

        $response = new Response(
            $response_text,
            $response_code,
            array('content-type' => 'text/plain')
        );
        return $response;
    }

    /**
     * Deletes a UserRelationship entity.
     * Takes the ID of a Users entity, not UserRelationship
     *
     * @Route("/delete/{id}", name="user_relationship_delete")
     * @Method("POST")
     * @Method("GET")
     */
    public
    function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $source_user = $this->get('security.context')->getToken()->getUser();
        $target_user = $em->getRepository('CAD\Bundle\HushBundle\Entity\Users')->findOneBy(array('id' => $id));
        $checker_service = $this->get('relationship_checker');

        if (!empty($target_user) && $checker_service->inRelationship($source_user, $target_user)) {
            $relationship = $checker_service->getRelationships($source_user, $target_user);

            $em->remove($relationship[0]);
            $em->flush();

            $response_text = 'Friend request sent';
            $response_code = Response::HTTP_OK;
        } else {
            $response_text = 'You are not friends with user';
            $response_code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new Response(
            $response_text,
            $response_code,
            array('content-type' => 'text/plain')
        );
        return $response;
    }
}