<?php

namespace Tech506\Bundle\CallServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tech506\Bundle\CallServiceBundle\Entity\Product;
use Tech506\Bundle\CallServiceBundle\Entity\ProductCategory;
use Tech506\Bundle\CallServiceBundle\Entity\ProductSaleType;
use Tech506\Bundle\CallServiceBundle\Entity\State;

class LoadCallServiceData implements FixtureInterface, ContainerAwareInterface {
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $manager) {

        $this->createCategories($manager);

        $this->createStates($manager);

        $manager->flush();
    }

    public function createStates(ObjectManager $manager) {
        $stateNames = array("Barbosa", "Bello","Caldas", "Copacabana", "El Retiro", "Envigado", "Girardota", "Itagüí", "La Estrella"
            , "Marinilla", "Medellín", "Rionegro", "Sabaneta", "San Jerónimo");
        foreach($stateNames as $stateName) {
            $state = new State();
            $state->setName($stateName);
            $manager->persist($state);
        }
    }

    public function createCategories(ObjectManager $manager) {
        $serviceCategory = new ProductCategory();
        $serviceCategory->setName("Servicios");
        $serviceCategory->setDescription("Este producto es un servicio y no representa un objeto físico");
        $manager->persist($serviceCategory);
        $this->createServicesProducts($manager, $serviceCategory);

        $partsCategory = new ProductCategory();
        $partsCategory->setName("Repuestos");
        $partsCategory->setDescription("Partes físicas utilizadas en los servicios");
        $manager->persist($partsCategory);
        //$this->createServicesProducts($manager, $serviceCategory);
    }

    public function createServicesProducts(ObjectManager $manager, ProductCategory $category) {
        $revision = new Product();
        $revision->setCategory($category);
        $revision->setName("Revisión");
        $revision->setDescription("Revisar 1 Producto");
        $revision->setOnlyOnManaging(false);
        $manager->persist($revision);
        $this->createRevisionSaleTypes($manager, $revision);

        $product1 = new Product();
        $product1->setCategory($category);
        $product1->setName("Individual");
        $product1->setDescription("1 Producto");
        $product1->setOnlyOnManaging(false);
        $manager->persist($product1);
        $this->createCalentadorSaleTypes($manager, $product1);

        $product3 = new Product();
        $product3->setCategory($category);
        $product3->setName("Combo 2");
        $product3->setDescription("2 Productos");
        $product3->setOnlyOnManaging(false);
        $manager->persist($product3);
        $this->createCombo2SaleTypes($manager, $product3);

        $product4 = new Product();
        $product4->setCategory($category);
        $product4->setName("Combo 3");
        $product4->setDescription("3 Productos");
        $product4->setOnlyOnManaging(false);
        $manager->persist($product4);
        $this->createCombo3SaleTypes($manager, $product4);

        $product5 = new Product();
        $product5->setCategory($category);
        $product5->setName("Otro");
        $product5->setDescription("Cualquier otro tipo de servicio");
        $product5->setOnlyOnManaging(true);
        $manager->persist($product5);
        $this->createOtherSaleTypes($manager, $product5);

        $garanty = new Product();
        $garanty->setCategory($category);
        $garanty->setName("Garantía");
        $garanty->setDescription("");
        $garanty->setOnlyOnManaging(true);
        $manager->persist($garanty);
        $this->createGarantySaleTypes($manager, $garanty);
    }

    public function createGarantySaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Estándar");
        $saleType1->setDescription("");
        $saleType1->setFullPrice(0);
        $saleType1->setSellerWin(0);
        $saleType1->setTechnicianWin(0);
        $saleType1->setTransportationCost(0);
        $saleType1->setUtility(0);
        $saleType1->setOnlyForAdmin(false);
        $manager->persist($saleType1);
    }

    public function createRevisionSaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Estándar");
        $saleType1->setDescription("Venta normal");
        $saleType1->setFullPrice(30000);
        $saleType1->setSellerWin(6000);
        $saleType1->setTechnicianWin(8500);
        $saleType1->setTransportationCost(1500);
        $saleType1->setUtility(14000);
        $saleType1->setOnlyForAdmin(false);
        $manager->persist($saleType1);
    }

    public function createCalentadorSaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Estándar");
        $saleType1->setDescription("Venta normal");
        $saleType1->setFullPrice(65000);
        $saleType1->setSellerWin(11500);
        $saleType1->setTechnicianWin(16000);
        $saleType1->setTransportationCost(1500);
        $saleType1->setUtility(36000);
        $saleType1->setOnlyForAdmin(false);
        $manager->persist($saleType1);

        $saleType2 = new ProductSaleType();
        $saleType2->setProduct($product);
        $saleType2->setName("Promo");
        $saleType2->setDescription("Venta normal con precio de promoción");
        $saleType2->setFullPrice(60000);
        $saleType2->setSellerWin(10000);
        $saleType2->setTechnicianWin(15000);
        $saleType2->setTransportationCost(1500);
        $saleType2->setUtility(33500);
        $saleType2->setOnlyForAdmin(false);
        $manager->persist($saleType2);

        $saleType3 = new ProductSaleType();
        $saleType3->setProduct($product);
        $saleType3->setName("Director");
        $saleType3->setDescription("Venta autorizada por un director");
        $saleType3->setFullPrice(55000);
        $saleType3->setSellerWin(8500);
        $saleType3->setTechnicianWin(14500);
        $saleType3->setTransportationCost(1500);
        $saleType3->setUtility(30500);
        $saleType3->setOnlyForAdmin(true);
        $manager->persist($saleType3);

        $saleType4 = new ProductSaleType();
        $saleType4->setProduct($product);
        $saleType4->setName("Familiar");
        $saleType4->setDescription("Venta Familiar");
        $saleType4->setFullPrice(50000);
        $saleType4->setSellerWin(0);
        $saleType4->setTechnicianWin(14000);
        $saleType4->setTransportationCost(1500);
        $saleType4->setUtility(34500);
        $saleType4->setOnlyForAdmin(true);
        $manager->persist($saleType4);
    }

    public function createEstufaSaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Estándar");
        $saleType1->setDescription("Venta normal");
        $saleType1->setFullPrice(65000);
        $saleType1->setSellerWin(11500);
        $saleType1->setTechnicianWin(16000);
        $saleType1->setTransportationCost(1500);
        $saleType1->setUtility(36000);
        $saleType1->setOnlyForAdmin(false);
        $manager->persist($saleType1);

        $saleType2 = new ProductSaleType();
        $saleType2->setProduct($product);
        $saleType2->setName("Promo");
        $saleType2->setDescription("Venta normal con precio de promoción");
        $saleType2->setFullPrice(60000);
        $saleType2->setSellerWin(10000);
        $saleType2->setTechnicianWin(15000);
        $saleType2->setTransportationCost(1500);
        $saleType2->setUtility(33500);
        $saleType2->setOnlyForAdmin(false);
        $manager->persist($saleType2);

        $saleType3 = new ProductSaleType();
        $saleType3->setProduct($product);
        $saleType3->setName("Director");
        $saleType3->setDescription("Venta autorizada por un director");
        $saleType3->setFullPrice(55000);
        $saleType3->setSellerWin(8500);
        $saleType3->setTechnicianWin(14500);
        $saleType3->setTransportationCost(1500);
        $saleType3->setUtility(30500);
        $saleType3->setOnlyForAdmin(true);
        $manager->persist($saleType3);

        $saleType4 = new ProductSaleType();
        $saleType4->setProduct($product);
        $saleType4->setName("Familiar");
        $saleType4->setDescription("Venta Familiar");
        $saleType4->setFullPrice(50000);
        $saleType4->setSellerWin(0);
        $saleType4->setTechnicianWin(14000);
        $saleType4->setTransportationCost(1500);
        $saleType4->setUtility(34500);
        $saleType4->setOnlyForAdmin(true);
        $manager->persist($saleType4);
    }

    public function createCombo2SaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Estándar");
        $saleType1->setDescription("Venta normal");
        $saleType1->setFullPrice(90000);
        $saleType1->setSellerWin(19000);
        $saleType1->setTechnicianWin(26000);
        $saleType1->setTransportationCost(1500);
        $saleType1->setUtility(43500);
        $saleType1->setOnlyForAdmin(false);
        $manager->persist($saleType1);

        $saleType2 = new ProductSaleType();
        $saleType2->setProduct($product);
        $saleType2->setName("Promo");
        $saleType2->setDescription("Venta normal con precio de promoción");
        $saleType2->setFullPrice(85000);
        $saleType2->setSellerWin(16500);
        $saleType2->setTechnicianWin(25000);
        $saleType2->setTransportationCost(1500);
        $saleType2->setUtility(42000);
        $saleType2->setOnlyForAdmin(false);
        $manager->persist($saleType2);

        $saleType3 = new ProductSaleType();
        $saleType3->setProduct($product);
        $saleType3->setName("Director");
        $saleType3->setDescription("Venta autorizada por un director");
        $saleType3->setFullPrice(80000);
        $saleType3->setSellerWin(14000);
        $saleType3->setTechnicianWin(24500);
        $saleType3->setTransportationCost(1500);
        $saleType3->setUtility(40000);
        $saleType3->setOnlyForAdmin(true);
        $manager->persist($saleType3);

        $saleType4 = new ProductSaleType();
        $saleType4->setProduct($product);
        $saleType4->setName("Familiar");
        $saleType4->setDescription("Venta Familiar");
        $saleType4->setFullPrice(75000);
        $saleType4->setSellerWin(0);
        $saleType4->setTechnicianWin(24000);
        $saleType4->setTransportationCost(1500);
        $saleType4->setUtility(49500);
        $saleType4->setOnlyForAdmin(true);
        $manager->persist($saleType4);
    }

    public function createCombo3SaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Estándar");
        $saleType1->setDescription("Venta normal");
        $saleType1->setFullPrice(125000);
        $saleType1->setSellerWin(26000);
        $saleType1->setTechnicianWin(37500);
        $saleType1->setTransportationCost(1500);
        $saleType1->setUtility(60000);
        $saleType1->setOnlyForAdmin(false);
        $manager->persist($saleType1);

        $saleType2 = new ProductSaleType();
        $saleType2->setProduct($product);
        $saleType2->setName("Promo");
        $saleType2->setDescription("Venta normal con precio de promoción");
        $saleType2->setFullPrice(120000);
        $saleType2->setSellerWin(23000);
        $saleType2->setTechnicianWin(36500);
        $saleType2->setTransportationCost(1500);
        $saleType2->setUtility(59000);
        $saleType2->setOnlyForAdmin(false);
        $manager->persist($saleType2);

        $saleType3 = new ProductSaleType();
        $saleType3->setProduct($product);
        $saleType3->setName("Director");
        $saleType3->setDescription("Venta autorizada por un director");
        $saleType3->setFullPrice(110000);
        $saleType3->setSellerWin(20000);
        $saleType3->setTechnicianWin(36000);
        $saleType3->setTransportationCost(1500);
        $saleType3->setUtility(52500);
        $saleType3->setOnlyForAdmin(true);
        $manager->persist($saleType3);

        $saleType4 = new ProductSaleType();
        $saleType4->setProduct($product);
        $saleType4->setName("Familiar");
        $saleType4->setDescription("Venta Familiar");
        $saleType4->setFullPrice(100000);
        $saleType4->setSellerWin(0);
        $saleType4->setTechnicianWin(35500);
        $saleType4->setTransportationCost(1500);
        $saleType4->setUtility(63000);
        $saleType4->setOnlyForAdmin(true);
        $manager->persist($saleType4);
    }

    public function createOtherSaleTypes(ObjectManager $manager, Product $product) {
        $saleType1 = new ProductSaleType();
        $saleType1->setProduct($product);
        $saleType1->setName("Otro");
        $saleType1->setDescription("Venta normal");
        $saleType1->setFullPrice(0);
        $saleType1->setSellerWin(0);
        $saleType1->setTechnicianWin(0);
        $saleType1->setTransportationCost(0);
        $saleType1->setUtility(0);
        $saleType1->setOnlyForAdmin(true);
        $manager->persist($saleType1);
    }

}