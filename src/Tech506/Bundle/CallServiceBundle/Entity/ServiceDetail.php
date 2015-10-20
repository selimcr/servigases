<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="services_details")
 * @ORM\Entity()
 */
class ServiceDetail {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $observations;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $fullPrice;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $sellerWin;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $technicianWin;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $transportationCost;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $utility;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ManyToOne(targetEntity="TechnicianService")
     * @JoinColumn(name="technician_service_id", referencedColumnName="id")
     */
    private $technicianService;

    /**
     * @ManyToOne(targetEntity="ProductSaleType")
     * @JoinColumn(name="product_sale_type_id", referencedColumnName="id")
     */
    private $productSaleType;

    public function __construct() {
        $this->observations = "";
        $this->quantity = 1;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getObservations() {
        return $this->observations;
    }

    /**
     * @param mixed $observations
     * @return InvoiceDetail
     */
    public function setObservations($observations) {
        $this->observations = $observations;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullPrice() {
        return $this->fullPrice;
    }

    /**
     * @param mixed $fullPrice
     * @return InvoiceDetail
     */
    public function setFullPrice($fullPrice) {
        $this->fullPrice = $fullPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSellerWin() {
        return $this->sellerWin;
    }

    /**
     * @param mixed $sellerWin
     * @return InvoiceDetail
     */
    public function setSellerWin($sellerWin) {
        $this->sellerWin = $sellerWin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechnicianWin() {
        return $this->technicianWin;
    }

    /**
     * @param mixed $technicianWin
     * @return InvoiceDetail
     */
    public function setTechnicianWin($technicianWin) {
        $this->technicianWin = $technicianWin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransportationCost() {
        return $this->transportationCost;
    }

    /**
     * @param mixed $transportationCost
     * @return InvoiceDetail
     */
    public function setTransportationCost($transportationCost) {
        $this->transportationCost = $transportationCost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUtility() {
        return $this->utility;
    }

    /**
     * @param mixed $utility
     * @return InvoiceDetail
     */
    public function setUtility($utility) {
        $this->utility = $utility;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return InvoiceDetail
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechnicianService() {
        return $this->technicianService;
    }

    /**
     * @param mixed $technicianService
     * @return InvoiceDetail
     */
    public function setTechnicianService($technicianService) {
        $this->technicianService = $technicianService;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductSaleType() {
        return $this->productSaleType;
    }

    /**
     * @param mixed $productSaleType
     * @return InvoiceDetail
     */
    public function setProductSaleType($productSaleType) {
        $this->productSaleType = $productSaleType;
        return $this;
    }

    public function __toString(){
        return $this->id;
    }
}
?>
