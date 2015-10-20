<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PublicController: Handles the public requests
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 */
class PublicController extends Controller {

    /**
     * Render the homepage of the system
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction() {
        return $this->redirectToRoute('_admin_home');
        //return $this->render('Tech506CallServiceBundle:Public:index.html.twig');
    }
}
