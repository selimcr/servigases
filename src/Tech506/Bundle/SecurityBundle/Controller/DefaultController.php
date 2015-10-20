<?php

namespace Tech506\Bundle\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('Tech506SecurityBundle:Default:index.html.twig', array('name' => $name));
    }
}
