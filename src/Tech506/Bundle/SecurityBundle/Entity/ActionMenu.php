<?php
namespace Tech506\Bundle\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 *
 * @ORM\Table(name="actions_menu")
 * @ORM\Entity()
 */
class ActionMenu
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $route;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cssClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(type="integer", name="reference")
     * @Assert\NotBlank()
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, name="additional_path", nullable=true)
     * @Assert\NotBlank()
     */
    private $additionalPath;

    /**
     * @ORM\Column(type="integer", name="sort_order")
     * @Assert\NotBlank()
     */
    private $sortOrder;

    /**
     * @ManyToOne(targetEntity="ActionMenu", inversedBy="childrens")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var childrens
     * @ORM\OneToMany(targetEntity="ActionMenu", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    private $childrens;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="menuOptions")
     */
    private $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    public function __toString() {
        return $this->label;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Set route
     *
     * @param string $route
     */
    public function setRoute($route) {
        $this->route = $route;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set reference
     *
     * @param integer $reference
     */
    public function setReference($reference) {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return integer 
     */
    public function getReference() {
        return $this->reference;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     */
    public function setSortOrder($sortOrder) {
        $this->sortOrder = $sortOrder;
    }

    /**
     * Get sortOrder
     *
     * @return integer
     */
    public function getSortOrder() {
        return $this->sortOrder;
    }

    /**
     * Set parent
     *
     * @param \Tech506\Bundle\SecurityBundle\Entity\ActionMenu $parent
     */
    public function setParent(\Tech506\Bundle\SecurityBundle\Entity\ActionMenu $parent) {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return \Tech506\Bundle\SecurityBundle\Entity\ActionMenu
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Set cssClass
     *
     * @param string $cssClass
     */
    public function setCssClass($cssClass) {
        $this->cssClass = $cssClass;
    }

    /**
     * Get additionalPath
     *
     * @return string
     */
    public function getAdditionalPath() {
        return $this->additionalPath;
    }

    /**
     * Set additionalPath
     *
     * @param string $additionalPath
     */
    public function setAdditionalPath($additionalPath) {
        $this->additionalPath = $additionalPath;
    }

    /**
     * Get cssClass
     *
     * @return string
     */
    public function getCssClass() {
        return $this->cssClass;
    }

    /**
     * @return mixed
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     * @return ActionMenu
     */
    public function setRoles($roles) {
        $this->roles = $roles;
        return $this;
    }


    public function getChildrens() {
        return $this->childrens;
    }
}