<?php

namespace CAD\Bundle\HushBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('HushBundle:Default:index.html.twig', array('name' => $name));
    }
}
