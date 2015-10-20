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
 * @ORM\Table(name="permissions")
 * @ORM\Entity()
 */
class Permission {
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
     * @ORM\Column(type="integer", name="sort_order")
     * @Assert\NotBlank()
     */
    private $sortOrder;

    /**
     * @ManyToOne(targetEntity="Permission")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var childrens
     * @ORM\OneToMany(targetEntity="Permission", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    private $childrens;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="permissions")
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
     * @param \Tech506\Bundle\SecurityBundle\Entity\Permission $parent
     */
    public function setParent(\Tech506\Bundle\SecurityBundle\Entity\Permission $parent) {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return \Tech506\Bundle\AsiloBundle\SecurityBundle\Permission
     */
    public function getParent() {
        return $this->parent;
    }

    public function getChildrens() {
        return $this->childrens;
    }
}