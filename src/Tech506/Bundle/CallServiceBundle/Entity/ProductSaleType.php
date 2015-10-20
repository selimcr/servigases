<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="product_sale_types")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\CallServiceBundle\Repository\PriceRepository")
 */
class ProductSaleType {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $description;

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
     * @ORM\Column(name="only_for_admin", type="boolean")
     */
    private $onlyForAdmin;

    /**
     * @ManyToOne(targetEntity="Product")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    public function __construct() {
        $this->onlyForAdmin = false;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $eps
     * @return Product
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $arl
     * @return Product
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getOnlyForAdmin() {
        return $this->onlyForAdmin;
    }

    public function setOnlyForAdmin($onlyForAdmin) {
        $this->onlyForAdmin = $onlyForAdmin;
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
     * @return ProductSellType
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
     * @return ProductSellType
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
     * @return ProductSellType
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
     * @return ProductSellType
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
     * @return ProductSellType
     */
    public function setUtility($utility) {
        $this->utility = $utility;
        return $this;
    }

    /**
     * Set product
     *
     * @param \Tech506\Bundle\CallServiceBundle\Entity\Product $product
     */
    public function setProduct(\Tech506\Bundle\CallServiceBundle\Entity\Product $product) {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return \Tech506\Bundle\CallServiceBundle\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

    public function getNewServiceDetailFromMe(){
        $detail = new ServiceDetail();
        $detail->setFullPrice($this->getFullPrice());
        $detail->setSellerWin($this->getSellerWin());
        $detail->setTechnicianWin($this->getTechnicianWin());
        $detail->setUtility($this->getUtility());
        $detail->setTransportationCost($this->getTransportationCost());
        $detail->setProductSaleType($this);
        return $detail;
    }

    public function __toString(){
        return $this->product . " [" . $this->name . "]";
    }
}
?>
