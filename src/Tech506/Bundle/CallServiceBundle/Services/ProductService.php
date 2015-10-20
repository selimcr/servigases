<?php
namespace Tech506\Bundle\CallServiceBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tech506\Bundle\CallServiceBundle\Entity\ProductSaleType;
use Tech506\Bundle\SecurityBundle\Util\Enum\RolesEnum;
use Tecnotek\Bundle\AsiloBundle\Util\Enum\PermissionsEnum;

/**
 * Service that handles some logic related with the system products
 */
class ProductService {

    private $securityContext;
    private $router;
    private $logger;
    private $em;
    private $conn;
    private $translator;
    private $session;

    public function __construct($securityContext, $router, $logger, \Doctrine\ORM\EntityManager $em,
                                $translator, $session) {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->logger = $logger;
        $this->em = $em;
        $this->conn = $em->getConnection();
        $this->translator = $translator;
        $this->session = $session;
    }

    public function getAvailableProductsInfo() {
        $user = $this->securityContext->getToken()->getUser();
        $isAdmin = $this->securityContext->isGranted(RolesEnum::ADMINISTRATOR);
        $products = $this->em->getRepository('Tech506CallServiceBundle:Product')->findAll();
        $productsArray = array();
        foreach($products as $product) {
            //$product = new Product();
            $productArray = array();
            $productArray["id"] = $product->getId();
            $productArray["name"] = $product->getName();
            $productArray["description"] = $product->getDescription();
            $productTypesArray = array();
            foreach($product->getSaleTypes() as $saleType) {
                //$saleType = new ProductSaleType();
                if( $isAdmin ||
                    ($saleType->getName() != "Director" &&
                    $saleType->getName() != "Familiar") ) {

                    $saleTypeArray = array();
                    $saleTypeArray['id'] = $saleType->getId();
                    $saleTypeArray['name'] = $saleType->getName();
                    $saleTypeArray['fullPrice'] = $saleType->getFullPrice();
                    $description = $saleType->getDescription();
                    if($isAdmin) {
                        $description .= " [Com. Vendedor(a): " . $saleType->getSellerWin()
                            . ", Com. Tecnico(a): " . $saleType->getTechnicianWin() . "]" ;
                    } else {
                        $description .= " [Com. Vendedor(a): " . $saleType->getSellerWin() . "]" ;
                    }
                    $saleTypeArray['description'] = $description;
                    array_push($productTypesArray, $saleTypeArray);
                }
            }
            $productArray['types'] = $productTypesArray;
            array_push($productsArray, $productArray);
        }
        return $productsArray;
    }
}