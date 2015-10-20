<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tech506\Bundle\CallServiceBundle\Entity\Call;
use Tech506\Bundle\CallServiceBundle\Entity\Client;

/**
 * Class AdminController: Handles the basic requests of the invoices generation and management
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/calls")
 */
class CallsController extends Controller {

    /**
     * @Route("/", name="_admin_calls")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function techniciansAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Sales/Calls/list.html.twig', array(
            'role' => 'ROLE_TECHNICIAN'
        ));
    }

    /**
     * Returns the List of Calls paginated for Bootstrap Table depending of the current User
     *
     * @Route("/list", name="_admin_calls_list")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function paginatedListAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $limit = $request->get('limit');
            $offset = $request->get('offset');
            $order = $request->get('order');
            $search = $request->get('search');
            $sort = $request->get('sort');
            $em = $this->getDoctrine()->getManager();
            $usr= $this->get('security.context')->getToken()->getUser();
            $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');

            $paginator = $em->getRepository('Tech506CallServiceBundle:Call')
                ->getPageWithFilterForUser($offset, $limit, $search, $sort, $order, $isAdmin, $usr);
            $results = $this->getResults($paginator);
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('User::getUsersList [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    private function getResults($paginator) {
        $results = array();
        foreach($paginator as $call) {
            //$call = new Call();
            $seller = $call->getSeller();
            $dateTime = $call->getDateTime();

            array_push($results, array(
                'id'        => $call->getId(),
                'seller'    => $seller->getFullName(),
                'client'    => $call->getClient()->toString(),
                'dateTime'  => (isset($dateTime))? $dateTime->format('Y-m-d H:i:s'):'',
                'generateService'   => $call->getGenerateService(),
            ));
        }
        return $results;
    }

}
