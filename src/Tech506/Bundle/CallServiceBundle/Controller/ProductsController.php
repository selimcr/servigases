<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tech506\Bundle\CallServiceBundle\Entity\Client;
use Tech506\Bundle\CallServiceBundle\Entity\Product;
use Tech506\Bundle\CallServiceBundle\Entity\ProductCategory;
use Tech506\Bundle\CallServiceBundle\Entity\ProductSaleType;

/**
 * Class AdminController: Handles the basic requests of the invoices generation and management
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/products")
 */
class ProductsController extends Controller {

    /*******************************************************/
    /***                    CATEGORIES                   ***/
    /*******************************************************/

    /**
     * @Route("/categories", name="_admin_products_categories")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function categoriesAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Products/categories.html.twig');
    }

    /**
     * Returns the List of Administrators paginated for Bootstrap Table
     *
     * @Route("/categories/list", name="_admin_products_categories_list")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function categoriesPaginatedListAction() {
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
            $paginator = $em->getRepository('Tech506CallServiceBundle:ProductCategory')
                ->getPageWithFilter($offset, $limit, $search, $sort, $order);
            $results = array();
            foreach($paginator as $category) {
                array_push($results, array(
                    'id'            => $category->getId(),
                    'name'          => $category->getName(),
                    'description'   => $category->getDescription(),
                ));
            }
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('ProductCategories::List [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * Save or Update a Product Category
     *
     * @Route("/categories/save", name="_admin_products_categories_save")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function categoriesSaveAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }

        try {
            //Get parameters
            $request = $this->get('request');
            $id = $request->get('id');
            $name = $request->get('name');
            $translator = $this->get("translator");

            if( isset($id) && isset($name) && trim($name) != ""){
                $em = $this->getDoctrine()->getManager();
                $entity = new ProductCategory();
                if($id != 0) { //It's updating, find the dance
                    $entity = $em->getRepository('Tech506CallServiceBundle:ProductCategory')->find($id);
                }
                if( isset($entity) ) {
                    $entity->setName($name);
                    $entity->setDescription($request->get('description'));
                    $em->persist($entity);
                    $em->flush();
                    return new Response(json_encode(array(
                        'error' => false,
                        'msg' => $translator->trans('product.category.save.success'))));
                } else {
                    return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
                }
            } else {
                return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
            }
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('ProductCategories::save [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'msg' => $info)));
        }
    }

    /*******************************************************/
    /***                     PRODUCTS                    ***/
    /*******************************************************/

    /**
     * @Route("/", name="_admin_products")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function productsAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Products/list.html.twig');
    }

    /**
     * Returns the List of Administrators paginated for Bootstrap Table
     *
     * @Route("/list", name="_admin_products_list")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function productsPaginatedListAction() {
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
            $category = $request->get('category');
            $em = $this->getDoctrine()->getManager();
            $paginator = $em->getRepository('Tech506CallServiceBundle:Product')
                ->getPageWithFilter($offset, $limit, $search, $sort, $order, $category);
            $results = array();
            $translator = $this->get('translator');
            foreach($paginator as $category) {
                $onlyOnManagingtText = $category->getOnlyOnManaging()? $translator->trans('yes'):$translator->trans('no');
                array_push($results, array(
                    'id'            => $category->getId(),
                    'name'          => $category->getName(),
                    'description'   => $category->getDescription(),
                    'amountOfPrices'    => sizeof($category->getSaleTypes()),
                    'categoryId'    => $category->getCategory()->getId(),
                    'categoryName'  => $category->getCategory()->getName(),
                    'onlyOnManagingText' => $onlyOnManagingtText,
                    'onlyOnManaging' => $category->getOnlyOnManaging()
                ));
            }
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('ProductCategories::List [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * Save or Update a Product Category
     *
     * @Route("/save", name="_admin_products_save")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function productsSaveAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }

        try {
            //Get parameters
            $request = $this->get('request');
            $id = $request->get('id');
            $name = $request->get('name');
            $translator = $this->get("translator");

            if( isset($id) && isset($name) && trim($name) != ""){
                $em = $this->getDoctrine()->getManager();
                $entity = new Product();
                if($id != 0) { //It's updating, find the dance
                    $entity = $em->getRepository('Tech506CallServiceBundle:Product')->find($id);
                }
                if( isset($entity) ) {
                    $entity->setName($name);
                    $entity->setDescription($request->get('description'));
                    $entity->setCategory(
                        $em->getRepository('Tech506CallServiceBundle:ProductCategory')
                            ->find($request->get('category')));
                    $entity->setOnlyOnManaging($request->get('onlyOnManaging'));
                    $em->persist($entity);
                    $em->flush();
                    return new Response(json_encode(array(
                        'error' => false,
                        'msg' => $translator->trans('product.save.success'))));
                } else {
                    return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
                }
            } else {
                return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
            }
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('ProductCategories::save [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'msg' => $info)));
        }
    }

    /*******************************************************/
    /***                      PRICES                     ***/
    /*******************************************************/

