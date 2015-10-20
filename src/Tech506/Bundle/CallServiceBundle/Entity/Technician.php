<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;


/**
 * @ORM\Table(name="technician")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\CallServiceBundle\Repository\TechniciansRepository")
 */
class Technician extends Employee {
    /**
     * @ORM\Column(type="string", length=25)
     */
    private $shirtSize;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $pantsSize;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $shoesSize;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $motorcycleLicensePlate;

    /**
     * @ORM\Column(type="date", nullable = true)
     */
    private $lastUniformDate;

    public function __construct() {
        parent::__construct();
        $this->shirtSize = "";
        $this->pantsSize = "";
        $this->shoesSize = "";
        $this->motorcycleLicensePlate = "";
    }

    /**
     * @return mixed
     */
    public function getLastUniformDate() {
        return $this->lastUniformDate;
    }

    /**
     * @param mixed $lastUniformDate
     * @return User
     */
    public function setLastUniformDate($lastUniformDate) {
        $this->lastUniformDate = $lastUniformDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShirtSize() {
        return $this->shirtSize;
    }

    /**
     * @param mixed $shirtSize
     * @return Technician
     */
    public function setShirtSize($shirtSize) {
        $this->shirtSize = $shirtSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPantsSize() {
        return $this->pantsSize;
    }

    /**
     * @param mixed $pantsSize
     * @return Technician
     */
    public function setPantsSize($pantsSize) {
        $this->pantsSize = $pantsSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShoesSize() {
        return $this->shoesSize;
    }

    /**
     * @param mixed $shoesSize
     * @return Technician
     */
    public function setShoesSize($shoesSize) {
        $this->shoesSize = $shoesSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMotorcycleLicensePlate() {
        return $this->motorcycleLicensePlate;
    }

    /**
     * @param mixed $motorcycleLicensePlate
     * @return Technician
     */
    public function setMotorcycleLicensePlate($motorcycleLicensePlate) {
        $this->motorcycleLicensePlate = $motorcycleLicensePlate;
        return $this;
    }

    public function __toString() {
        return "Technician [" . $this->id . "]";
    }
}
?>
