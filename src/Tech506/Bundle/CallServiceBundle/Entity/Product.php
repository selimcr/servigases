<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\CallServiceBundle\Repository\ProductRepository")
 */
class Product {

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
     * @ORM\Column(name="only_on_managing", type="boolean")
     */
    private $onlyOnManaging;

    /**
     * @ManyToOne(targetEntity="ProductCategory")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var saleTypes
     *
     * @ORM\OneToMany(targetEntity="ProductSaleType", mappedBy="product", cascade={"all"})
     */
    private $saleTypes;

    public function __construct() {
        $this->saleTypes = new ArrayCollection();
        $this->onlyOnManaging = false;
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

    public function getOnlyOnManaging() {
        return $this->onlyOnManaging;
    }

    public function setOnlyOnManaging($onlyOnManaging) {
        $this->onlyOnManaging = $onlyOnManaging;
        return $this;
    }

    /**
     * Set category
     *
     * @param \Tech506\Bundle\CallServiceBundle\Entity\ProductCategory $category
     */
    public function setCategory(\Tech506\Bundle\CallServiceBundle\Entity\ProductCategory $category) {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return \Tech506\Bundle\CallServiceBundle\Entity\ProductCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @return saleTypes
     */
    public function getSaleTypes() {
        return $this->saleTypes;
    }

    /**
     * @param saleTypes $saleTypes
     * @return Product
     */
    public function setSaleTypes($saleTypes) {
        $this->saleTypes = $saleTypes;
        return $this;
    }

    public function __toString(){
        return $this->name;
    }
}
?>
