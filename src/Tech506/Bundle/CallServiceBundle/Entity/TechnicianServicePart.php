<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\CallServiceBundle\Util\Enum\TechnicianServiceStatus;
use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="technician_service_parts")
 * @ORM\Entity()
 */
class TechnicianServicePart {

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
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $fullPrice;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $realCost;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $technicianCost;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $technicianCommision;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $utility;

    /**
     * @ManyToOne(targetEntity="TechnicianService")
     * @JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

    public function __construct() {
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
     * @param mixed $name
     * @return TechnicianServicePart
     */
    public function setName($name) {
        $this->name = $name;
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
     * @return TechnicianServicePart
     */
    public function setFullPrice($fullPrice) {
        $this->fullPrice = $fullPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRealCost() {
        return $this->realCost;
    }

    /**
     * @param mixed $realCost
     * @return TechnicianServicePart
     */
    public function setRealCost($realCost) {
        $this->realCost = $realCost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechnicianCost() {
        return $this->technicianCost;
    }

    /**
     * @param mixed $technicianCost
     * @return TechnicianServicePart
     */
    public function setTechnicianCost($technicianCost) {
        $this->technicianCost = $technicianCost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechnicianCommision() {
        return $this->technicianCommision;
    }

    /**
     * @param mixed $technicianCommision
     * @return TechnicianServicePart
     */
    public function setTechnicianCommision($technicianCommision) {
        $this->technicianCommision = $technicianCommision;
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
     * @return TechnicianServicePart
     */
    public function setUtility($utility) {
        $this->utility = $utility;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getService() {
        return $this->service;
    }

    /**
     * @param mixed $service
     * @return TechnicianServicePart
     */
    public function setService($service) {
        $this->service = $service;
        return $this;
    }


    public function __toString(){
        return $this->id;
    }
}
?>
