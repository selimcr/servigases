<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AdminController: Handles the basic requests of the invoices generation and management
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/")
 */
class InvoiceController extends Controller {

    /**
     * @Route("/sales/new", name="_admin_sales_new")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function newAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Sales/new.html.twig', array(
        ));
    }

    /**
     * @Route("/products/services/list", name="_admin_products_service_list")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    private function getProductsList() {
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
            $paginator = $em->getRepository('Tech506CallServiceBundle:Product')
                ->getPageWithFilter($offset, $limit, $search, $sort, $order);
            $results = $this->getProductsResults($paginator);
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Seller::getUsersList [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    private function getProductsResults($paginator) {
        $results = array();
        foreach($paginator as $product) {
            array_push($results, array(
                'id' => $product->getId(),
                'name' => $product->getName(),
            ));
        }
        return $results;
    }
}
