<?php

namespace CAD\Bundle\HushBundle\Helpers;

use \Doctrine\ORM\EntityManager;
use \CAD\Bundle\HushBundle\Entity\Users;

class RelationshipChecker{
    protected $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function inRelationship(Users $user1, Users $user2){
        if($user1->getId() == $user2->getId()){
            return false;
        }

        $check_query = $this->em->createQuery('SELECT rel from HushBundle:UserRelationship rel WHERE :user1 MEMBER OF rel.users AND :user2 MEMBER OF rel.users');
        $check_query->setParameter('user1', $user1);
        $check_query->setParameter('user2', $user2);
        $entities = $check_query->getResult();

        if(empty($entities)){
            return false;
        }

        return true;
    }
}