<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="product_categories")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\SecurityBundle\Repository\GenericRepository")
 */
class ProductCategory {

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
     * @var products
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category", cascade={"all"})
     */
    private $products;

    public function __construct() {
        $this->products = new ArrayCollection();
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

    /**
     * @return products
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * @param products $products
     * @return Product
     */
    public function setProducts($products) {
        $this->products = $products;
        return $this;
    }

    public function __toString(){
        return $this->id;
    }
}
?>