    /**
     * @Route("/prices", name="_admin_products_prices")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function pricesAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Products/prices.html.twig', array(
            'productId' => 0
        ));
    }

    /**
     * @Route("/{productId}/prices", name="_admin_products_product_prices")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function productPricesAction($productId) {
        return $this->render('Tech506CallServiceBundle:Admin:Products/prices.html.twig', array(
            'productId' => $productId
        ));
    }

    /**
     * Returns the List of Administrators paginated for Bootstrap Table
     *
     * @Route("/prices/list", name="_admin_products_prices_list")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function pricesPaginatedListAction() {
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
            $productId = $request->get('product');
            $em = $this->getDoctrine()->getManager();
            $paginator = $em->getRepository('Tech506CallServiceBundle:ProductSaleType')
                ->getPageWithFilter($offset, $limit, $search, $sort, $order, $productId);
            $results = array();
            $translator = $this->get('translator');
            foreach($paginator as $price) {
                $onlyForAdminText = $price->getOnlyForAdmin()? $translator->trans('yes'):$translator->trans('no');
                array_push($results, array(
                    'id'                    => $price->getId(),
                    'name'                  => $price->getName(),
                    'description'           => $price->getDescription(),
                    'fullPrice'             => intval($price->getFullPrice()),
                    'sellerWin'             => intval($price->getSellerWin()),
                    'technicianWin'         => intval($price->getTechnicianWin()),
                    'transportationCost'    => intval($price->getTransportationCost()),
                    'utility'               => intval($price->getUtility()),
                    'onlyForAdmin'          => $onlyForAdminText,
                    'onlyForAdminValue'     => $price->getOnlyForAdmin()
                ));
            }
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('ProductCategories::List [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * Save or Update a Product Category
     *
     * @Route("/prices/save", name="_admin_products_prices_save")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function pricesSaveAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }

        try {
            //Get parameters
            $request = $this->get('request');
            $id = $request->get('id');
            $name = $request->get('name');
            $translator = $this->get("translator");

            if( isset($id) && isset($name) && trim($name) != ""){
                $em = $this->getDoctrine()->getManager();
                $entity = new ProductSaleType();
                if($id != 0) { //It's updating, find the dance
                    $entity = $em->getRepository('Tech506CallServiceBundle:ProductSaleType')->find($id);
                } else {
                    $entity->setProduct(
                        $em->getRepository('Tech506CallServiceBundle:Product')->find($request->get('productId')));
                }
                if( isset($entity) ) {
                    $entity->setName($name);
                    $entity->setDescription($request->get('description'));
                    $entity->setFullPrice($request->get('fullPrice'));
                    $entity->setSellerWin($request->get('sellerWin'));
                    $entity->setTechnicianWin($request->get('technicianWin'));
                    $entity->setTransportationCost($request->get('transportationCost'));
                    $entity->setUtility($request->get('utility'));
                    $entity->setOnlyForAdmin($request->get('onlyForAdmin'));
                    $em->persist($entity);
                    $em->flush();
                    return new Response(json_encode(array(
                        'error' => false,
                        'msg' => $translator->trans('price.save.success'))));
                } else {
                    return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
                }
            } else {
                return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
            }
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('ProductPrice::save [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'msg' => $info)));
        }
    }

    /**
     * @Route("/prices/load", name="_admin_products_services_prices_load")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function loadProductServicesPricesAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
            $product = $em->getRepository('Tech506CallServiceBundle:Product')->find($request->get('productId'));
            $prices = array();
            foreach($product->getSaleTypes() as $price) {
                if ($price->getOnlyForAdmin() == false || $isAdmin) {
                    array_push($prices, array(
                        'id'                    => $price->getId(),
                        'productId'             => $product->getId(),
                        'name'                  => $price->getName(),
                        'description'           => $price->getDescription(),
                        'fullPrice'             => intval($price->getFullPrice()),
                        'sellerWin'             => intval($price->getSellerWin()),
                        'technicianWin'         => intval($price->getTechnicianWin()),
                        'transportationCost'    => intval($price->getTransportationCost()),
                        'utility'               => intval($price->getUtility()),
                    ));
                }
            }
            return new Response(json_encode(array(
                'error'     => false,
                'msg'       => "",
                'prices'    => $prices
            )));

        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Product::loadProductServicesPricesAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * @Route("/services/load", name="_admin_products_services_load")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function loadProductServicesAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $productsArray = $this->get("productService")->getAvailableProductsInfo();
            return new Response(json_encode(array(
                'error'     => false,
                'msg'       => "",
                'products'  => $productsArray
            )));

        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Product::loadProductServicesAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }
}
